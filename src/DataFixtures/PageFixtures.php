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

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadPages($manager);
    }

    private function loadPages(ObjectManager $manager): void
    {
        foreach ($this->getPageData() as [$title, $content]) {
            $page = new Page();
            $page->setSlug($title);
            $page->setHeadTitle($title);
            $page->setTitle($title);
            $page->setDescription($title);
            $page->setContent($content);

            $manager->persist($page);
        }

        $manager->flush();
    }

    /**
     * getPageData.
     *
     * @return array<int,array<int,string>>
     */
    private function getPageData(): array
    {
        return [
            // $pageData = [$title, $content];
            ['homepage', '<p>contenu de homepage</p>'],
        ];
    }
}
