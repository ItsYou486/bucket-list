<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function home(): Response
    {
        $tableauLieux = [
            'chine'=> 'Voir la Grande Muraille de Chine,',
            'champagne'=> 'Boire du Champagne dans une limousine',
            'italie'=> 'Sauter en parachute'
        ];

        return $this->render('main/index.html.twig', [
            'tableau_lieux'=> $tableauLieux

        ]);
    }

    #[Route('/aboutus', name: 'app_aboutus')]
    public function abousUs(): Response
    {

        return $this->render('main/aboutus.html.twig');
    }
}
