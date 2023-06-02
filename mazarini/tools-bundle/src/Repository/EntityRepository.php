<?php

namespace Mazarini\ToolsBundle\Repository;

use App\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entity>
 *
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $class)
    {
        parent::__construct($registry, $class);
        return $this;
    }

    public function save(Entity $entity): self
    {
        $this->getEntityManager()->persist($entity);
        return $this;
    }

    public function remove(Entity $entity): self
    {
        $this->getEntityManager()->remove($entity);
        return $this;
    }

    public function flush(): self
    {
        $this->getEntityManager()->flush();
        return $this;
    }
}
