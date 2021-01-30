<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\FavorisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris", name="favoris")
     */
    public function index(FavorisRepository $favRep,ArticleRepository $articleRep): Response
    {
        $favoris = $favRep->findBy(["user"=> $this->getUser() ]);
        //dd($favoris[0]->getArticle()->getId());
        $articles_tab =[];
        foreach($favoris as $article)
        {
            array_push($articles_tab,$articleRep->findOneBy(['id'=> $article->getId()]));
        }
        //dd($articles_tab);

        return $this->render('favoris/index.html.twig',[
            'articles' => $articles_tab
        ]);
    }
}
