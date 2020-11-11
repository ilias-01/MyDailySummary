<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualteController extends AbstractController
{
    /**
     * @Route("/actuality", name="app.actuality")
     */
    public function allActuality(): Response
    {
        $rss_politique = simplexml_load_file('https://www.ouest-france.fr/rss-en-continu.xml');
        
        return $this->render('actuality/index.html.twig', [
            'controller_name' => 'ActualteController',
            'rss' => $rss_politique
        ]);
    }


}
