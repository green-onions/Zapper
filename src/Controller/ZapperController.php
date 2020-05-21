<?php
// src/Controller/ZapperController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/zapper", name="zapper_")
 */
class ZapperController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        return $this->render('zapper/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœ0-9-]+"}, defaults={"slug" = null}, utf8=true, name="show")
     * @param string $slug
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }

        $slug = preg_replace('/-/', ' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException('No program with '.$slug.' title, found in program\'s table.');
        }

        return $this->render('zapper/show.html.twig', [
            'program' => $program,
            'slug'    => $slug
        ]);
    }

    /**
     * @Route("/category/{categoryName}", requirements={"categoryName"="[a-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœ0-9-]+"}, defaults={"categoryName" = null}, utf8=true, name="category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory (string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $programsInCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );

        return $this->render('zapper/category.html.twig', [
            'category' => $category,
            'programs' => $programsInCategory
        ]);
    }
}