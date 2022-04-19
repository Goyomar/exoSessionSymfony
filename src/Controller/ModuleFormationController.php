<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Entity\ModuleFormation;
use App\Form\ModuleFormationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleFormationController extends AbstractController
{
    /**
     * @Route("/module/formation", name="app_module_formation")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $modules = $doctrine->getRepository(ModuleFormation::class)->findAll();

        return $this->render('module_formation/index.html.twig', [
            'modules' => $modules
        ]);
    }

    /**
     * @Route("/module/add", name="add_module_formation")
     * @Route("/module/edit/{id}", name="edit_module_formation")
     */
    public function add(ManagerRegistry $doctrine, ModuleFormation $module = null, Request $request): Response
    {
        if(!$module){
            $module = new ModuleFormation();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(ModuleFormationType::class, $module);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $module = $form->getData();

            $entityManager->persist($module);
            $entityManager->flush();
            return $this->redirectToRoute('app_module_formation');
        }

        return $this->renderForm('module_formation/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/categorie/add", name="add_categorie")
     * @Route("/categorie/edit/{id}", name="edit_categorie")
     */
    public function addCategorie(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
    {
        if(!$categorie){
            $categorie = new Categorie();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('app_module_formation');
        }

        return $this->renderForm('module_formation/addCategorie.html.twig', [
            'form' => $form,
        ]);
    }
}
