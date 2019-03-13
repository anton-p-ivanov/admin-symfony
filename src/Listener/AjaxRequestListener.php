<?php

namespace App\Listener;

use App\Annotation\AjaxRequest;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AjaxRequestListener
 * @package App\Listener
 */
class AjaxRequestListener
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        if (!is_array($controllers = $event->getController())) {
            return;
        }

        $request = $event->getRequest();

        list($controller, $methodName) = $controllers;

        $reflectionObject = new \ReflectionObject($controller);
        $reflectionMethod = $reflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader
            ->getMethodAnnotation($reflectionMethod, AjaxRequest::class);

        if (!$methodAnnotation) {
            return;
        }

        if (!$request->isXmlHttpRequest()) {
            throw new HttpException(400, 'This route can be accessed via AJAX-Request only.');
        }
    }
}