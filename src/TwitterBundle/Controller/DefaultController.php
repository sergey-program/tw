<?php

namespace TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TwitterBundle\Repository\TweetRepository;

/**
 * Class DefaultController
 *
 * @package TwitterBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @param null|int    $page
     * @param null|string $hashtag
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page, $hashtag = null)
    {
        /** @var TweetRepository $rTweet */
        $rTweet = $this->getDoctrine()->getRepository('TwitterBundle:Tweet');

        $limit = 10;
        $tweetsPaginator = $rTweet->getPaginated($page, $limit);
        $maxPage = ceil($tweetsPaginator->count() / $limit);
        $curPage = $page;

        $topRetweeted = $rTweet->getTopRetweeted();

        return $this->render('TwitterBundle:Default:index.html.twig', [
            'tweets' => $tweetsPaginator,
            'topRetweeted' => $topRetweeted,
            'maxPage' => $maxPage,
            'curPage' => $curPage
        ]);
    }
}
