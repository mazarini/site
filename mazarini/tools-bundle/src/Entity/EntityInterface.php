<?php

namespace Mazarini\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

interface EntityInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return bool
     */
    public function isNew(): bool;
}
