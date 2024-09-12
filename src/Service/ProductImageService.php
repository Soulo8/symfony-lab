<?php

namespace App\Service;

use App\Entity\Product;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ProductImageService
{
    private $uploaderHelper;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getImagesData(Product $product): array
    {
        $index = 0;

        return $product->getImages()->map(function ($image) use (&$index) {
            return [
                'index' => $index++,
                'name' => $image->getImageName(),
                'url' => $this->uploaderHelper->asset($image, 'imageFile'),
            ];
        })->toArray();
    }
}
