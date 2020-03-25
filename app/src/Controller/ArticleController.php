<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article")
     */
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $dql = "SELECT a FROM App\Entity\Article a ORDER BY a.created_at DESC";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            $request->query->getInt('limit', 2) /*limit per page*/
        );

        return $this->render('article/list.html.twig', [
            'controller_name' => 'ArticleController',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/article/view/{article}", name="view_article")
     */
    public function article(Article $article)
    {
        return $this->render('article/view.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("article/create", name="create_article")
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'action' => 'create',
        ]);
    }

    /**
     * @Route("article/update/{article}", name="update_article")
     */
    public function update(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', [
                'article' => $article->getId(),
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $article->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'action' => 'update',
        ]);
    }

    /**
     * @Route("article/delete/{article}", name="delete_article")
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article');
    }
}
