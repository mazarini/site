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

namespace App\Test\Controller;

use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PageRepository $repository;
    private string $path = '/';
    private Registry $registry;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $registry = static::getContainer()->get('doctrine');
        if (null !== $registry && is_a($registry, Registry::class)) {
            $this->registry = $registry;
        }
        $repository = $this->registry->getRepository(Page::class);
        if (is_a($repository, PageRepository::class)) {
            $this->repository = $repository;
        }
        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testRender(): void
    {
        // $this->markTestIncomplete();
        $fixture = new Page();
        $fixture->setTitle('Page Title');
        $fixture->setSlug('my-slug');
        $fixture->setHeadTitle('Head-Title');
        $fixture->setDescription('Description');
        $fixture->setContent('Content');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getSlug()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Head-Title');
    }
}
