<?php

namespace Mazarini\ToolsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait EntityTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @var int|null
     */
    private ?int $id = null;

    /**
     * Get $id
     *
     * @return int
     */
    public function getId(): int
    {
        if ($this->id === null) {
            return 0;
        }
        return $this->id;
    }

    /**
     * Tell if an entity is to create
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }
}
