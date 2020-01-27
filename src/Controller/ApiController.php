<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Box;
use App\Entity\Ean;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
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
     * @SWG\Response(
     *     response=200,
     *     description="Gets all users"
     * )
     * @SWG\Tag(name="Users")
     */
    public function findAllUsersAction(): JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['id' => 'DESC']);
        $data = $this->serializer->serialize($users, JsonEncoder::FORMAT, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'plainPassword', 'salt', 'password', 'box', 'roles'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }]
        );

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/ean/{ean}", name="findEanProductInfo")
     * @SWG\Response(
     *     response=200,
     *     description="Get detailed informmation about a product"
     * )
     * @SWG\Parameter(
     *     name="ean",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The ean of the requested product, eg: 8719987324772"
     * )
     * @SWG\Tag(name="Ean")
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
     * @SWG\Response(
     *     response=200,
     *     description="Get all boxes from a user"
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The uuid of the user, eg: 10d2327b-20ad-11ea-8a5a-0242c0a86003"
     * )
     * @SWG\Tag(name="Boxes")
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
     * @SWG\Response(
     *     response=200,
     *     description="Gets all products in a box"
     * )
     * @SWG\Parameter(
     *     name="boxId",
     *     in="path",
     *     type="string",
     *     required=true,
     *     description="The id of the requested box, eg: 434"
     * )
     * @SWG\Tag(name="Products")
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
     * @SWG\Response(
     *     response=201,
     *     description="Create new product"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *              @SWG\Property(
     *                  property="box",
     *                  type="integer",
     *                  description="The ID of the box to add your product in",
     *                  example="434"
     *              ),
     *              @SWG\Property(
     *              property="products",
     *              type="array",
     *              description="The product that should be added",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="name",
     *                      type="string",
     *                      description="The name of the product",
     *                      example="Worst"
     *                  ),
     *                  @SWG\Property(
     *                      property="desc",
     *                      type="string",
     *                      description="The description of the product",
     *                      example="Grote & sappige bloedworst"
     *                  ),
     *                  @SWG\Property(
     *                      property="amount",
     *                      type="integer",
     *                      description="The amount added to the box",
     *                      example=2
     *                  ),
     *                  @SWG\Property(
     *                      property="expires",
     *                      type="integer",
     *                      description="The days until the product expires",
     *                      example=12
     *                  ),
     *                  @SWG\Property(
     *                      property="unit",
     *                      type="string",
     *                      description="The unit with amount of the product",
     *                      example="200g"
     *                  ),
     *                  @SWG\Property(
     *                      property="category",
     *                      type="string",
     *                      description="The category of the product",
     *                      example="meat"
     *                  )
     *              )
     *          )
     *
     *     )
     *
     * )
     * @SWG\Tag(name="Products")
     */
    public function createProductAction(Request $request): JsonResponse
    {
        $box = $this->em->getRepository(Box::class)->findOneBy(array('id' => $request->request->get('box')));
        $products = $request->request->get('products');

        if (empty($products)) {
            throw new BadRequestHttpException('Can\'t find a product to add');
        }

        foreach ($products as $product) {
            $name = $product['name'];
            $desc = $product['desc'];
            $amount = $product['amount'];
            $expires = $product['expires'] . ' days';
            $unit = $product['unit'];
            $category = $product['category'];

            if (empty($name) || empty($amount) || empty($expires) || empty($category)) {
                throw new BadRequestHttpException('Can\'t find name, amount, expires or category in the product');
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
     * @SWG\Response(
     *     response=204,
     *     description="Delete a product from the box"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="The id of the product that will be deleted"
     * )
     * @SWG\Tag(name="Products")
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
     * @SWG\Response(
     *     response=200,
     *     description="Create new product"
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="The id of the product that changes"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *         @SWG\Property(
     *             property="amount",
     *             type="integer",
     *             description="The new amount of the product in the box",
     *             example=3
     *         )
     *     )
     * )
     * @SWG\Tag(name="Products")
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
