<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\StringRandomizerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\VarDumper\VarDumper;
use Throwable;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', []);
    }

    /**
     * @Route("/early-registration", name="early_registration", methods={"POST"})
     */
    public function earlyRegistration(
        Request $request, 
        EntityManagerInterface $em, 
        ValidatorInterface $validator, 
        UserPasswordEncoderInterface $passwordEncoder,
        StringRandomizerHelper $helper): JsonResponse
    {
        $data = $request->request->all();
        
        try {
            $user = new User();
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPhone($data['phone']);
            $user->setPlainPassword($helper->random());

            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $em->persist($user);
            $em->flush($user);

            return $this->json(["message"=>'Cadastro realizado com sucesso!']);
        } catch (Throwable $e) {
            return $this->json(["message"=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}