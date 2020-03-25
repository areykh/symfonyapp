<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("comment/create/{article}", name="create_comment")
     */
    public function create(Request $request, Article $article)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('create_comment', [
                'article' => $article->getId(),
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setArticle($article);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('view_article', [
                'article' => $article->getId(),
            ]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'action' => 'create',
        ]);
    }

    /**
     * @Route("comment/update/{article}/{comment}", name="update_comment")
     */
    public function update(Request $request, Article $article, Comment $comment)
    {
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('update_comment', [
                'article' => $article->getId(),
                'comment' => $comment->getId(),
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('view_article', [
                'article' => $article->getId(),
            ]);
        }

        return $this->render('comment/form_update.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'action' => 'update',
        ]);
    }

    /**
     * @Route("comment/delete/{article}/{comment}", name="delete_comment")
     */
    public function delete(Article $article, Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('view_article', [
            'article' => $article->getId(),
        ]);
    }
}
