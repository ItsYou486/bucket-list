<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\FormType;
use App\Form\SerieType;
use App\Repository\WishRepository;
use App\Service\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/wish', name: 'app_wish')]
#[IsGranted('ROLE_USER')]
class WishController extends AbstractController
{
    #[Route('/list', name: '_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $bucketList = $wishRepository->findAll();
        return $this->render('wish/list.html.twig',[
            'bucketlist'=>$bucketList
        ]);
    }

    #[Route('/list/detail/{id}', name: '_detail', requirements:['id'=>'\d+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $detail = $wishRepository->find($id);
        return $this->render('wish/detail.html.twig',
        [
            'detailBucketList' => $detail
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Censurator $censurator): Response
    {
        $wish = new Wish();

        $form = $this->createForm(FormType::class, $wish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($wish->getTitle()) . '-' . uniqid() . '.' . $posterFile->guessExtension();
                $posterFile->move($this->getParameter('poster_dir'), $fileName);
                $wish->setPosterFile($fileName);
            }

            $censuredTitle = $censurator->purify($wish->getTitle());
            $censuredDescription = $censurator->purify($wish->getDescription());

            $wish->setTitle($censuredTitle);
            $wish->setDescription($censuredDescription);

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'La série a été enregistrée');
            return $this->redirectToRoute('app_wish_list');
        }


        return $this->render('wish/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $em, Wish $wish, SluggerInterface $slugger, Censurator $censurator): Response
    {

        $form = $this->createForm(FormType::class, $wish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $dir = $this->getParameter('poster_dir');
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($wish->getTitle()) . '-' . uniqid() . '.' . $posterFile->guessExtension();
                $posterFile->move($dir, $fileName);

                if ($wish->getPosterFile() && \file_exists($dir . '/' . $wish->getPosterFile())) {
                    unlink($dir . '/' . $wish->getPosterFile());
                }

                $wish->setPosterFile($fileName);

            }

            $censuredTitle = $censurator->purify($wish->getTitle());
            $censuredDescription = $censurator->purify($wish->getDescription());

            $wish->setTitle($censuredTitle);
            $wish->setDescription($censuredDescription);

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'La série a été modifiée');
            return $this->redirectToRoute('app_wish_list');
        }


        return $this->render('wish/formupdate.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(Wish $wish, EntityManagerInterface $em): Response
    {
        $em->remove($wish);
        $em->flush();

        return $this->redirectToRoute('app_wish_list');
    }


}
