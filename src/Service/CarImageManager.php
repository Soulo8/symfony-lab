<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Car;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class CarImageManager
{
    public function __construct(
        private KernelInterface $kernel,
        private RouterInterface $router,
        private UploaderHelper $uploaderHelper,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function getData(Car $car): array
    {
        $data = ['files' => []];
        $images = $car->getImages();
        foreach ($images as $image) {
            $url = $this->router->generate(
                'app_car_image_download',
                ['id' => $image->getId()]
            );
            $filename = $this->uploaderHelper->asset($image, 'imageFile');

            $data['files'][] = [
                'source' => $image->getId(),
                'options' => [
                    'type' => 'local',
                    'file' => [
                        'name' => $image->getImageName(),
                        'size' => $image->getImageSize(),
                        'type' => mime_content_type(sprintf(
                            '%s/%s',
                            $this->kernel->getProjectDir(),
                            $filename
                        )),
                    ],
                    'metadata' => [
                        'poster' => $url,
                    ],
                ],
            ];
        }

        return $data;
    }

    public function updatePosition(Request $request, Car $car): void
    {
        if ($request->request->has('car')) {
            $data = $request->request->all()['car'];
            if (array_key_exists('images', $data)) {
                $positions = array_flip($data['images']);
                $images = $car->getImages();
                foreach ($images as $image) {
                    $image->setPosition($positions[$image->getId()]);
                }
                $car->sortImagesByPosition();
            }
        }
    }
}
