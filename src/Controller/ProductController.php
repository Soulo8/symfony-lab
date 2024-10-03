<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductImageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, ['validation_groups' => ['create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $request->files->get('product')['newImages'];
            foreach ($images as $image) {
                $productImage = new ProductImage();
                $productImage->setImageFile($image);

                $errors = $validator->validate($productImage);

                if (count($errors) > 0) {
                    $this->addFlash('error', "L'un des fichiers n'est pas une image.");

                    return $this->render('product/new.html.twig', [
                        'product' => $product,
                        'form' => $form,
                    ], new Response(null, 422));
                }

                $product->addImage($productImage);
            }

            if (0 === $product->getImages()->count()) {
                $this->addFlash('error', "Vous n'avez pas ajouté d'image.");

                return $this->render('product/new.html.twig', [
                    'product' => $product,
                    'form' => $form,
                ], new Response(null, 422));
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'PUT'])]
    public function edit(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ProductImageService $productImageService,
    ): Response {
        $originalImages = new ArrayCollection();
        foreach ($product->getImages() as $image) {
            $originalImages->add($image);
        }

        $productImageService->updatePosition($request, $product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            foreach ($originalImages as $image) {
                if (false === $product->getImages()->contains($image)) {
                    $entityManager->remove($image);
                }
            }

            if ($form->isValid()) {
                $images = $request->files->get('product')['newImages'];
                foreach ($images as $image) {
                    $productImage = new ProductImage();
                    $productImage->setImageFile($image);

                    $errors = $validator->validate($productImage);

                    if (count($errors) > 0) {
                        $this->addFlash('error', "L'un des fichiers n'est pas une image.");

                        return $this->render('product/edit.html.twig', [
                            'product' => $product,
                            'form' => $form,
                            'images' => $productImageService->getImagesData($product),
                        ], new Response(null, 422));
                    }

                    $product->addImage($productImage);
                }

                if (0 === $product->getImages()->count()) {
                    $this->addFlash('error', "Vous n'avez pas ajouté d'image.");

                    return $this->render('product/edit.html.twig', [
                        'product' => $product,
                        'form' => $form,
                        'images' => $productImageService->getImagesData($product),
                    ], new Response(null, 422));
                }

                $entityManager->flush();

                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'images' => $productImageService->getImagesData($product),
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/download-image/{id}', name: 'app_product_image', methods: ['GET'])]
    public function downloadImageAction(Product $product, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($product, $fileField = 'imageFile');
    }
}
