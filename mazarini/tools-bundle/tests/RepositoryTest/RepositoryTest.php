<?php

namespace App\Tests\Repository;

use App\Entity\Entity;
use App\Repository\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityRepositoryTest extends KernelTestCase
{
    protected static EntityRepository $repository;
    protected int $id = 0;

    public static function setUpBeforeClass(): void
    {
    }

    protected function setUp(): void
    {
        static::setRepository();
        $this->tearDown();

        $entity = new Entity();
        static::$repository->save($entity)->flush();
        $this->id = $entity->getId();
    }

    public function testFindById(): void
    {
        $entity = static::$repository->findOneById($this->id);
        $this->assertNotNull($entity);
        $this->assertSame($this->id, $entity->getId());
    }

    public function testRemove(): void
    {
        $entity = static::$repository->findOneById($this->id);
        $this->assertNotNull($entity);
        static::$repository->remove($entity)->flush();
        $this->assertTrue($entity->isNew());
        $entity = static::$repository->findOneById($this->id);
        $this->assertNull($entity);
    }

    protected function tearDown(): void
    {
        foreach (static::$repository->findAll() as $object) {
            static::$repository->remove($object, true);
        }
    }

    public static function setRepository(): void
    {
        $registry = static::getContainer()->get('doctrine');
        if (null !== $registry && is_a($registry, Registry::class)) {
            $repository = $registry->getRepository(Entity::class);
            if (is_a($repository, EntityRepository::class)) {
                static::$repository = $repository;
            }
        }
    }
}
