<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Trendix\AdminBundle\EventListener\DiscriminatorEntry;


/**
 * Class User
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\DiscriminatorColumn(name = "_user_type", type = "string")
 * @DiscriminatorEntry(value = "app_user")
 */
class AppUser extends \Trendix\AdminBundle\Entity\User
{

    public function __construct()
    {
        parent::__construct();
    }
}