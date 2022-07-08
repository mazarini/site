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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/menu')]
class MenuAdminController extends AbstractController
{
    #[Route('/', name: 'app_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository, int $id = 0): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MenuRepository $menuRepository): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuRepository->add($menu, true);

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, MenuRepository $menuRepository): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menuRepository->add($menu, true);

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, MenuRepository $menuRepository): Response
    {
        $token = $request->request->get('_token');
        if (!\is_string($token)) {
            $token = null;
        }
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $token)) {
            $menuRepository->remove($menu, true);
        }

        return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
