<?php

namespace TwitterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request     $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($hashtag = null, $page, Request $request)
    {
        /** @var TweetRepository $rTweet */
        $rTweet = $this->getDoctrine()->getRepository('TwitterBundle:Tweet');

        $tpp = $this->getParameter('tweets_per_page');

        // create filter array for pagination and top tweet column
        $filter = $this->getSearchFilter($request, $hashtag);

        $tweetsPaginator = $rTweet->getPaginated($page, $tpp, $filter);
        $maxPage = ceil($tweetsPaginator->count() / $tpp);

        $ttc = $this->getParameter('tweets_top_count');
        $ttd = $this->getParameter('tweets_top_days');

        $topRetweeted = $rTweet->getTopRetweeted($ttc, $ttd, $filter);

        return $this->render('TwitterBundle:Default:index.html.twig', [
            'tweets' => $tweetsPaginator,
            'topRetweeted' => $topRetweeted,
            'maxPage' => $maxPage,
            'curPage' => $page
        ]);
    }

    /**
     * Prepare filter array, used in pagination and top tweets column.
     *
     * @param Request     $request
     * @param string|null $hashtag
     *
     * @return array
     */
    private function getSearchFilter(Request $request, $hashtag)
    {
        // base return
        $result = ['hashtags' => [], 'searchString' => null];
        // if hashtag in url
        if ($hashtag) {
            $result['hashtags'][] = $hashtag;
        }

        // detect search string\hashtag, search param is not routed
        $searchString = $request->query->get('search');

        if (is_int(strpos($searchString, '#'))) {
            $result['hashtags'][] = str_replace('#', '', $searchString);
        } else {
            $result['searchString'] = $searchString;
        }

        return $result;
    }
}
