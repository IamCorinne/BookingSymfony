<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_list")
     */
    public function index(CommentRepository $repo,$page,Pagination $paginationService)
    {
        $paginationService  ->setEntityClass(Comment::class)
                            ->setLimit(5)
                            ->setPage($page)
                            //->setRoute('admin_comments_list')
                            ;
        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $paginationService
        ]);
    }
    /**
     * Permet d'éviter un commentaire insultant ou autre
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Comment $comment,Request $request, EntityManagerInterface $manager )
    {
        $form=$this->createForm(AdminCommentType::class, $comment);
        $form->handlerequest($request);

        if($form->isSubmitted()&& $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success","Le commentaire a bien été enregistré.");
            return $this->redirectToRoute('admin_comments_list');
        }
        return $this->render('admin/comment/edit.html.twig',
        [
            'comments'=>$comment,
            'form'=>$form->createView()
        ]);
    }
    /**
     * Suppression d'un commentaire
     *  @Route("/admin/comment/{id}/delete",name="admin_comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success',"Le commentaire {$comment->getId()} a bien été supprimé.");

        return $this->redirectToRoute('admin_comments_list');
    }
}
