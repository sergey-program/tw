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
        /** @var TweetRepository $rTweet */
        $rTweet = $this->getDoctrine()->getRepository('TwitterBundle:Tweet');

        $tpp = $this->getParameter('tweets_per_page');

        $tweetsPaginator = $rTweet->getPaginated($page, $tpp, $hashtag);
        $maxPage = ceil($tweetsPaginator->count() / $tpp);
        $curPage = $page;

        $ttc = $this->getParameter('tweets_top_count');
        $ttd = $this->getParameter('tweets_top_days');

        $topRetweeted = $rTweet->getTopRetweeted($ttc, $ttd, $hashtag);

        return $this->render('TwitterBundle:Default:index.html.twig', [
            'tweets' => $tweetsPaginator,
            'topRetweeted' => $topRetweeted,
            'maxPage' => $maxPage,
            'curPage' => $curPage
        ]);
    }
}
