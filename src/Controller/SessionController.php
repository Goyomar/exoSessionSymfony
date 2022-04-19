<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Planifier;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\PlanifierType;
use App\Entity\ModuleFormation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{
    /**
     * @Route("/session", name="app_session")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $sessions = $doctrine->getRepository(Session::class)->findAll();

        $maintenant = new \DateTime();

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            'maintenant' => $maintenant
        ]);
    }

    /**
     * @Route("/session/add", name="add_session")
     * @Route("/session/edit/{id}", name="edit_session")
     */
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
    {
        if(!$session){
            $session = new Session();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session = $form->getData();

            $entityManager->persist($session);
            $entityManager->flush();
            return $this->redirectToRoute('app_session');
        }

        return $this->renderForm('session/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/session/{id}", name="show_session")
     */
    public function show(ManagerRegistry $doctrine, Session $session): Response
    {
        $nonInscrit = $doctrine->getRepository(Stagiaire::class)->getNonInscrits($session->getId());
        $nonPlanifier = $doctrine->getRepository(ModuleFormation::class)->getNonPlanifier($session->getId());

        return $this->render('session/show.html.twig',[
            'session' => $session,
            'nonInscrit' => $nonInscrit,
            'nonPlanifier' => $nonPlanifier
        ]);
    }

    /**
     * @Route("/session/desinscrire/{idSession}/{idStagiaire}", name="desinscrire_session")
     * 
     * @ParamConverter("session", options={"mapping": {"idSession" : "id"}})
     * @ParamConverter("stagiaire", options={"mapping": {"idStagiaire" : "id"}})
     */
    public function desinscrire(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire): Response
    {
        $session->removeStagiaire($stagiaire);
        $doctrine->getManager()->flush();
        $nonInscrit = $doctrine->getRepository(Stagiaire::class)->getNonInscrits($session->getId());
        $nonPlanifier = $doctrine->getRepository(ModuleFormation::class)->getNonPlanifier($session->getId());

        return $this->render('session/show.html.twig',[
            'session' => $session,
            'nonInscrit' => $nonInscrit,
            'nonPlanifier' => $nonPlanifier
        ]);
    }

    /**
     * @Route("/session/inscrire/{idSession}/{idStagiaire}", name="inscrire_session")
     * 
     * @ParamConverter("session", options={"mapping": {"idSession" : "id"}})
     * @ParamConverter("stagiaire", options={"mapping": {"idStagiaire" : "id"}})
     */
    public function inscrire(ManagerRegistry $doctrine, Session $session, Stagiaire $stagiaire): Response
    {
        if ($session->getPlaceRestante() > 0){
            $session->addStagiaire($stagiaire);
            $doctrine->getManager()->flush();
        }
        
        $nonInscrit = $doctrine->getRepository(Stagiaire::class)->getNonInscrits($session->getId());
        
        return $this->render('session/show.html.twig',[
            'session' => $session,
            'nonInscrit' => $nonInscrit
        ]);
    }

    /**
     * @Route("/session/delModule/{idPlanifier}/{idSession}", name="delmodule_session")
     * 
     * @ParamConverter("session", options={"mapping": {"idSession" : "id"}})
     * @ParamConverter("planifier", options={"mapping": {"idPlanifier" : "id"}})
     */
    public function delModule(ManagerRegistry $doctrine, Session $session, Planifier $planifier): Response
    {
        $session->removePlanifier($planifier);
        $doctrine->getManager()->remove($planifier);
        $doctrine->getManager()->flush();
        
        $nonInscrit = $doctrine->getRepository(Stagiaire::class)->getNonInscrits($session->getId());
        $nonPlanifier = $doctrine->getRepository(ModuleFormation::class)->getNonPlanifier($session->getId());

        return $this->render('session/show.html.twig',[
            'session' => $session,
            'nonInscrit' => $nonInscrit,
            'nonPlanifier' => $nonPlanifier
        ]);
    }

    /**
     * @Route("/session/addModule/{idModule}/{idSession}", name="addmodule_session")
     * 
     * @ParamConverter("session", options={"mapping": {"idSession" : "id"}})
     * @ParamConverter("module", options={"mapping": {"idModule" : "id"}})
     */
    public function addModule(ManagerRegistry $doctrine, Session $session, ModuleFormation $module, Request $request): Response
    {
            $planifier = new Planifier();
            $planifier->setDuree($request->request->get("nbJours"))
                      ->setModuleFormation($module)
                      ->setSession($session);
            $doctrine->getManager()->persist($planifier);
            $doctrine->getManager()->flush();

        $nonInscrit = $doctrine->getRepository(Stagiaire::class)->getNonInscrits($session->getId());
        $nonPlanifier = $doctrine->getRepository(ModuleFormation::class)->getNonPlanifier($session->getId());

        return $this->render('session/show.html.twig',[
            'session' => $session,
            'nonInscrit' => $nonInscrit,
            'nonPlanifier' => $nonPlanifier
        ]);
    }

    /**
     * @Route("/planifier/add", name="add_planifier")
     * @Route("/planifier/edit/{id}", name="edit_planifier")
     */
    public function addPlanifier(ManagerRegistry $doctrine, Planifier $planifier = null, Request $request): Response
    {
        if(!$planifier){
            $planifier = new Planifier();
        }

        $entityManager = $doctrine->getManager();
        $form = $this->createForm(PlanifierType::class, $planifier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $planifier = $form->getData();

            $entityManager->persist($planifier);
            $entityManager->flush();
            return $this->redirectToRoute('app_session');
        }

        return $this->renderForm('session/addPlanifier.html.twig', [
            'form' => $form,
        ]);
    }
}
