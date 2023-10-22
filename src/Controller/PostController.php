<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function index(PersistenceManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $em = $doctrine->getManager();

        $pageSize = 4;
        $status = 'Validé';
        $currentPage = $request->query->getInt('page', 1);

        $posts = $paginator->paginate(
            $em->getRepository(Post::class)->createQueryBuilder('p')
                ->where('p.state = :state')
                ->setParameter('state', $status)
                ->orderBy('p.modified_at', 'DESC')
                ->getQuery(),
            $currentPage,
            $pageSize,
        );

        $totalPages = ceil($posts->getTotalItemCount() / $pageSize);
        $previousPage = $currentPage > 1 ? $currentPage - 1 : null;
        $nextPage = $currentPage < $totalPages ? $currentPage + 1 : null;

        return $this->render('posts/blog.html.twig', [
            'posts' => $posts,
            'status' => $status,
            'currentPage' => $currentPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/posts/create', name: 'app_post_create')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $post->setPostUser($this->getUser());
            $post->setState('Validé');
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $post->setModifiedAt(new \DateTimeImmutable('now'));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts');
        } else {
            $errors = $validator->validate($post);

            return $this->render('posts/create.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
            ]);
        }

        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
            'errors' => null,
        ]);
    }

    #[Route('/posts/{id}/edit', name: 'app_post_edit')]
    public function edit(Request $request, Post $post, PersistenceManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $post->setPostUser($this->getUser());
            $post->setState('Validé');
            $post->setModifiedAt(new \DateTimeImmutable('now'));
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts');
        } else {
            $errors = $validator->validate($post);

            return $this->render('posts/edit.html.twig', [
                'form' => $form->createView(),
                'errors' => $errors,
                'post' => $post,
            ]);
        }
    }

    #[Route('/posts/{id}/delete', name: 'app_post_delete')]
    public function delete(Post $post, PersistenceManagerRegistry $doctrine): Response
    {
        if ($post->getPostUser() != $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer ce post');
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_posts');
    }
}
