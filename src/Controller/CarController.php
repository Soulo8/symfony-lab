<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Service\CarSearchManagement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use SlopeIt\BreadcrumbBundle\Attribute\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        CarSearchManagement $carSearchManagement,
        Request $request,
    ): Response {
        $qb = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Car::class, 'c');
        $query = $carSearchManagement
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

        $formSearch = $carSearchManagement->buildForm();
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
    ): Response {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($car);
            $entityManager->flush();

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
    ): Response {
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
