<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function earlyRegistration(Request $request): JsonResponse
    {
        $data = $request->request->all();
        return $this->json(["status"=>true, "message"=>'Cadastro realizado com sucesso!'], Response::HTTP_BAD_REQUEST);
    }
}