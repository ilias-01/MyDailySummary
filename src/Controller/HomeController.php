<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleSearch;
use App\Form\ArticleSearchType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em,Request $request,ArticleRepository $articleRep): Response
    {   
        //purge DATABASE before add new data
        //truncate on the Article table
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('article', true ));
        
        //In casse there is an error while loading the xml files
        try{
            $rss_politique = simplexml_load_file('https://www.lefigaro.fr/rss/figaro_politique.xml');
            $rss_election = simplexml_load_file('https://www.lefigaro.fr/rss/figaro_elections.xml');
            $rss_sport = simplexml_load_file('https://sport24.lefigaro.fr/rssfeeds/sport24-accueil.xml');
            //Array transform
            $objJsonDocument = json_encode($rss_politique);
            $arrOutput_politique = json_decode($objJsonDocument, TRUE);

            $articles = [];

            for ($i = 0; $i < count($rss_politique->channel->item); $i++) {
                $article = new Article();
                $articles[] = [
                    'item'.$i => $arrOutput_politique['channel']['item'][$i]
                ];
                $article->setTitle( $articles[$i]['item'.$i]['title']);
                $article->setDescription($articles[$i]['item'.$i]['description']);
                $article->setCategory($articles[$i]['item'.$i]['category']);
                $article->setPubDate($articles[$i]['item'.$i]['pubDate']);
                $article->setAuthor($articles[$i]['item'.$i]['author']);
                $article->setLink($articles[$i]['item'.$i]['link']);
                $em->persist($article);
            }
            $em->flush();
        }catch(Exception $e){
            $rss_politique=null;
            $rss_election=null;
            $rss_sport=null;
        }

        $arSearch = new ArticleSearch();
        $form = $this->createForm(ArticleSearchType::class,$arSearch);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $rss_politique = $articleRep->findBySearch($arSearch);
        }

        return $this->render('home/index.html.twig', [
            'rss_politique' => $rss_politique,
            'form_search' => $form->createView()
        ]);
    }
}
