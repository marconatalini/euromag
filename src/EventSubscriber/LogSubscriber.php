<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 27/01/2019
 * Time: 00:13
 */

namespace App\EventSubscriber;

use App\Entity\Articoli;
use App\Entity\Log;
use App\Entity\Ubicazioni;
use App\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class LogSubscriber implements EventSubscriberInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            Events::ARTICOLO_UBICATO => 'onArticoloUbicato',
            Events::UBICAZIONE_LIBERATA => 'onUbicazioneLiberata',
            Events::ARTICOLO_AGGIUNTO=> 'onArticoloNuovo',
            Events::ARTICOLO_MODIFICATO=> 'onArticoloModificato',
        ];
    }

    public function onArticoloNuovo(GenericEvent $event): void
    {
        /** @var Articoli $articolo */
        $articolo = $event->getSubject();

        $this->LogIt("Aggiunto ". $articolo->getCodice());
    }

    public function onArticoloModificato(GenericEvent $event): void
    {
        /** @var Articoli $articolo */
        $articolo = $event->getSubject();

        $this->LogIt("Modificato ". $articolo->getCodice());
    }

    public function onArticoloUbicato(GenericEvent $event): void
    {
        /** @var Ubicazioni $ubicazione */
        $ubicazione = $event->getSubject();

        $this->LogIt($ubicazione->getCodice(). " occupata da " . $ubicazione->getArticolo()->getCodice());
    }

    public function onUbicazioneLiberata(GenericEvent $event): void
    {
        /** @var Ubicazioni $ubicazione */
        $ubicazione = $event->getSubject();
        $codiceArticolo = $event->getArgument('codiceArticolo');

        $this->LogIt($ubicazione->getCodice(). " liberata da ". $codiceArticolo);

    }

    private function LogIt(string $action)
    {
        $log = new Log();
        $log->setTimestamp(new \DateTime('now'))
            ->setUser($_SERVER['HTTP_USER_AGENT'])
            ->setDescrizione($action)
            ->setIp($_SERVER['REMOTE_ADDR']);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}