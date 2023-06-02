<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Mazarini\ToolsBundle\Entity\EntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity
{
    use EntityTrait;
}
