<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Form\User\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Router;

/**
 * Class UserService
 *
 * @package App\Service\User
 */
class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * DeleteEntity constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param Http\RequestStack $requestStack
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        Http\RequestStack $requestStack
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;

        $this->request = $requestStack->getCurrentRequest();
        if (!$this->request) {
            throw new HttpException(Http\Response::HTTP_BAD_REQUEST, 'Could not get current request from stack.');
        }
    }

    /**
     * @param string $view
     * @param User $user
     *
     * @return Http\Response
     */
    public function process(string $view, User $user): Http\Response
    {
        /* @var \Symfony\Component\Form\Form $form */
        $form = $this->container->get('form.factory')->create(UserType::class, $user);
        $form->handleRequest($this->request);

        $isNewElement = $user->getUuid() === null;

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->entityManager;
            $manager->persist($user);
            $manager->flush();

            $route = $this->getRouter()->generate('users:index');

            if ($form->get('apply')->isClicked()) {
                $flashMessage = $isNewElement ? 'flash.create.success' : 'flash.update.success';
                $this->getSession()->getFlashBag()->add('success', $flashMessage);

                $route = $this->getRouter()->generate('users:edit', ['uuid' => $user->getUuid()]);
            }

            return Http\RedirectResponse::create($route);
        }

        return $this->render($view, [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $view
     * @param array $params
     *
     * @return Http\Response
     */
    protected function render(string $view, array $params = []): Http\Response
    {
        return Http\Response::create($this->container->get('twig')->render($view, $params));
    }

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return $this->container->get('session');
    }

    /**
     * @return Router
     */
    protected function getRouter(): Router
    {
        return $this->container->get('router');
    }
}