<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CarSearch;
use App\Entity\Car;
use App\Entity\CarImage;
use App\Form\CarSearchType;
use App\Form\CarType;
use App\Service\CarImageManager;
use App\Service\CarSearchManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use SlopeIt\BreadcrumbBundle\Attribute\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/car')]
#[Breadcrumb([
    'label' => 'home',
    'route' => 'app_home',
])]
final class CarController extends AbstractController
{
    private const int LIMIT_PER_PAGE = 4;

    #[Route('', name: 'app_car_index', methods: ['GET'])]
    #[Breadcrumb([
        ['label' => 'car.list'],
    ])]
    public function index(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        CarSearchManager $carSearchManager,
        Request $request,
    ): Response {
        $qb = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Car::class, 'c');
        $query = $carSearchManager
            ->addFilters($qb, $request)
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE,
            [
                'defaultSortFieldName' => 'c.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );

        $formSearch = $this->createForm(
            CarSearchType::class,
            new CarSearch()
        );
        $formSearch->handleRequest($request);

        return $this->render('car/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $formSearch,
        ]);
    }

    #[Route('/new', name: 'app_car_new', methods: ['GET', 'POST'])]
    #[Breadcrumb([
        ['label' => 'car.list', 'route' => 'app_car_index'],
        ['label' => 'car.new'],
    ])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ValidatorInterface $validator,
    ): Response {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['car'];
            if (array_key_exists('images', $data)) {
                $images = $data['images'];

                foreach ($images as $imageJson) {
                    $data = json_decode($imageJson, true);
                    $tempPath = sprintf(
                        '%s/%s',
                        sys_get_temp_dir(),
                        $data['name']
                    );

                    file_put_contents($tempPath, base64_decode($data['data']));

                    $image = new UploadedFile(
                        $tempPath,
                        $data['name'],
                        $data['type'],
                        null
                    );

                    $carImage = new CarImage();
                    $carImage->setImageFile($image);

                    $errors = $validator->validate($carImage);

                    if (count($errors) > 0) {
                        $this->addFlash(
                            'error',
                            $translator->trans(
                                'one_of_the_files_is_not_an_image'
                            )
                        );

                        return $this->render('car/new.html.twig', [
                            'car' => $car,
                            'form' => $form,
                        ], new Response(null, 422));
                    }

                    $car->addImage($carImage);
                }
            }

            $entityManager->persist($car);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('record.added'));

            return $this->redirectToRoute(
                'app_car_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_car_edit', methods: ['GET', 'PUT'])]
    #[Breadcrumb([
        ['label' => 'car.list', 'route' => 'app_car_index'],
        ['label' => 'car.edit'],
    ])]
    public function edit(
        Request $request,
        Car $car,
        EntityManagerInterface $entityManager,
        CarImageManager $carImageManager,
    ): Response {
        $carImageManager->updatePosition($request, $car);

        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_car_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form,
            'images' => $carImageManager->getData($car),
            'urlProcess' => $this->generateUrl(
                'app_car_image_process',
                ['car' => $car->getId()]
            ),
        ]);
    }

    #[Route('/{id}', name: 'app_car_delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        Car $car,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete'.$car->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($car);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_car_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
