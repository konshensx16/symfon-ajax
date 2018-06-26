<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TrelloController extends Controller
{
    /**
     * @Route("/trello", name="trello")
     */
    public function index()
    {
        return $this->render('trello/index.html.twig', [
            'controller_name' => 'TrelloController',
        ]);
    }
}
