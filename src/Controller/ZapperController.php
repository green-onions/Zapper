<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
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
    public function index() :Response
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
     * @param string|null $slug
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
     * @Route("/episode/{slug}", defaults={"slug" = null}, name="episode")
     * @param Request $request
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Request $request, Episode $episode): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $season   = $episode->getSeason();
        $program  = $season->getProgram();
        $comments = $episode->getComments();

        foreach ($comments->getValues() as $key => $commentToDelete) {
            if ($this->isCsrfTokenValid('delete' . $commentToDelete->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($commentToDelete);
                $entityManager->flush();
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('zapper_episode', ['slug' => $episode->getSlug()]);
        }

        return $this->render('zapper/episode.html.twig', [
            'form'     => $form->createView(),
            'program'  => $program,
            'season'   => $season,
            'episode'  => $episode,
            'comments' => $comments,
        ]);
    }
}
