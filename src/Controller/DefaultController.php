<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="index")
     */
    public function index()
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        $data = null;
        if (!empty($user)) {
            $userClone = clone $user;
            $userClone->setPassword('');
            $data = $this->serializer->serialize($userClone, JsonEncoder::FORMAT);
        }

        return $this->render('base.html.twig', [
            'isAuthenticated' => json_encode(!empty($user)),
            'user'            => $data ?? json_encode($data),
        ]);
    }

    /**
     * @Route("/api/testview", name="default")
     */
    public function testview()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => 'admin@admin.com'));
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'user' => $user
        ]);
    }
}
