<?php

declare(strict_types=1);

function databaseConfig(string $databaseUrl): array
{
    $parts = parse_url($databaseUrl);
    if ($parts === false || !in_array($parts['scheme'] ?? '', ['mysql', 'mariadb'], true)) {
        throw new RuntimeException('DATABASE_URL must use the mysql or mariadb scheme.');
    }

    $host = $parts['host'] ?? '';
    $database = rawurldecode(ltrim($parts['path'] ?? '', '/'));
    if ($host === '' || !isset($parts['user']) || $database === '') {
        throw new RuntimeException('DATABASE_URL must include a host, user, and database name.');
    }
    if (!preg_match('/^[A-Za-z0-9_$-]+$/', $database)) {
        throw new RuntimeException('The database name in DATABASE_URL contains unsupported characters.');
    }

    return [
        'host' => trim($host, '[]'),
        'port' => $parts['port'] ?? 3306,
        'user' => rawurldecode($parts['user']),
        'password' => rawurldecode($parts['pass'] ?? ''),
        'database' => $database,
    ];
}

function importBackup(): void
{
    $backupPath = getenv('DB_BACKUP_PATH') ?: '/dbbackup/backup.sql';
    if (!file_exists($backupPath)) {
        fwrite(STDOUT, "No database backup found at {$backupPath}; skipping import.\n");
        return;
    }
    if (!is_file($backupPath) || !is_readable($backupPath)) {
        throw new RuntimeException("Database backup is not a readable file: {$backupPath}");
    }

    $backupSize = filesize($backupPath);
    if ($backupSize === false || $backupSize === 0) {
        throw new RuntimeException("Database backup is not a readable, non-empty file: {$backupPath}");
    }

    $databaseUrl = getenv('DATABASE_URL');
    if ($databaseUrl === false || $databaseUrl === '') {
        throw new RuntimeException('DATABASE_URL is required when a database backup is mounted.');
    }

    $backupHash = hash_file('sha256', $backupPath);
    if ($backupHash === false) {
        throw new RuntimeException("Unable to calculate SHA-256 for {$backupPath}.");
    }

    $config = databaseConfig($databaseUrl);
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        ),
        $config['user'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10,
        ]
    );

    $lockName = 'grupoempresa_database_backup_import';
    $lock = $pdo->prepare('SELECT GET_LOCK(?, 60)');
    $lock->execute([$lockName]);
    if ((int) $lock->fetchColumn() !== 1) {
        throw new RuntimeException('Could not acquire the database backup import lock.');
    }

    try {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS grupoempresa_deployment_backups (
                sha256 CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
                imported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (sha256)
            ) ENGINE=InnoDB'
        );

        $processed = $pdo->prepare(
            'SELECT 1 FROM grupoempresa_deployment_backups WHERE sha256 = ?'
        );
        $processed->execute([$backupHash]);
        if ($processed->fetchColumn()) {
            fwrite(STDOUT, "Database backup {$backupHash} was already imported; skipping.\n");
            return;
        }

        $allowNonEmpty = filter_var(
            getenv('DB_BACKUP_IMPORT_ALLOW_NONEMPTY') ?: '0',
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$allowNonEmpty) {
            $tables = (int) $pdo->query(
                "SELECT COUNT(*) FROM information_schema.tables
                 WHERE table_schema = DATABASE()
                   AND table_name <> 'grupoempresa_deployment_backups'"
            )->fetchColumn();
            if ($tables > 0) {
                throw new RuntimeException(
                    'The target database is not empty. Refusing to import the backup; '
                    . 'set DB_BACKUP_IMPORT_ALLOW_NONEMPTY=1 only if replacing/importing '
                    . 'into this database is intentional.'
                );
            }
        }

        $environment = getenv();
        if (!is_array($environment)) {
            $environment = [];
        }
        $environment['MYSQL_PWD'] = $config['password'];
        $command = [
            'mariadb',
            '--protocol=tcp',
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['user'],
            '--database=' . $config['database'],
            '--default-character-set=utf8mb4',
        ];
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            '/var/www/html',
            $environment
        );
        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start the MariaDB client.');
        }

        $input = fopen($backupPath, 'rb');
        if ($input === false) {
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            throw new RuntimeException("Unable to open database backup: {$backupPath}");
        }

        $firstLine = fgets($input);
        // MariaDB 10.6 dump metadata is a client command unsupported by older clients.
        if ($firstLine === false || trim($firstLine) !== '/*M!999999\\- enable the sandbox mode */') {
            rewind($input);
        }
        $copiedBytes = stream_copy_to_stream($input, $pipes[0]);
        fclose($input);
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($copiedBytes === false || $exitCode !== 0) {
            throw new RuntimeException(
                'MariaDB backup import failed with exit code ' . $exitCode
                . ($stderr === false || trim($stderr) === '' ? '.' : ': ' . trim($stderr))
            );
        }

        $record = $pdo->prepare(
            'INSERT INTO grupoempresa_deployment_backups (sha256) VALUES (?)'
        );
        $record->execute([$backupHash]);
        fwrite(STDOUT, "Database backup {$backupHash} imported and marked as processed.\n");
    } finally {
        $release = $pdo->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);
    }
}

try {
    importBackup();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Database backup import failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
