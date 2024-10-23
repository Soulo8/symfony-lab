<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RestoreTag extends AbstractController
{
    public function __invoke(Tag $tag): Tag
    {
        $tag->setDeletedAt(null);

        return $tag;
    }
}
