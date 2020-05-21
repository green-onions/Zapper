<?php
// src/Controller/ZapperController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/zapper", name="zapper_")
 */
class ZapperController extends AbstractController
{
    /**
     * @Route("/zapper", name="index")
     */
    public function index() :Response
    {
        return $this->render('zapper/index.html.twig', [
            'website' => 'Zapper Séries',
        ]);
    }

    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœ0-9-]+"}, utf8=true, name="show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug = ''): Response
    {
        $seriesTitle = ucwords(str_replace('-', ' ', $slug));
        return $this->render('zapper/show.html.twig', [
            'title' => $seriesTitle,
        ]);
    }
}