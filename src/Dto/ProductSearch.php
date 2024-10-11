<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Tag;

final class ProductSearch
{
    private ?string $name = null;
    private Tag|string|null $tag = null;
    private Tag|string|null $subTag = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTag(): Tag|string|null
    {
        return $this->tag;
    }

    public function setTag(Tag|string|null $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getSubTag(): Tag|string|null
    {
        return $this->subTag;
    }

    public function setSubTag(Tag|string|null $subTag): self
    {
        $this->subTag = $subTag;

        return $this;
    }
}
