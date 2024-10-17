<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarImage;
use App\Repository\CarImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/car-image')]
final class CarImageController extends AbstractController
{
    #[Route(
        '/download/{id}',
        name: 'app_car_image_download',
        methods: ['GET']
    )]
    public function download(
        CarImage $carImage,
        DownloadHandler $downloadHandler,
    ): Response {
        return $downloadHandler->downloadObject(
            $carImage,
            $fileField = 'imageFile'
        );
    }

    #[Route(
        '/process/car/{car}',
        name: 'app_car_image_process',
        methods: ['POST']
    )]
    public function process(
        Request $request,
        Car $car,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
    ): JsonResponse {
        $image = $request->files->get('car')['images'][0];

        $carImage = new CarImage();
        $carImage->setImageFile($image);

        $errors = $validator->validate($carImage);

        if (count($errors) > 0) {
            return $this->json([
                'error' => $translator->trans(
                    'one_of_the_files_is_not_an_image'
                ),
            ], 422);
        }

        $car->addImage($carImage);

        $entityManager->flush();

        return $this->json($carImage->getId());
    }

    #[Route('/revert', name: 'app_car_image_revert', methods: ['DELETE'])]
    public function revert(
        Request $request,
        CarImageRepository $carImageRepository,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
    ): JsonResponse {
        $id = json_decode($request->getContent(), true);
        $carImage = $carImageRepository->find($id);

        $entityManager->remove($carImage);
        $entityManager->flush();

        return $this->json(['success' => $translator->trans('file.deleted')]);
    }

    #[Route('/{id}/remove', name: 'app_car_image_remove', methods: ['DELETE'])]
    public function remove(
        CarImage $carImage,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
    ): JsonResponse {
        $entityManager->remove($carImage);
        $entityManager->flush();

        return $this->json(['success' => $translator->trans('file.deleted')]);
    }
}
