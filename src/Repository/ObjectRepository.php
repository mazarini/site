<?php

/*
 * This file is part of mazarini/site.
 *
 * mazarini/site is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * mazarini/site is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with mazarini/site. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 * @template-extends ServiceEntityRepository<T>
 */
abstract class ObjectRepository extends ServiceEntityRepository
{
    public function add(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        $this->flush($flush);
    }

    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        $this->flush($flush);
    }

    public function flush(bool $flush = true): void
    {
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
