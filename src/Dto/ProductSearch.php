<?php

namespace App\Dto;

use App\Entity\Tag;

class ProductSearch
{
    public ?string $name = null;
    public Tag|string|null $tag = null;
    public Tag|string|null $subTag = null;
}
