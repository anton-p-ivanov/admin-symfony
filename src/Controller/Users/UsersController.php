<?php

namespace App\Controller\Users;

use App\Annotation\AjaxRequest;
use App\Entity\User\User;
use App\Service\User\UserService;
use App\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController
 *
 * @Route("/users")
 * @package App\Controller\Users
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="users:index", methods={"GET"})
     *
     * @param Http\Request $request
     * @param int $page
     *
     * @return Http\Response
     */
    public function index(Http\Request $request, int $page): Http\Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $viewFile = "users/users/index.html.twig";
        if ($request->isXmlHttpRequest()) {
            $viewFile = "users/users/_index.html.twig";
        }

        return $this->render($viewFile, [
            'rows' => new Paginator($repository->search($request->get('search')), $page),
        ]);
    }

    /**
     * @Route("/add", name="users:add", methods={"GET","POST"})
     *
     * @param UserService $service
     *
     * @return Http\Response
     */
    public function add(UserService $service): Http\Response
    {
        return $service->process('/users/users/add.html.twig', new User());
    }

    /**
     * @Route("/{uuid}/edit", name="users:edit", methods={"GET","POST"})
     *
     * @param User $user
     * @param UserService $service
     *
     * @return Http\Response
     */
    public function edit(User $user, UserService $service): Http\Response
    {
        return $service->process('/users/users/edit.html.twig', $user);
    }

    /**
     * @Route("/{uuid}/copy", name="users:copy", methods={"GET","POST"})
     *
     * @param User $user
     * @param UserService $service
     *
     * @return Http\Response
     */
    public function copy(User $user, UserService $service): Http\Response
    {
        return $service->process('/users/users/copy.html.twig', clone $user);
    }

    /**
     * @Route("/{uuid?batch}/delete", name="users:delete", methods={"GET","DELETE"})
     * @AjaxRequest()
     *
     * @return Http\Response
     */
    public function delete(): Http\Response
    {
        return new Http\Response();
    }
}
