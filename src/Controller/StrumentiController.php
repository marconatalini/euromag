<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/strumenti")
 */
class StrumentiController extends AbstractController
{
    /**
     * @Route("/opti", name="strumenti_ottimizza")
     */
    public function index(Request $request)
    {
        $lbarra = $request->get('lunghezzaBarre');
        $scarto = $request->get('scartoTeste');
        if ($lbarra !== null){
            $misure = $request->request->get('misure');
            $listaPezzi = [];
            foreach ($misure as $item){
                if (is_array($item)){
                    $misura = $item['lunghezza'];
                    $pezzi = $item['quantita'];
                    if ($misura != '0'){
                        for ($i = 1; $i <= $pezzi; $i++) {
                            $listaPezzi[] = $misura;
                        }
                    }
                }
            }
            $listaBarreOttimizzate = $this->optimize($lbarra, $scarto, $listaPezzi);

            return $this->render('strumenti/ottimizzazione.html.twig', [
                'result' => $listaBarreOttimizzate,
            ]);
        }

        return $this->render('strumenti/index.html.twig', [
            'controller_name' => 'StrumentiController',
        ]);
    }

    private function optimize($lbarra, $scarto, $misure)
    {
        $listaBarre = [];
        $barra = [];
        $lmax = $lbarra-$scarto;

        rsort($misure); //ordino decrescente

        while (count($misure) > 0){ //mentre ci sono ancora pezzi
            while ( ($value = $this->findMaxToFit($lmax - array_sum($barra), $misure)) != 0){
                $barra[] = $value;
            }
            $listaBarre[] = $barra;
            unset($barra);
            $barra = [];
        }

        return $listaBarre;
    }

    private function findMaxToFit($resto, &$array){
        $fit = 0;
        foreach ($array as $value){
            if ($value < $resto) {
                $fit = $value;
                $index = array_search($fit, $array);
                unset($array[$index]);
                break;
            }
        }
        return $fit;
    }
}
