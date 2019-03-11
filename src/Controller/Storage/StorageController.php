<?php

namespace App\Controller\Storage;

use App\Entity\Storage as Storage;
use App\Form\Storage\StorageType;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StorageController
 *
 * @Route("/storage")
 * @package App\Controller\Storage
 */
class StorageController extends AbstractController
{
    /**
     * @Route("/", name="storage:root", methods="GET")
     *
     * @return Http\Response
     */
    public function root(): Http\Response
    {
        $nodes = $this->getDoctrine()->getRepository(Storage\Tree::class)->getRootNodes();

        if (is_array($nodes) && count($nodes) > 0) {
            return $this->redirectToRoute('storage:index', ['uuid' => array_shift($nodes)->getUuid()]);
        }

        throw new NotFoundHttpException('Invalid root node');
    }

    /**
     * @Route("/{uuid}/{page<\d+>?1}", name="storage:index", methods={"GET"})
     *
     * @param Http\Request $request
     * @param string $uuid
     * @param int $page
     *
     * @return Http\Response
     */
    public function index(Http\Request $request, string $uuid, int $page): Http\Response
    {
        $repository = $this->getDoctrine()->getRepository(Storage\Tree::class);

        /* @var Storage\Tree $node */
        $node = $repository->findOneBy(['uuid' => $uuid]);

        $viewFile = "storage/storage/index.html.twig";
        if ($request->isXmlHttpRequest()) {
            $viewFile = "storage/storage/_index.html.twig";
        }

        return $this->render($viewFile, [
            'node' => $node,
            'rows' => new Paginator($repository->search($node, $request), $page),
            'path' => $repository->getPath($node),
        ]);
    }


    /**
     * @Route("/{uuid}/edit", name="storage:edit", methods={"GET","POST"})
     *
     * @param Http\Request $request
     * @param Storage\Storage $storage
     *
     * @return Http\Response
     */
    public function edit(Http\Request $request, Storage\Storage $storage): Http\Response
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->getRepository(Storage\Tree::class)->setTreeNode($storage);

            if ($form->get('apply')->isClicked()) {
                return $this->redirectToRoute('storage:edit', ['uuid' => $storage->getUuid()]);
            }

            return $this->redirectToRoute('storage:index', ['uuid' => $storage->getParent()->getUuid()]);
        }

        return $this->render('storage/storage/edit.html.twig', [
            'storage' => $storage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{uuid}/access", name="storage:access", methods="GET")
     *
     * @return Http\Response
     */
    public function access(): Http\Response
    {
        return new Http\Response();
    }

    /**
     * @Route("/{uuid}/download", name="storage:download", methods="GET")
     *
     * @return Http\Response
     */
    public function download(): Http\Response
    {
        return new Http\Response();
    }

    /**
     * @Route("/{uuid}/delete", name="storage:delete", methods="DELETE")
     *
     * @return Http\Response
     */
    public function delete(): Http\Response
    {
        return new Http\Response();
    }

    /**
     * @Route("/{uuid}/upload", name="storage:upload", methods={"PUT"})
     *
     * @param Http\Request $request
     * @param Storage\Tree $root
     *
     * @return Http\JsonResponse
     */
    public function upload(Http\Request $request, Storage\Tree $root): Http\JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'This @Route can be accessed via AJAX-Request only.');
        }

        $response = json_decode($request->getContent(), true);

        // Preparing File entity
        $file = new Storage\File([
            'name' => preg_replace('/\.[\d]{10,}$/', '', $response['name']), // Cut off lastModified part
            'size' => (int) $response['size'],
            'type' => $response['type'],
            'hash' => $response['hash'],
        ]);

        // Preparing Storage entity
        $storage = new Storage\Storage([
            'title' => $response['name'],
            'type' => Storage\Storage::STORAGE_TYPE_FILE,
            '+version' => $file
        ]);

        $node = new Storage\Tree([
            'parent' => $root,
            'storage' => $storage
        ]);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($node);
        $manager->flush();

        return Http\JsonResponse::create(['uuid' => $file->getUuid(), 'name' => $response['name']]);
    }

    /**
     * @Route("/{uuid}/new", name="storage:new", methods="POST")
     *
     * @return Http\Response
     */
    public function new(): Http\Response
    {
        return new Http\Response();
    }
}
