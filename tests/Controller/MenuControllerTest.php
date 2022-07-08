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

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MenuRepository $repository;
    private Registry $registry;

    private string $path = '/admin/menu/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/login');
        self::assertResponseStatusCodeSame(200);
        $this->client->submitForm('Sign in', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $registry = static::getContainer()->get('doctrine');
        if (null !== $registry && is_a($registry, Registry::class)) {
            $this->registry = $registry;
        }
        $repository = $this->registry->getRepository(Menu::class);
        if (is_a($repository, MenuRepository::class)) {
            $this->repository = $repository;
        }

        foreach ($this->repository->findAll() as $object) {
            if ('root' !== $object->getLabel()) {
                $this->repository->remove($object, true);
            }
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = \count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'menu[url]' => 'Testing',
            'menu[label]' => 'Testing',
            'menu[weight]' => '0',
        ]);

        self::assertResponseRedirects('/admin/menu/');

        self::assertSame($originalNumObjectsInRepository + 1, \count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $fixture = new Menu();
        $fixture->setUrl('My Title');
        $fixture->setLabel('My Title');
        $fixture->setWeight(0);

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Menu');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixture = new Menu();
        $fixture->setUrl('My Title');
        $fixture->setLabel('My Title');
        $fixture->setWeight(0);

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'menu[url]' => 'Something New',
            'menu[label]' => 'Something New',
            'menu[weight]' => '0',
        ]);

        self::assertResponseRedirects('/admin/menu/');

        $fixture = $this->repository->find($fixture->getId());

        self::assertSame('Something New', $fixture->getUrl());
        self::assertSame('Something New', $fixture->getLabel());
        self::assertSame(0, $fixture->getWeight());
    }

    public function testRemove(): void
    {
        $originalNumObjectsInRepository = \count($this->repository->findAll());

        $fixture = new Menu();
        $fixture->setUrl('My Title');
        $fixture->setLabel('My Title');
        $fixture->setWeight(0);

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, \count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, \count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/menu/');
    }
}
