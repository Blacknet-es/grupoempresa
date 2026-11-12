<?php

namespace App\Repository;

use App\Entity\Socio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Socio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Socio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Socio[]    findAll()
 * @method Socio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Socio::class);
    }

    public function getNewNumSocio()
    {
        $sql = "SELECT MAX(s.numSocio) as MAX FROM App:Socio s";

        return $this->_em->createQuery($sql)->getSingleScalarResult() + 1;
    }

    public function countActivos()
    {
        $sql = "SELECT COUNT(s.id) as TOTAL FROM App:Socio s WHERE s.activo = true";

        return $this->_em->createQuery($sql)->getSingleScalarResult();
    }

    public function findCiudades()
    {
        $sql = "SELECT DISTINCT(s.ciudad) as ciudad FROM App:Socio s WHERE s.activo = true ORDER BY s.ciudad ASC";

        return $this->_em->createQuery($sql)->getResult();
    }
}
