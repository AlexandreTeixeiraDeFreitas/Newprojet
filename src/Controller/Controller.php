<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
//use Doctrine\Persistance\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class Controller extends AbstractController
{
    public function __construct(protected ManagerRegistry $registry)
    {
        
    }
    #[Route('', name: 'home')]
    public function index(): Response
    {
        $articleRegistery = $this->registry->getRepository(Article::class);
        $articles = $articleRegistery->findAll();
        //dd($article);
        return $this->render('layout/index.html.twig',[
            "articles" => $articles
        ]);
    }
    #[Route('/article/{id}', name: 'article', methods: ['GET'])]
    public function getArticle($id){
        $articleRegistery = $this->registry->getRepository(Article::class)->find($id);
        $articles = $articleRegistery->findAll($id);
        dd($articles);

        $title = $articles->getTitle();
        return $this->render('layout/article.html.twig',[
            "articles" => $articles
        ]);
    }

    #[Route('/new', name: 'new')]
    public function newArticle(Request $request){
        $articles = new Article();
        $form = $this->createForm(ArticleType::class, $articles);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $p =$this->registry->getManager();
            $p->persist($articles);
            $p->flush();
            return $this->redirectToRoute('home');
        }
        return $this->renderForm('layout/New.html.twig',[
            "form" => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->save($article, true);

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
}
