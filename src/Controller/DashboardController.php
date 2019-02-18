<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 *
 * @Route("/")
 *
 * @package App\Controller
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard:home")
     * @Template("dashboard/home.html.twig")
     *
     * @return array
     */
    public function home(): array
    {
        return [];
    }
}