<?php

namespace Main\TweetsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainTweetsBundle:Default:index.html.twig');
    }
}
