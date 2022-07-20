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

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/menu')]
class MenuAdminController extends AbstractController
{
    #[
        Route('/', name: 'app_menu', methods: ['GET']),
        Route('/index.html', name: 'app_menu_index', methods: ['GET']),
    ]
    public function index(MenuRepository $menuRepository): Response
    {
        $root = $menuRepository->getById(0);
        if (null === $root) {
            throw new \LogicException('No "main" menu');
        }

        return $this->redirectToRoute('app_menu_show', ['id' => $root->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/new.html', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuRepository $menuRepository, int $id): Response
    {
        $root = $menuRepository->getById($id);
        if (null === $root) {
            $this->addFlash('warning', 'Parent inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        $menu = new Menu();
        $root->addChild($menu);
        $menu->setWeight($root->getChilds()->count());

        return $this->update($request, $menuRepository, $root, $menu);
    }

    #[Route('/{id}/edit.html', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MenuRepository $menuRepository, int $id): Response
    {
        $menu = $menuRepository->getById($id);
        if (null === $menu) {
            $this->addFlash('warning', 'Menu inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        return $this->update($request, $menuRepository, $menu, $menu);
    }

    #[Route('/{id}/up.html', name: 'app_menu_up', methods: ['GET', 'POST'])]
    public function up(MenuRepository $menuRepository, int $id): Response
    {
        $menu = $menuRepository->getById($id);
        $parent = $menu->getParent();
        if (null === $menu) {
            $this->addFlash('warning', 'Menu inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        if (null === $parent) {
            $this->addFlash('warning', 'Parent inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        $weight = $menu->getWeight() - 1;

        foreach ($parent->getChilds() as $child) {
            if ($child->getWeight() === $weight) {
                $otherMenu = $child;
            }
        }

        $menu->setWeight($weight);
        $menuRepository->add($menu);
        $otherMenu->setWeight($weight + 1);
        $menuRepository->add($otherMenu);
        $menuRepository->flush();

        return $this->redirectToRoute('app_menu_show', ['id' => $parent->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/down.html', name: 'app_menu_down', methods: ['GET', 'POST'])]
    public function down(MenuRepository $menuRepository, int $id): Response
    {
        $menu = $menuRepository->getById($id);
        $parent = $menu->getParent();
        if (null === $menu) {
            $this->addFlash('warning', 'Menu inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        if (null === $parent) {
            $this->addFlash('warning', 'Parent inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        $weight = $menu->getWeight() + 1;

        foreach ($parent->getChilds() as $child) {
            if ($child->getWeight() === $weight) {
                $otherMenu = $child;
            }
        }

        $menu->setWeight($weight);
        $menuRepository->add($menu);
        $otherMenu->setWeight($weight - 1);
        $menuRepository->add($otherMenu);
        $menuRepository->flush();

        return $this->redirectToRoute('app_menu_show', ['id' => $parent->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/show.html', name: 'app_menu_show', methods: ['GET'])]
    public function show(MenuRepository $menuRepository, int $id): Response
    {
        $menu = $menuRepository->getById($id);
        if (null === $menu) {
            $this->addFlash('warning', 'Menu inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/delete.html', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, MenuRepository $menuRepository, EntityManagerInterface $manager, int $id): Response
    {
        $menu = $menuRepository->getById($id);
        if (null === $menu) {
            $this->addFlash('warning', 'Menu inconnu, retour à la racine');

            return $this->index($menuRepository);
        }

        $root = $menu->getParent();
        if (null === $root) {
            $this->addFlash('error', 'La racine ne peut pas être supprimée');

            return $this->index($menuRepository);
        }

        if (0 < $menu->getChilds()->count()) {
            $this->addFlash('error', 'Supprimer les enfants avant');

            return $this->redirectToRoute('app_menu_show', ['id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        $token = $request->request->get('_token');
        if (!\is_string($token)) {
            $token = null;
        }
        if (!$this->isCsrfTokenValid('delete'.$menu->getId(), $token)) {
            $this->addFlash('error', 'Erreur de token');

            return $this->redirectToRoute('app_menu_show', ['id' => $menu->getId()], Response::HTTP_SEE_OTHER);
        }

        $weight = $menu->getWeight();
        foreach ($menu->getChilds() as $child) {
            if ($weight < $child->getWeight()) {
                $child->setWeight($child->getWeight() - 1);
                $manager->persist($child);
            }
        }
        $menuRepository->remove($menu, true);

        $manager->flush();

        return $this->redirectToRoute('app_menu_show', ['id' => $root->getId()], Response::HTTP_SEE_OTHER);
    }

    private function update(Request $request, MenuRepository $menuRepository, Menu $root, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->control($menuRepository, $menu)) {
            $menuRepository->add($menu, true);

            return $this->redirectToRoute('app_menu_show', ['id' => $root->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/edit.html.twig', [
            'root' => $root,
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    private function control(MenuRepository $menuRepository, Menu $menu): bool
    {
        if (!$menuRepository->verifySlug($menu)) {
            $this->addFlash('error', sprintf('Le slug "%s" n\'est pas disponible', $menu->getSlug()));

            return false;
        }

        return true;
    }
}
