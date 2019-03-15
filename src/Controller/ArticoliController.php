<?php

namespace App\Controller;

use App\Entity\Articoli;
use App\Events;
use App\Form\ArticoliForm;
use App\Repository\ArticoliRepository;
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
 * @Route("articoli")
 */
class ArticoliController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="articoli_index")
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="articolo_add_success")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="articoli_index_paginated")
     * @Cache(smaxage="10")
     */
    public function index(Request $request, int $page, string $_format, ArticoliRepository $articoliRepository) : Response
    {
        if (null !== $request->get('search')) {
            $articoli = $articoliRepository->findByCodice($page, $request->get('search'));
        } else {
            $articoli = $articoliRepository->listAll($page);
        }

        return $this->render('articoli/index.html.twig', [
            'controller_name' => 'ArticoliController',
            'articoli' => $articoli,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/new", name="articoli_new")
     */
    public function new(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        // creates a task and gives it some dummy data for this example
        $art = new Articoli();

        $form = $this->createForm(ArticoliForm::class, $art);

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
                $this->addFlash('success','Articolo '.$art->getCodice().' salvato con successo!');
                $eventDispatcher->dispatch(Events::ARTICOLO_AGGIUNTO, $event);
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning','Articolo '.$art->getCodice().' già presente a catalogo!');
            }

            return $this->redirectToRoute('articolo_add_success');
        }

        return $this->render('articoli/edit.html.twig', array(
            'form' => $form->createView(),
            'formTitle' => 'Aggiungi articolo',
            'articolo' => $art,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="articoli_update")
     */
    public function update(Request $request, $id, EventDispatcherInterface $eventDispatcher) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $art = $entityManager->getRepository(Articoli::class)->find($id);

        $form = $this->createForm(ArticoliForm::class, $art);

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
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning','Articolo '.$art->getCodice().' già presente a catalogo!');
            }

            $this->addFlash('success','Articolo '.$art->getCodice().' salvato con successo!');
            $eventDispatcher->dispatch(Events::ARTICOLO_MODIFICATO, $event);

            return $this->redirectToRoute('articolo_add_success');
        }

        return $this->render('articoli/edit.html.twig', array(
            'form' => $form->createView(),
            'formTitle' => 'Modifica articolo',
            'articolo' => $art,
        ));
    }

    /**
     * @Route("/view/{id}", name="articoli_view")
     */
    public function view(Request $request, $id) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articolo = $entityManager->getRepository(Articoli::class)->find($id);

        return $this->render('articoli/view.html.twig', array(
            'articolo' => $articolo,
        ));

    }
}
