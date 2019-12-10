<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Box;
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
     * @Rest\Get("/boxes", name="findAllBoxes")
     */
    public function findAllBoxesAction(): JsonResponse
    {
        $boxes = $this->em->getRepository(Box::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($boxes, JsonEncoder::FORMAT, [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/products", name="findAllProducts")
     */
    public function findAllProductsAction(): JsonResponse
    {
        $products = $this->em->getRepository(Products::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($products, JsonEncoder::FORMAT, [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
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
        $name = $request->request->get('name');
        $desc = $request->request->get('desc');
        $amount = $request->request->get('amount');
        $expires = $request->request->get('expires');
        $unit = $request->request->get('unit');

        if (empty($name) || empty($amount) || empty($expires)) {
            throw new BadRequestHttpException('Can\'t find name, amount or expires in the body');
        }

        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => 'admin@admin.com'));
        $box = $user->getBox()[1];

        $productEntity = new Products();
        $productEntity->setName($name);
        $productEntity->setDescription($desc);
        $productEntity->setAmount($amount);
        $productEntity->setBox($box);
        $productEntity->setExpires(new \DateTime($expires));
        $productEntity->setUnit($unit);
        $this->em->persist($productEntity);
        $this->em->flush();
        $data = $this->serializer->serialize($productEntity, JsonEncoder::FORMAT, [
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
}
