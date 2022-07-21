<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MenuExtension extends AbstractExtension
{
    private MenuRepository $menuRepository;

    function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }
    public function getFilters(): array
    {
        return [
            // new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMenu', [$this, 'getMenu']),
        ];
    }

    public function getMenu(string $slug = 'main'): Collection
    {
        $menu = $this->menuRepository->getBySlug($slug);
        if ($menu === null) {
            $col = new ArrayCollection(['A', 'B']);
            $col->add('C');
        } else {
            return $menu->getChilds();
        }
    }
}
