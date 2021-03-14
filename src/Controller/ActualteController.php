<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("/actuality/add/{id}", name="add.actuality")
     */
    public function addToFavoris(Article $article,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ],403);

        if(!$article)return $this->json([
            'code' => 404,
            'message' => "Article non trouvé"
        ],404);

        $favoris =  new Favoris();
        $favoris->setUser($user);
        $favoris->setArticle($article);

        $em->persist($favoris);
        $em->flush();
        return $this->json([
            'code' => "200",
            'message' => "L'article est bien ajouté aux favoris"
        ],200);
    }

    /**
     * @Route("/actuality/remove/{id}", name="remove.actuality")
     */
    public function removeFromFavoris(Article $article,EntityManagerInterface $em): Response
    {
        $favRepo = $em->getRepository(Favoris::class);
        $favoris = $favRepo->find($article);

        $em->remove($favoris);
        $em->flush();

        return $this->json([
            'code' => "200",
            'message' => "L'article est bien supprimé des favoris"
        ],200);
    }

}
