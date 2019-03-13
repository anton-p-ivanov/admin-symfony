<?php

namespace App\Controller\Storage;

use App\Annotation\AjaxRequest;
use App\Entity\Storage as Storage;
use App\Form\ConfirmType;
use App\Form\Storage\FileType;
use App\Service\DeleteService;
use App\Service\DownloadService;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation as Http;
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
     * @AjaxRequest()
     *
     * @param Storage\Storage $storage
     *
     * @return Http\Response
     */
    public function index(Storage\Storage $storage): Http\Response
    {
        $query = $this->getDoctrine()
            ->getRepository(Storage\Version::class)
            ->search($storage);

        return $this->render('storage/versions/_index.html.twig', [
            'storage' => $storage,
            'rows' => new Paginator($query)
        ]);
    }

    /**
     * @Route("/versions/{uuid}/upload", name="storage:versions:upload", methods={"PUT"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Storage $storage
     *
     * @return Http\JsonResponse
     */
    public function upload(Http\Request $request, Storage\Storage $storage): Http\JsonResponse
    {
        $response = json_decode($request->getContent(), true);

        // Preparing File entity
        $file = new Storage\File([
            'name' => preg_replace('/\.[\d]{10,}$/', '', $response['name']), // Cut off lastModified part
            'size' => (int) $response['size'],
            'type' => $response['type'],
            'hash' => $response['hash'],
        ]);

        $storage->addVersion($file);
        $storage->setUpdatedAt((new \DateTime())->getTimestamp());

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($storage);
        $manager->flush();

        return Http\JsonResponse::create([
            'uuid' => $file->getUuid(),
            'name' => $response['name'],
            'url' => $this->generateUrl('storage:versions:index', ['uuid' => $storage->getUuid()]),
            'container' => 'versions'
        ]);
    }

    /**
     * @Route("/versions/{uuid}/edit", name="storage:versions:edit", methods={"GET","POST"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Version $version
     *
     * @return Http\Response
     */
    public function edit(Http\Request $request, Storage\Version $version): Http\Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'This @Route can be accessed via AJAX-request only.');
        }

        $file = $version->getFile();

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        $response = new Http\Response();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($file);
                $manager->flush();

                return $response;
            }

            $response->setStatusCode(Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('storage/versions/edit.html.twig', [
            'form' => $form->createView(),
            'version' => $version
        ], $response);
    }

    /**
     * @Route("/versions/{uuid}/activate", name="storage:versions:activate", methods="GET")
     * @AjaxRequest()
     *
     * @param Storage\Version $version
     *
     * @return Http\Response
     */
    public function activate(Storage\Version $version): Http\Response
    {
        // Set active state
        $version->setIsActive(true);

        // Set update date & time
        $version->getStorage()->setUpdatedAt((new \DateTime())->getTimestamp());
        $version->getFile()->setUpdatedAt((new \DateTime())->getTimestamp());

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($version);
        $manager->flush();

        return $this->index($version->getStorage());
    }

    /**
     * @Route("/versions/{uuid}/download", name="storage:versions:download", methods="GET")
     * @AjaxRequest()
     *
     * @param Storage\Version $version
     * @param DownloadService $service
     *
     * @return Http\Response
     */
    public function download(Storage\Version $version, DownloadService $service): Http\Response
    {
        return $service->download($version->getFile());
    }

    /**
     * @Route("/versions/{uuid}/delete", name="storage:versions:delete", methods={"GET", "DELETE"})
     * @AjaxRequest()
     *
     * @param Http\Request $request
     * @param Storage\Version $version
     * @param DeleteService $service
     *
     * @return Http\Response
     */
    public function delete(Http\Request $request, Storage\Version $version, DeleteService $service): Http\Response
    {
        if ($version->isActive()) {
            throw new HttpException(Http\Response::HTTP_BAD_REQUEST, 'Active version could not be deleted.');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'action' => $this->generateUrl('storage:versions:delete', ['uuid' => $version->getUuid()]),
            'method' => 'DELETE',
            'attr' => [
                'data-url' => $this->generateUrl('storage:versions:index', ['uuid' => $version->getStorage()->getUuid()]),
                'data-container' => '#versions'
            ]
        ]);

        $form->handleRequest($request);
        $response = new Http\Response();

        if ($form->isSubmitted()) {
            if ($form->isValid() && $service->delete($version)) {
                return $response;
            }

            $response->setStatusCode(Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('confirm.html.twig', [
            'form' => $form->createView()
        ], $response);
    }
}
