<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Form\Profile as Type;
use App\Service\Profile as Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProfileController
 *
 * @Route("/profile")
 *
 * @package App\Controller
 */
class ProfileController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * AccountController constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/login", name="profile:login")
     * @Template("profile/login.html.twig")
     *
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return array
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): array
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $request->get('username', $lastUsername),
            'error' => $error,
        ];
    }

    /**
     * @Route("/register", name="profile:register")
     *
     * @param Request $request
     * @param Profile\RegisterService $service
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function register(
        Request $request,
        Profile\RegisterService $service,
        TranslatorInterface $translator): Response
    {
        $form = $this->createForm(Type\RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service->register($form)) {
                return $this->redirectToRoute('profile:confirm', [
                    'username' => $form->get('username')->getNormData()
                ]);
            }

            $form->get('username')->addError(new FormError($translator->trans('form.register.user_exists')));
        }

        return $this->render('profile/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm", name="profile:confirm")
     *
     * @param Request $request
     * @param Profile\ConfirmService $service
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function confirm(Request $request, Profile\ConfirmService $service): Response
    {
        $form = $this->createForm(Type\ConfirmType::class, $request->query->all());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service->confirm($form)) {
                $this->authorize($service->getUser(), $request);
                return $this->redirectToRoute('dashboard:home');
            }
        }

        return $this->render('profile/confirm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset", name="profile:reset")
     *
     * @param Request $request
     * @param Profile\ResetService $service
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function reset(Request $request, Profile\ResetService $service, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(Type\ResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getNormData();
            if ($service->reset($username)) {
                return $this->redirectToRoute('profile:password', ['username' => $username]);
            }

            $form->get('username')->addError(new FormError($translator->trans('form.reset.invalid_username')));
        }

        return $this->render('profile/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password", name="profile:password")
     *
     * @param Request $request
     * @param Profile\PasswordService $service
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function password(Request $request, Profile\PasswordService $service): Response
    {
        $form = $this->createForm(Type\PasswordType::class, $request->query->all());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($service->update($form)) {
                return $this->redirectToRoute('profile:login', ['username' => $form->get('username')->getNormData()]);
            }
        }

        return $this->render('profile/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     */
    protected function authorize(User $user, Request $request)
    {
        $token = new UsernamePasswordToken($user, null, 'main', ['USER']);
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
    }
}