<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\UserRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, SortieRepository $sortieRepository, UserRepository $userRepository, CampusRepository $campusRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $sorties = $sortieRepository->findAll();
        $sortiesNb = $sortieRepository->count([]);
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sortiesNb' => $sortiesNb,
            'campus' => $campusRepository->findAll(),
        ]);
    }



    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $sortie = new Sortie();

        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $sortie->setCampus($user->getCampus());
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'Créée']));
            $sortie->setOrganisateur($this->getUser());
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(int $id, SortieRepository $sortieRepository, UserRepository $participantRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        //$sortie->getParticipants();

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            $this->addFlash('success', 'La sortie : '  .$sortie->getNom(). ' à bien été modifié' );

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/annule', name: 'app_sortie_annule', methods: ['GET', 'POST'])]
    public function annule(Request $request, SortieRepository $sortieRepository, Sortie $sortie, EtatRepository $etatRepository, UserRepository $UserRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('motif', TextareaType::class,[
                'label' => false,
                'attr' =>['placeholder' => 'Veuillez saisir le motif d\'annulation'],
                'required' => true,
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $sortie->getDateHeureDebut() > new \DateTime()){

            $motif=$form->getData();

            $etat = $etatRepository->findOneBy(['libelle' =>'cloturée'],[]);
            $sortie->setEtat($etat);
            $sortie->setInfosSortie($motif['motif']);
            $sortieRepository->save($sortie, true);

            $this->addFlash('success', 'La sortie : '  .$sortie->getNom(). ' à bien été annulée' );

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/annule.html.twig',[
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);


    }

    #[Route('/inscription/{id}', name: 'app_sortie_inscription', methods: ['GET'])]
    public function inscription (int $id, SortieRepository $sortieRepository)
    {
        $sortie = $sortieRepository->find($id);
        $sortie->addUser($this->getUser());

        $sortieRepository->save($sortie, true);

        $this->addFlash('success', 'Vous êtes bien inscrit à la sortie : '  .$sortie->getNom() );

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/desiste/{id}', name: 'app_sortie_desiste', methods: ['GET'])]
    public function desiste (int $id, SortieRepository $sortieRepository)
    {
        $sortie = $sortieRepository->find($id);
        $sortie->removeUser($this->getUser());

        $sortieRepository->save($sortie, true);

        $this->addFlash('success', 'Vous vous êtes bien désisté de la sortie : '  .$sortie->getNom() );

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


}