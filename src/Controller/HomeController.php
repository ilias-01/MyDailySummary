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
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request,ArticleRepository $articleRep): Response
    {   
        //purge DATABASE before add new data
        //truncate on the Article table
        $connection = $this->em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('article', true ));
        
        //In case of there is an error while loading the xml files
        try{
            $rss_politique = simplexml_load_file('https://www.lefigaro.fr/rss/figaro_politique.xml');
            $rss_election = simplexml_load_file('https://www.lefigaro.fr/rss/figaro_elections.xml');
            //$rss_sport = simplexml_load_file('https://sport24.lefigaro.fr/rssfeeds/sport24-accueil.xml');
            
            $this->putArticlesOnDB($rss_politique);
            $this->putArticlesOnDB($rss_election);
            //$this->putArticlesOneDB($rss_sport);

        }catch(Exception $e){
            $rss_politique=null;
            $rss_election=null;
            //$rss_sport=null;
        }

        $arSearch = new ArticleSearch();
        $form = $this->createForm(ArticleSearchType::class,$arSearch);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $rss_politique = $articleRep->findBySearch($arSearch);
        }
        return $this->render('home/index.html.twig', [
            'rss_politique' => $rss_politique,
            'rss_election' => $rss_election,
            'form_search' => $form->createView()
        ]);
    }

    private function  putArticlesOnDB($rss)
    {
        //Array transform
        $objJsonDocument = json_encode($rss);
        $arrOutput = json_decode($objJsonDocument, TRUE);
        
        $articles = [];
        for ($i = 0; $i < count($rss->channel->item); $i++) {
            $article = new Article();
            $articles[] = [
                'item'.$i => $arrOutput['channel']['item'][$i]
            ];
            $article->setTitle( $articles[$i]['item'.$i]['title']);
            $article->setDescription($articles[$i]['item'.$i]['description'] == [] ? '' : $articles[$i]['item'.$i]['description']);
            $article->setCategory($articles[$i]['item'.$i]['category'] == null ? 'Sport' : $articles[$i]['item'.$i]['category']);
            $article->setPubDate($articles[$i]['item'.$i]['pubDate']);
            $article->setAuthor($articles[$i]['item'.$i]['author'] == null ? '' : $articles[$i]['item'.$i]['author']);
            $article->setLink($articles[$i]['item'.$i]['link']);
            $this->em->persist($article);
        }
        $this->em->flush();
    }
}
