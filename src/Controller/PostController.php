<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use App\Entity\Post;
use App\Form\PostFormType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function index(PersistenceManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $em = $doctrine->getManager();

        $posts = $paginator->paginate(
            $em->getRepository(Post::class)->createQueryBuilder('p')
                ->orderBy('p.modified_at', 'DESC')
                ->getQuery(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('posts/blog.html.twig', [
            'posts' => $posts,
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
                'errors' => null,
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

        $post->setModifiedAt(new \DateTimeImmutable('now'));
        $post->setState('Supprimé');
        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_posts');
    }
}
