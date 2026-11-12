<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ImportDatabaseBackupCommand extends Command
{
    protected static $defaultName = 'app:database-backup:import';

    private const IMPORTS_TABLE = 'grupoempresa_deployment_backups';
    private const LOCK_NAME = 'grupoempresa_database_backup_import';
    private const SANDBOX_DIRECTIVE = '/*M!999999\\- enable the sandbox mode */';

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }

    protected function configure()
    {
        $backupPath = getenv('DB_BACKUP_PATH') ?: '/dbbackup/backup.sql';

        $this
            ->setDescription('Import the configured SQL backup once.')
            ->addOption(
                'backup',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to the SQL backup file.',
                $backupPath
            )
            ->addOption(
                'allow-non-empty',
                null,
                InputOption::VALUE_NONE,
                'Allow importing into a database that already contains tables.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $backupPath = $input->getOption('backup');

        if (!file_exists($backupPath)) {
            $io->comment(sprintf('No database backup found at "%s"; skipping import.', $backupPath));

            return 0;
        }
        if (!is_file($backupPath) || !is_readable($backupPath)) {
            throw new RuntimeException(sprintf('Database backup is not a readable file: %s', $backupPath));
        }

        $backupSize = filesize($backupPath);
        if ($backupSize === false || $backupSize === 0) {
            throw new RuntimeException(sprintf('Database backup is empty or unreadable: %s', $backupPath));
        }

        $backupHash = hash_file('sha256', $backupPath);
        if ($backupHash === false) {
            throw new RuntimeException(sprintf('Unable to calculate SHA-256 for %s.', $backupPath));
        }

        $lock = $this->connection->fetchColumn('SELECT GET_LOCK(?, 60)', [self::LOCK_NAME]);
        if ((int) $lock !== 1) {
            throw new RuntimeException('Could not acquire the database backup import lock.');
        }

        try {
            $this->connection->exec(
                'CREATE TABLE IF NOT EXISTS ' . self::IMPORTS_TABLE . ' (
                    sha256 CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
                    imported_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (sha256)
                ) ENGINE=InnoDB'
            );

            if ($this->wasAlreadyImported($backupHash)) {
                $io->comment(sprintf('Database backup %s was already imported; skipping.', $backupHash));

                return 0;
            }

            if (!$this->mayImportIntoNonEmptyDatabase($input)) {
                $tableCount = (int) $this->connection->fetchColumn(
                    'SELECT COUNT(*) FROM information_schema.tables
                     WHERE table_schema = DATABASE() AND table_name <> ?',
                    [self::IMPORTS_TABLE]
                );
                if ($tableCount > 0) {
                    throw new RuntimeException(
                        'The target database is not empty. Refusing to import the backup; '
                        . 'use --allow-non-empty or set DB_BACKUP_IMPORT_ALLOW_NONEMPTY=1 '
                        . 'only if modifying this database is intentional.'
                    );
                }
            }

            $this->runImport($backupPath);

            $this->connection->insert(self::IMPORTS_TABLE, ['sha256' => $backupHash]);
            $io->success(sprintf('Database backup %s imported and marked as processed.', $backupHash));

            return 0;
        } finally {
            $this->connection->fetchColumn('SELECT RELEASE_LOCK(?)', [self::LOCK_NAME]);
        }
    }

    private function wasAlreadyImported(string $backupHash): bool
    {
        return (bool) $this->connection->fetchColumn(
            'SELECT 1 FROM ' . self::IMPORTS_TABLE . ' WHERE sha256 = ?',
            [$backupHash]
        );
    }

    private function mayImportIntoNonEmptyDatabase(InputInterface $input): bool
    {
        $fromEnvironment = filter_var(
            getenv('DB_BACKUP_IMPORT_ALLOW_NONEMPTY') ?: '0',
            FILTER_VALIDATE_BOOLEAN
        );

        return $input->getOption('allow-non-empty') || $fromEnvironment;
    }

    private function runImport(string $backupPath): void
    {
        $params = $this->connection->getParams();
        $host = $params['host'] ?? 'localhost';
        $database = $params['dbname'] ?? '';
        $user = $params['user'] ?? '';

        if ($database === '' || !preg_match('/^[A-Za-z0-9_$-]+$/', $database)) {
            throw new RuntimeException('The configured database name is missing or unsupported.');
        }
        if ($user === '') {
            throw new RuntimeException('The configured database user is missing.');
        }

        $command = ['mariadb'];
        if (!empty($params['unix_socket'])) {
            $command[] = '--socket=' . $params['unix_socket'];
        } else {
            $command[] = '--protocol=tcp';
            $command[] = '--host=' . trim($host, '[]');
            $command[] = '--port=' . ($params['port'] ?? 3306);
        }
        $command[] = '--user=' . $user;
        $command[] = '--database=' . $database;
        $command[] = '--default-character-set=utf8mb4';

        $input = fopen($backupPath, 'rb');
        if ($input === false) {
            throw new RuntimeException(sprintf('Unable to open database backup: %s', $backupPath));
        }

        try {
            $firstLine = fgets($input);
            if ($firstLine === false || trim($firstLine) !== self::SANDBOX_DIRECTIVE) {
                rewind($input);
            }

            $process = new Process(
                $command,
                null,
                ['MYSQL_PWD' => $params['password'] ?? ''],
                $input,
                null
            );
            $process->run();
        } finally {
            fclose($input);
        }

        if (!$process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput());
            throw new RuntimeException(
                sprintf(
                    'MariaDB backup import failed with exit code %d%s',
                    $process->getExitCode(),
                    $errorOutput === '' ? '.' : ': ' . $errorOutput
                )
            );
        }
    }
}
