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
        $data = $this->serializer->serialize($users, JsonEncoder::FORMAT);

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
}
