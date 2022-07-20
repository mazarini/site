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

use App\Entity\Menu;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ObjectRepository<Menu>
 *
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ObjectRepository
{
    /**
     * @var array<int|string, Menu>
     */
    private array $menus = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);

        // Load all menus
        foreach ($this->findBy([], ['parent' => 'asc', 'weight' => 'asc']) as $menu) {
            $this->addMenu($menu);
        }

        // Create "main" is not
        if (0 === \count($this->menus)) {
            $menu = new menu();
            $menu->setSlug('main');
            $menu->setLabel('Main menu');
            $this->add($menu, true);
            $this->addMenu($menu);
        }

        $this->menus[0] = $this->menus['main'];
    }

    public function getbyId(int $id): ?Menu
    {
        if (isset($this->menus[$id])) {
            return $this->menus[$id];
        }

        return null;
    }

    public function getbySlug(string $slug): ?Menu
    {
        if (isset($this->menus[$slug])) {
            return $this->menus[$slug];
        }

        return null;
    }

    public function verifySlug(menu $menu): bool
    {
        switch (true) {
            case !isset($this->menus[$menu->getSlug()]):
                // New slug
                return true;
            case $this->menus[$menu->getSlug()]->getId() === $menu->getId():
                // Slug of current menu (not changed)
                return true;
        }

        return false;
    }

    private function addMenu(Menu $menu): void
    {
        $this->menus[$menu->getId()] = $menu;
        $this->menus[$menu->getSlug()] = $menu;
    }
}
