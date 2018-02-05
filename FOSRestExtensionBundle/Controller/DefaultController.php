<?php

namespace SC\FOSRestExtensionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BetsBundle\Entity\Questions;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        var_dump($this->getParameter("test"));
        return $this->render('SCFOSRestExtensionBundle:Default:index.html.twig');
    }
}
