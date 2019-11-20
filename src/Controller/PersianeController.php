<?php

namespace App\Controller;

use App\Entity\Persiane;
use App\Events;
use App\Form\PersianeForm;
use App\Repository\PersianeRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Controller used to manage articoli contents in the public part of the site.
 *
 * @Route("persiane")
 */
class PersianeController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="persiane_index")
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="persiane_add_success")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="persiane_index_paginated")
     * @Cache(smaxage="10")
     */
    public function index(Request $request, int $page, string $_format, PersianeRepository $persianeRepository) : Response
    {
        if (null !== $request->get('search')) {
            $persiane = $persianeRepository->findByCodice($page, $request->get('search'));
        } else {
            $persiane = $persianeRepository->listAll($page);
        }

        return $this->render('persiane/index.html.twig', [
            'controller_name' => 'PersianeController',
            'persiane' => $persiane,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/new", name="persiane_new")
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        // creates a task and gives it some dummy data for this example
        $art = new Persiane();

        $form = $this->createForm(PersianeForm::class, $art);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $art = $form->getData();

            // creates the OrderPlacedEvent and dispatches it
            $event = new GenericEvent($art);

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($art);
                $entityManager->flush();
                $this->addFlash('success','Persiana '.$art->getCodice().' salvato con successo!');
                $eventDispatcher->dispatch(Events::PERSIANA_AGGIUNTA, $event);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning','Persiana '.$art->getCodice().' già presente a magazzino!');
            }

            return $this->redirectToRoute('persiane_index');
        }

        return $this->render('persiane/edit.html.twig', array(
            'form' => $form->createView(),
            'formTitle' => 'Aggiungi persiana',
            'persiana' => $art,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="persiane_update")
     */
    public function update(Request $request, $id, EventDispatcherInterface $eventDispatcher) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $art = $entityManager->getRepository(Persiane::class)->find($id);
        $pezzi = $art->getPezzi();

        $form = $this->createForm(PersianeForm::class, $art);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $art = $form->getData();

            // creates the OrderPlacedEvent and dispatches it
            $event = new GenericEvent($art, ['pezzi' => $pezzi]);

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($art);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning','Persiana '.$art->getCodice().' già presente a catalogo!');
            }

            $this->addFlash('success','Persiana '.$art->getCodice().' salvato con successo!');
            $eventDispatcher->dispatch(Events::PERSIANA_MOVIMENTATA, $event);

            return $this->redirectToRoute('persiane_index');
        }

        return $this->render('persiane/edit.html.twig', array(
            'form' => $form->createView(),
            'formTitle' => 'Modifica articolo',
            'persiana' => $art,
        ));
    }


}
