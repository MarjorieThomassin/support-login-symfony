<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchProgramFormType;

/**
 * @Route("/program", name="program_")
 */

class ProgramController extends AbstractController
{
     /**
 * @Route("/", name="index")
 */
public function index(Request $request, ProgramRepository $programRepository): Response
    {
        $form = $this->createForm(SearchProgramFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
         $programs = $programRepository->findAll();
    }

    return $this->render('program/index.html.twig', [
        'programs' => $programs,
        'form' => $form->createView(),
    ]);
    }

    /**
 * @Route("/{id<^[0-9]+$>}", methods={"GET"}, name="show")
 */
public function show(int $id): Response
{
    $program = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findOneBy(['id' => $id]);
    if (!$program) {
        throw $this->createNotFoundException(
            'No program with id : '.$id.' found in program\'s table.'
        );
    }
    return $this->render('program/show.html.twig', [
        'program' => $program,
    ]);
}

 /**
     * The controller for the program add form
     *
     * @Route("/new", name="new")
     */
    public function new( Request $request,
    EntityManagerInterface $entityManager) : Response
    {
        // Create a new program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Program Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to programs list
            $this->addFlash('success', 'The new program has been created');

            return $this->redirectToRoute('program_index');
        }        
        // Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}