<?php

namespace App\DataFixtures;

use App\Entity\CuentaBancaria;
use App\Entity\Socio;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Trendix\AdminBundle\Utility\Dates;

class ZimportFixtures extends Fixture
{
    /**
     * @var EntityManager
     */
    private $em;

    public function load(ObjectManager $em)
    {
        $this->em = $em;
        $excel = IOFactory::load(__DIR__.'/socios.xlsx');

        $row = $excel->getActiveSheet()->removeRow(1);
        $datosHoja = $excel->getActiveSheet()->toArray(null, true, true, true);

        foreach ($datosHoja as $s) {
            $socio = new Socio();
            $socio
                ->setNumSocio($s['A'])
                ->setApellidos($s['C'])
                ->setNombre($s['D'])
                ->setDni(str_replace(['.', '-'], ['',''],$s['E']))
                ->setDomicilio($s['F'] ?: '--')
                ->setTelefono($s['G'])
                ->setTelefono2($s['H'])
                ->setEmail($s['K'] ?: '--')
                ->setFechaNacimiento(\DateTime::createFromFormat('d/m/Y', $s['L']) ?: null)
                ->setFechaAlta(\DateTime::createFromFormat('d/m/Y', $s['M']) ?: null)
                ->setFechaBaja(\DateTime::createFromFormat('d/m/Y', $s['N']) ?: null)
                ->setCuentaBancaria($this->importarCuentaBancaria($s))
                ->setComentarios($s['T'])
                ->setCodigoPostal($s['AB'] ?: '--')
                ->setCuota($s['AC'])
                ->setDifusion($s['AD'] == 'Si Acepta')
                ->setCiudad($s['AH'] ?: '--')
                ->setProvincia($s['AK'])
                ->setActivo(empty($s['N']))
                ->setMetodoPago($this->importarMetodoPago($s['AI']))
            ;
            $em->persist($socio);
            $em->flush();
        }
    }

    private function importarCuentaBancaria($s)
    {
        $iban = $s['P'];
        $numeracion = str_replace('_', '', $s['O']);

        return $iban.$numeracion;
    }

    private function importarMetodoPago($metodo)
    {
        $mp = $this->em->getRepository('App:MetodoPago')->findOneBy(['clave' => $metodo]);
        if (null === $mp) {
            $mp = $this->em->getRepository('App:MetodoPago')->findOneBy(['clave' => 'OTROS']);
        }

        return $mp;
    }
}