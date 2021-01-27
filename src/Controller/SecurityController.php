<?php

namespace App\Controller;

use App\Entity\ArticleSearch;
use App\Form\ArticleSearchType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils,ArticleRepository $articleRep,Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $arSearch = new ArticleSearch();
        $form = $this->createForm(ArticleSearchType::class,$arSearch);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $rss_politique = $articleRep->findBySearch($arSearch);
            return $this->render('home/index.html.twig', [
                'rss_politique' => $rss_politique,
                'form_search' => $form->createView()
            ]);
        }



        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error,'form_search' => $form->createView()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
