<?php

namespace App\Controller\Storage;

use App\Entity\Storage\Storage;
use App\Entity\Storage\Version;
use App\Form\Storage\FileType;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VersionsController
 *
 * @Route("/storage")
 * @package App\Controller\Storage
 */
class VersionsController extends AbstractController
{
    /**
     * @Route("/versions/{uuid}/index", name="storage:versions:index", methods="GET")
     *
     * @param Storage $storage
     *
     * @return Response
     */
    public function index(Storage $storage): Response
    {
        $query = $this->getDoctrine()
            ->getRepository(Version::class)
            ->search($storage);

        return $this->render('storage/versions/_index.html.twig', [
            'storage' => $storage,
            'rows' => new Paginator($query)
        ]);
    }

    /**
     * @Route("/versions/{uuid}/upload", name="storage:versions:upload", methods="GET")
     *
     * @return Response
     */
    public function upload(): Response
    {
        return new Response();
    }

    /**
     * @Route("/versions/{uuid}/edit", name="storage:versions:edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Version $version
     *
     * @return Response
     */
    public function edit(Request $request, Version $version): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'This @Route can be accessed via AJAX-request only.');
        }

        $file = $version->getFile();

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        $response = new Response();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($file);
                $manager->flush();

                return $response;
            }

            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('storage/versions/edit.html.twig', [
            'form' => $form->createView(),
            'version' => $version
        ], $response);
    }

    /**
     * @Route("/versions/{uuid}/activate", name="storage:versions:activate", methods="GET")
     *
     * @return Response
     */
    public function activate(): Response
    {
        return new Response();
    }

    /**
     * @Route("/versions/{uuid}/download", name="storage:versions:download", methods="GET")
     *
     * @return Response
     */
    public function download(): Response
    {
        return new Response();
    }

    /**
     * @Route("/versions/{uuid}/delete", name="storage:versions:delete", methods="GET")
     *
     * @return Response
     */
    public function delete(): Response
    {
        return new Response();
    }
}
