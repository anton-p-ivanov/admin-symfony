<?php

namespace App\Controller\Storage;

use App\Annotation\AjaxRequest;
use App\Entity\Storage as Storage;
use App\Form\ConfirmType;
use App\Form\AccessType;
use App\Form\Storage\StorageType;
use App\Service\AccessService;
use App\Service\DeleteService;
use App\Service\DownloadService;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation as Http;
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
            'uploaderUrl' => getenv('UPLOADER_URL')
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
            $manager->persist($storage);

            $repository = $manager->getRepository(Storage\Tree::class);
            if ($repository->isNodeChanged($storage)) {
                $repository->persistAsFirstChildOf($storage->getNode(), $storage->getParent());
            }

            $manager->flush();

            if ($form->get('apply')->isClicked()) {
                $this->addFlash('success', 'flash.update.success');
                return $this->redirectToRoute('storage:edit', ['uuid' => $storage->getUuid()]);
            }

            return $this->redirectToRoute('storage:index', ['uuid' => $storage->getParent()->getUuid()]);
        }

        return $this->render('storage/storage/edit.html.twig', [
            'storage' => $storage,
            'form' => $form->createView(),
            'uploaderUrl' => getenv('UPLOADER_URL'),
        ]);
    }

    /**
     * @Route("/{uuid}/access", name="storage:access", methods={"GET","POST"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Tree $tree
     * @param AccessService $service
     *
     * @return Http\Response
     */
    public function access(Http\Request $request, Storage\Tree $tree, AccessService $service): Http\Response
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(AccessType::class, null, [
            'action' => $this->generateUrl('storage:access', ['uuid' => $tree->getUuid()]),
            'attr' => [
                'data-container' => '#spreadsheet',
                'data-url' => $this->generateUrl('storage:index', ['uuid' => $tree->getUuid()]),
            ]
        ]);

        $form->handleRequest($request);

        $response = new Http\Response();

        if ($form->isSubmitted()) {
            if ($form->isValid() && $service->apply($form)) {
                return $response;
            }

            $response->setStatusCode(Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('access.html.twig', [
            'tree' => $tree,
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * @Route("/{uuid}/download", name="storage:download", methods="GET")
     *
     * @param Storage\Storage $storage
     * @param DownloadService $service
     *
     * @return Http\Response
     */
    public function download(Storage\Storage $storage, DownloadService $service): Http\Response
    {
        return $service->download($storage->getFile());
    }

    /**
     * @Route("/{uuid}/delete", name="storage:delete", methods={"GET","DELETE"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Tree $node
     * @param DeleteService $service
     *
     * @return Http\Response
     */
    public function delete(Http\Request $request, Storage\Tree $node, DeleteService $service): Http\Response
    {
        $form = $this->createForm(ConfirmType::class, null, [
            'action' => $this->generateUrl('storage:delete', ['uuid' => $node->getUuid(), 'batch' => $request->get('batch')]),
            'method' => 'DELETE',
            'attr' => [
                'data-url' => $this->generateUrl('storage:index', ['uuid' => $request->get('batch') ? $node->getUuid() : $node->getParent()->getUuid()]),
                'data-container' => '#spreadsheet'
            ]
        ]);

        $form->handleRequest($request);
        $response = new Http\Response();

        if ($form->isSubmitted()) {
            $nodes = [$node->getStorage()];

            if ($request->get('batch')) {
                $items = json_decode($form->get('items')->getNormData(), true);

                /* @var Storage\Tree[] $nodes */
                $nodes = $this->getDoctrine()
                    ->getRepository(Storage\Tree::class)
                    ->findBy(['uuid' => $items]);

                if (!$nodes) {
                    $form->addError(new FormError('form.errors.invalid_items'));
                }

                $nodes = array_map(function (Storage\Tree $node) { return $node->getStorage(); }, $nodes);
            }

            if ($form->isValid() && $service->delete($nodes)) {
                return $response;
            }

            $response->setStatusCode(Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('confirm.html.twig', [
            'form' => $form->createView()
        ], $response);
    }

    /**
     * @Route("/{uuid}/upload", name="storage:upload", methods={"PUT"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Tree $parent
     *
     * @return Http\JsonResponse
     */
    public function upload(Http\Request $request, Storage\Tree $parent): Http\JsonResponse
    {
        $response = json_decode($request->getContent(), true);

        // Preparing File entity
        $file = new Storage\File([
            'name' => preg_replace('/\.[\d]{10,}$/', '', $response['name']), // Cut off lastModified part
            'size' => (int) $response['size'],
            'type' => $response['type'],
            'hash' => $response['hash'],
        ]);

        // Preparing Storage entity
        $donor = $parent->getStorage();
        $storage = new Storage\Storage([
            'title' => $response['name'],
            'type' => Storage\Storage::STORAGE_TYPE_FILE,
            'categories' => $donor->getCategories(),
            'roles' => $donor->getRoles(),
            '+version' => $file,
            'node' => new Storage\Tree([
                'parent' => $parent,
            ])
        ]);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($storage);
        $manager->flush();

        return Http\JsonResponse::create([
            'uuid' => $file->getUuid(),
            'name' => $response['name'],
            'url' => $this->generateUrl('storage:index', ['uuid' => $parent->getUuid()]),
            'container' => 'spreadsheet'
        ]);
    }

    /**
     * @Route("/{uuid}/new", name="storage:new", methods={"GET","POST"})
     *
     * @param Http\Request $request
     * @param Storage\Tree $parent
     *
     * @return Http\Response
     */
    public function new(Http\Request $request, Storage\Tree $parent): Http\Response
    {
        $donor = $parent->getStorage();
        $storage = new Storage\Storage([
            'title' => 'New folder',
            'roles' => $donor->getRoles(),
            'categories' => $donor->getCategories(),
            'node' => new Storage\Tree([
                'parent' => $parent
            ])
        ]);

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($storage);
            $manager->flush();

            if ($form->get('apply')->isClicked()) {
                $this->addFlash('success', 'flash.create.success');
                return $this->redirectToRoute('storage:edit', ['uuid' => $storage->getUuid()]);
            }

            return $this->redirectToRoute('storage:index', ['uuid' => $storage->getParent()->getUuid()]);
        }

        return $this->render('storage/storage/new.html.twig', [
            'storage' => $storage,
            'form' => $form->createView(),
        ]);
    }
}
