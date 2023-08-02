<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(UserRepository $userRepository, SortieRepository $sortieRepository): Response
    {
//        if ($this->getUser()) {
            return $this->render('main/home.html.twig', [
                'user'=>$this->getUser(),
                'sortiesNb'=>$sortieRepository->count([])
            ]);
//        }
//        return $this->redirectToRoute('app_login');
    }
}
