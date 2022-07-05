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

class PageAdminControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Registry $registry;
    private PageRepository $repository;
    private string $path = '/admin/page/';

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

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Page index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = \count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'page[title]' => 'Testing',
            'page[slug]' => 'Testing',
            'page[headTitle]' => 'Testing',
            'page[description]' => 'Testing',
            'page[content]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/page/');

        self::assertSame($originalNumObjectsInRepository + 1, \count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $fixture = new Page();
        $fixture->setTitle('My Title');
        $fixture->setSlug('My Title');
        $fixture->setHeadTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setContent('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getSlug()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Page');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixture = new Page();
        $fixture->setTitle('My Title');
        $fixture->setSlug('My Title');
        $fixture->setHeadTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setContent('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getSlug()));

        $this->client->submitForm('Update', [
            'page[title]' => 'Something New',
            'page[slug]' => 'Something New',
            'page[headTitle]' => 'Something New',
            'page[description]' => 'Something New',
            'page[content]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/page/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getHeadTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getContent());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = \count($this->repository->findAll());

        $fixture = new Page();
        $fixture->setTitle('My Title');
        $fixture->setSlug('My Title');
        $fixture->setHeadTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setContent('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, \count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getslug()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, \count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/page/');
    }
}
