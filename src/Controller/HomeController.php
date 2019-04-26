<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JSONResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Psr\Log\LoggerInterface;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;


/**
 * @Route("/", name="oldarticle_")
 */
class HomeController extends AbstractController
{

    /**
     * @var App\Repository\ArticleRepository
     */
    private $articleRepository;

    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        ArticleRepository $articleRepository,
        LoggerInterface $logger,
        EntityManagerInterface $em
    ) {
        $this->articleRepository = $articleRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function index(): Response
    {
        $articles = $this->articleRepository->findAllOrderedByDateDesc();

        return $this->render("old_article/index.html.twig", [
            "articles" => $articles,
        ]);
    }

    /**
     * @Route(path="/{id}", name="show", requirements = { 
     *      "id" = "\d+"
     *  })
     */
    public function show(Article $article): Response
    {
        return $this->render("old_article/article.html.twig", [
            "article" => $article
        ]);
    }

    /**
     * @Route("/{id}/like", name="like", requirements = {
     *      "id" = "\d+"
     *  }, methods={
     *      "POST"
     *  })
     */
    public function toggleArticleHeart(Article $article): JSONResponse
    {
        $article->addLike();

        $likes = $article->getNbLike();

        $this->em->persist($article);
        $this->em->flush();

        $this->logger->info("New like on Article", [
            "id" => $article->getId()
        ]);

        return $this->json([
            "success" => true,
            "likes" => $likes,
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(ArticleFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Article $article */
            $article = $form->getData();

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Your new article is now published'
            );

            return $this->redirectToRoute('oldarticle_homepage');
        }
        return $this->render("old_article/new.html.twig", [
            "form" => $form->createView(),
        ]);
    }
}
