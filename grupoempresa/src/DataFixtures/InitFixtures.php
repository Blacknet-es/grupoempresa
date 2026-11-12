<?php

namespace App\DataFixtures;

use App\Entity\AppUser;
use App\Entity\MetodoPago;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $em)
    {
        // Creamos el usuario super admin
        $admin = new AppUser();
        $admin->setEmail('info@trendix.es')
            ->setEnabled(true)
            ->setName('Super')
            ->setLastName('Admin')
            ->setMobilePhone('+34600000000')
            ->setLocale('es')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPERADMIN'])
        ;

        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'Jesus090482_'));
        $em->persist($admin);
        $em->flush();

        // Creamos el usuario super admin2
        $admin = new AppUser();
        $admin->setEmail('info@grupoempresa.es')
            ->setEnabled(true)
            ->setName('Super')
            ->setLastName('Admin2')
            ->setMobilePhone('+34600000000')
            ->setLocale('es')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPERADMIN'])
        ;

        $admin->setPassword($this->passwordEncoder->encodePassword($admin, '123456'));
        $em->persist($admin);
        $em->flush();

        $banco = new MetodoPago();
        $banco->setNombre('Banco')
            ->setClave('BANCO')
            ->setPeriodo(12)
        ;
        $em->persist($banco);
        
        $ventanilla = new MetodoPago();
        $ventanilla->setNombre('ventanilla')
            ->setClave('VENTANILLA')
            ->setPeriodo(12)
        ;
        $em->persist($ventanilla);
        
        $otros = new MetodoPago();
        $otros->setNombre('otros')
            ->setClave('OTROS')
            ->setPeriodo(12)
        ;
        $em->persist($otros);

        $em->flush();
    }
}