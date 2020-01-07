<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Box;
use App\Entity\Ean;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Rest\Route("/api")
 */
final class ApiController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/users", name="findAllUsers")
     */
    public function findAllUsersAction(): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($users, JsonEncoder::FORMAT, [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/ean/{ean}", name="findEanProductInfo")
     */
    public function findEanProduct($ean): JsonResponse
    {
        $productInfo = $this->em->getRepository(Ean::class)->findOneBy(array('ean' => $ean));
        $data = $this->serializer->serialize($productInfo, JsonEncoder::FORMAT, [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/{uuid}/boxes", name="findAllBoxes")
     */
    public function findAllBoxesAction($uuid): JsonResponse
    {
        $boxes = $this->em->getRepository(Box::class)->findBy(['user' => $uuid], ['id' => 'DESC']);

        $data = $this->serializer->serialize($boxes, JsonEncoder::FORMAT, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'products'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/{boxId}/products", name="findAllProducts")
     */
    public function findAllProductsAction($boxId): JsonResponse
    {
        $products = $this->em->getRepository(Products::class)->findBy(['box' => $boxId], ['expires' => 'ASC']);
        $data = $this->serializer->serialize($products, JsonEncoder::FORMAT, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['box'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @throws BadRequestHttpException
     * @throws \Exception
     *
     * @Rest\Post("/add/product", name="createProduct")
     */
    public function createProductAction(Request $request): JsonResponse
    {
        $box = $this->em->getRepository(Box::class)->findOneBy(array('id' => $request->request->get('box')));
        $products = $request->request->get('products');

        foreach ($products as $product) {
            $name = $product['name'];
            $desc = $product['desc'];
            $amount = $product['amount'];
            $expires = $product['expires'];
            $unit = $product['unit'];
            $category = $product['category'];

            if (empty($name) || empty($amount) || empty($expires)) {
                throw new BadRequestHttpException('Can\'t find name, amount or expires in the body');
            }

            $productEntity = new Products();
            $productEntity->setName($name);
            $productEntity->setDescription($desc);
            $productEntity->setAmount($amount);
            $productEntity->setBox($box);
            $productEntity->setExpires(new \DateTime($expires));
            $productEntity->setUnit($unit);
            $productEntity->setCategory($category);
            $this->em->persist($productEntity);
        }

        $this->em->flush();
        $data = $this->serializer->serialize($box, JsonEncoder::FORMAT, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * @throws BadRequestHttpException
     * @throws \Exception
     *
     * @Rest\Delete("/remove/product/{id}", name="deleteProduct")
     */
    public function deleteProductAction(Request $request, $id): JsonResponse
    {
        $product = $this->em->getRepository(Products::class)->findOneBy(array('id' => $id));
        if (empty($product)) {
            throw new BadRequestHttpException(sprintf('Can\'t find product with id %s.', $id));
        }
        $this->em->remove($product);
        $this->em->flush();

        return new JsonResponse('Product deleted succesfully', Response::HTTP_NO_CONTENT, [], true);
    }

    /**
     *
     * @throws BadRequestHttpException
     * @throws \Exception
     *
     * @Rest\Put("/update/{id}", name="updateProduct")
     */
    public function updateProductAction(Request $request, $id): JsonResponse
    {
        $product = $this->em->getRepository(Products::class)->findOneBy(array('id' => $id));
        $amount = $request->request->get('amount');
        if (empty($amount) || $amount < 1) {
            throw new BadRequestHttpException('Amount is missing or invalid');
        }
        $product->setAmount($amount);
        $this->em->persist($product);
        $this->em->flush();

        $data = $this->serializer->serialize($product, JsonEncoder::FORMAT, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['box'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
