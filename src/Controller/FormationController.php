<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Entity\Formation;
use App\Form\FormateurType;
use App\Form\FormationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="app_formation")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $formations = $doctrine->getRepository(Formation::class)->findAll();

        return $this->render('formation/index.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("/formation/add", name="add_formation")
     * @Route("/formation/edit/{id}", name="edit_formation")
     */
    public function add(ManagerRegistry $doctrine, Formation $formation = null, Request $request): Response
    {
        if(!$formation){
            $formation = new Formation();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formation = $form->getData();

            $entityManager->persist($formation);
            $entityManager->flush();
            return $this->redirectToRoute('app_formation');
        }

        return $this->renderForm('formation/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/formateur/add", name="add_formateur")
     */
    public function addFormateur(ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {
        if(!$formateur){
            $formateur = new Formateur();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(FormateurType::class, $formateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formateur = $form->getData();

            $entityManager->persist($formateur);
            $entityManager->flush();
            return $this->redirectToRoute('app_formation');
        }

        return $this->renderForm('formation/addFormateur.html.twig', [
            'form' => $form,
        ]);
    }
}
