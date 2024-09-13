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
        $data = [];
        $images = $product->getImages();
        foreach ($images as $key => $image) {
            $data[] = [
                'index' => $key,
                'name' => $image->getImageName(),
                'url' => $this->uploaderHelper->asset($image, 'imageFile'),
            ];
        }

        return $data;
    }
}
