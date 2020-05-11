<?php
// src/Controller/ZapperController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZapperController extends AbstractController
{
    /**
     * @Route("/zapper", name="zapper_index")
     */
    public function index() :Response
    {
        return $this->render('zapper/index.html.twig', [
            'website' => 'Zapper Séries',
        ]);
    }
}