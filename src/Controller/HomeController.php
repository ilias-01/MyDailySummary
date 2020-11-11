<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $rss_politique = simplexml_load_file('https://www.lefigaro.fr/rss/figaro_politique.xml');
        //dd($data = file_get_contents('https://www.lefigaro.fr/politique/le-covid-19-a-pousse-les-nouveaux-maires-a-devenir-des-gestionnaires-de-crise-permanente-20201109'));
        //dd($rss_politique);
        return $this->render('home/index.html.twig', [
            'rss' => $rss_politique
        ]);
    }
}
