<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Entity;
use ReflectionClass;

class EntityTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsNew(): void
    {
        $entity = new Entity();
        $this->assertSame(0, $entity->getId());
        $this->assertTrue($entity->isNew());
        $reflectionClass = new ReflectionClass(Entity::class);
        $reflectionClass->getProperty('id')->setValue($entity, 1);
        $this->assertSame(1, $entity->getId());
        $this->assertFalse($entity->isNew());
    }
}
