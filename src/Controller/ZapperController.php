<?php
// src/Controller/ZapperController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request) :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        return $this->render('zapper/index.html.twig', [
            'programs'  => $programs,
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
     * @Route("/categoryTest/{categoryName}", requirements={"categoryName"="[a-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœ0-9-]+"}, defaults={"categoryName" = null}, utf8=true, name="categoryTest")
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
                ['categoryTest' => $category],
                ['id' => 'DESC'],
                3
            );

        return $this->render('zapper/category.html.twig', [
            'categoryTest' => $category,
            'programs' => $programsInCategory
        ]);
    }

    /**
     * @Route("/{slug}/seasons", requirements={"slug"="[a-záàâäãåçéèêëíìîïñóòôöõúùûüýÿæœ0-9-]+"}, defaults={"slug" = null}, utf8=true, name="seasons")
     * @param string $slug
     * @return Response
     */
    public function showByProgram(string $slug): Response
    {
        $slug = preg_replace('/-/', ' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        $seasonsInProgram = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        return $this->render('zapper/seasons.html.twig', [
            'program' => $program,
            'seasons' => $seasonsInProgram
        ]);
    }

    /**
     * @Route("/season/{id}", defaults={"id" = null}, name="episodes")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this->createNotFoundException('No id has been sent to find a season in season\'s table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);

        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        return $this->render('zapper/episodes.html.twig', [
            'season'   => $season,
            'episodes' => $episodes,
            'program'  => $program
        ]);
    }

    /**
     * @Route("/episode/{id}", defaults={"id" = null}, name="episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {
        $season  = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('zapper/episode.html.twig', [
            'program' => $program,
            'season'  => $season,
            'episode' => $episode
        ]);
    }
}