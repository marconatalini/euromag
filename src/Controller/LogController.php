<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 27/01/2019
 * Time: 14:32
 */

namespace App\Controller;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LogController
 * @package App\Controller
 * @Route ("/log")
 */
class LogController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="logs_index")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="logs_index_paginated")
     */
    public function index (Request $request, int $page, string $_format, LogRepository $logs) : Response
    {
        $lasts = $logs->findLastMonth($page);

        return $this->render('logs/index.html.twig', [
            'logs' => $lasts,
        ]);
    }
}