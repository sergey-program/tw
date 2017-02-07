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
    public function indexAction($hashtag = null, $page)
    {
//        var_dump($this->getRequest()->get('hashtag'));

        /** @var TweetRepository $rTweet */
        $rTweet = $this->getDoctrine()->getRepository('TwitterBundle:Tweet');

        $limit = 2;
        $tweetsPaginator = $rTweet->getPaginated($page, $limit, $hashtag);
        $maxPage = ceil($tweetsPaginator->count() / $limit);
        $curPage = $page;

        $topRetweeted = $rTweet->getTopRetweeted($limit, 10, $hashtag);

        return $this->render('TwitterBundle:Default:index.html.twig', [
            'tweets' => $tweetsPaginator,
            'topRetweeted' => $topRetweeted,
            'maxPage' => $maxPage,
            'curPage' => $curPage
        ]);
    }
}
