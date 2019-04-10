<?php

namespace App\Controller;

use App\Entity\Ubicazioni;
use App\Events;
use App\Form\UbicazioniForm;
use App\Repository\UbicazioniRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use http\Exception;
use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UbicazioniController
 * @package App\Controller
 * @Route ("/ubicazioni")
 */
class UbicazioniController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="ubicazioni_index")
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="ubicazioni_add_success")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="ubicazioni_index_paginated")
     */
    public function index (Request $request, int $page, string $_format, UbicazioniRepository $ubicazioni) : Response
    {

        $searchString = $request->get('search');
        $ubicsFree = null;

        if (null !== $searchString) {
            //$ubics = $ubicazioni->findByCodice($page, $request->get('search'));
            $ubics = $ubicazioni->findByArticolo($page, $searchString);
            $result = $ubics->getCurrentPageResults()->current();
            if ($ubics->getNbResults()== 1){
                $ubicsFree = $ubicazioni->findFreeFila($page, $result->getFila());
            }

        } else {
            $ubics= $ubicazioni->all($page);
        }

        return $this->render('ubicazioni/index.html.twig', [
            'controller_name' => 'UbicazioniController',
            'ubicazioni' => $ubics,
            'ubicazioniLibereFila' => $ubicsFree,
            // 'elencoFile' => $ubicazioni->elencoFile()
        ]);
    }

    /**
     * @Route("/fila/{fila<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="ubicazioni_fila")
     */
    public function fila(Request $request, int $fila, string  $_format, UbicazioniRepository $ubicazioni) : Response
    {
        $ubics = $ubicazioni->findUbicazioniFila($fila);
        return $this->render('ubicazioni/index.html.twig', [
            'controller_name' => 'UbicazioniController',
            'ubicazioni' => $ubics,
            'elencoFile' => $ubicazioni->elencoFile()
        ]);
    }

    /**
     * @Route("/free", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="ubicazioni_free")
     * @Route("/free/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="ubicazioni_free_paginated")
     */
    public function free(Request $request, int $page, string $_format, UbicazioniRepository $ubicazioni) : Response
    {

        $libere = $ubicazioni->findFree($page);

        return $this->render('ubicazioni/free.html.twig', [
            'controller_name' => 'UbicazioniController',
            'ubicazioni' => $libere,
            'elencoFile' => $ubicazioni->elencoFile()
        ]);
    }

    /**
     * @Route ("/doppie", name="ubicazioni_pulisci_doppie")
     */
    public function deleteDoppiSpaziVuoti(Request $request, UbicazioniRepository $ubicazioni)
    {
        $ids = $ubicazioni->findUbicazioniDoppieVuote();
        $em = $this->getDoctrine()->getManager();

        foreach ($ids as $id)
        {
            $doppia = $em->getRepository(Ubicazioni::class)->find($id);
            $em->remove($doppia);
            $em->flush();
            $this->addFlash('danger','Ubicazione '.$doppia->getCodice().' vuota e doppia è stata eliminata');
        }

        return $this->render("default/index.html.twig");
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/edit/{id}", name="ubicazioni_update")
     */
    public function update(Request $request, $id, EventDispatcherInterface $eventDispatcher) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ubic = $entityManager->getRepository(Ubicazioni::class)->find($id);

        $form = $this->createForm(UbicazioniForm::class, $ubic);

        if (null === $ubic->getArticolo()) {
            //non c'è articolo quindi non visualizzo tasto aggiungi
            $form->remove('aggiungi');
            $form->remove('libera');
        } else {
            $form->remove('cerca');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $ubic = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!

            if (null !== $ubic->getArticolo()){
                $codiceArt = $ubic->getArticolo()->getCodice();
            } else {
                $codiceArt = 'Nessun profilo';
            }

            // creates the OrderPlacedEvent and dispatches it
            $event = new GenericEvent($ubic, ['codiceArticolo'=>$codiceArt, ]);

            try {
                if ($form->has('aggiungi') and $form->get('aggiungi')->isClicked()){
                    $newUbic = new Ubicazioni();
                    $newUbic->setCodice($ubic->getCodice());
                    $newUbic->setFila($ubic->getFila());
                    $newUbic->setColonna($ubic->getColonna());
                    $newUbic->setPiano($ubic->getPiano());
                    $entityManager->persist($newUbic);
                    $entityManager->flush();
                    $this->addFlash('success','Ubicazione '.$ubic->getCodice().' duplicata per far spazio ad altro articolo.');
                    return $this->redirectToRoute('ubicazioni_update', ['id'=>$newUbic->getId()]);
                }
                if ($form->has('libera') and $form->get('libera')->isClicked()){
                    $ubic->setArticolo(null);
                    $this->addFlash('success','Ubicazione '.$ubic->getCodice().' ora è LIBERA. (Eliminato '.$codiceArt.')');
                    $eventDispatcher->dispatch(Events::UBICAZIONE_LIBERATA, $event);
                } elseif (null !== $ubic->getArticolo()){
                    $this->addFlash('danger','Ubicazione '.$ubic->getCodice().' occupata da '. $codiceArt);
                    $eventDispatcher->dispatch(Events::ARTICOLO_UBICATO, $event);
                }
                $entityManager->persist($ubic);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('warning','Ubicazione '.$ubic->getCodice().' già presente a magazzino!');
            }

            return $this->redirectToRoute('ubicazioni_add_success');
        }

        return $this->render('ubicazioni/edit.html.twig', array(
            'form' => $form->createView(),
            'ubicazione' => $ubic,
        ));
    }

    /**
     * @Route("/pdf", name="ubicazioni_pdf")
     */
    public function pdf(UbicazioniRepository $ubicazioniRepository)
    {
        $ubicazioni = $ubicazioniRepository->findAllpdf();

        $html = $this->renderView( 'ubicazioni/pdf.html.twig',[
            'ubicazioni' => $ubicazioni,
        ]);

        $mpdf = new Mpdf([
            'tempDir' => __DIR__ . '/../../var/mpdf'
        ]);

        //var_dump($mpdf->tempDir);die;

        try {
            // Write some HTML code:
            $mpdf->WriteHTML($html);

            // Output a PDF file directly to the browser
            $mpdf->Output();
            //$mpdf->Output('Ubicazioni.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        } catch (Exception $e) {
            return new Response($html);
        }

        return new Response($html);
    }
}
