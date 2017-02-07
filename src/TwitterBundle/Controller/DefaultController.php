<?php

namespace TwitterBundle\Controller;

use Endroid\Twitter\Twitter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TwitterBundle\Entity\Hashtag;
use TwitterBundle\Entity\Tweet;
use TwitterBundle\Repository\HashtagRepository;
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
        /** @var Twitter $api */
        $api = $this->get('endroid.twitter');
        $apiResponse = $api->query('statuses/user_timeline', 'GET', 'json', ['screen_name' => 'qslipper_ru']); // bbcrussian
        $apiTweets = json_decode($apiResponse->getContent());

        $em = $this->getDoctrine()->getManager();

        /** @var TweetRepository $rTweet */
        $rTweet = $this->getDoctrine()->getRepository('TwitterBundle:Tweet');
        /** @var HashtagRepository $rHashtag */
        $rHashtag = $this->getDoctrine()->getRepository('TwitterBundle:Hashtag');

        foreach ($apiTweets as $apiTweet) {
            $tweet = $rTweet->findOneBy(['id_str' => $apiTweet->id_str]);
            $tweet = $tweet ? $tweet : new Tweet();

            $tweet
                ->setIdStr($apiTweet->id_str)
                ->setText($apiTweet->text)
                ->setCreatedAt(strtotime($apiTweet->created_at))
                ->setFavorited($apiTweet->favorited ? $apiTweet->favorited : 0)
                ->setRetweetCount($apiTweet->retweet_count ? $apiTweet->retweet_count : 0);

            $em->persist($tweet);
            $em->flush();

            $rHashtag->deleteByTweetID($tweet->getId());

            // insert new\updated if exists
            if (isset($apiTweet->entities->hashtags) && !empty($apiTweet->entities->hashtags)) {
                foreach ($apiTweet->entities->hashtags as $apiHashtag) {
                    $hashtag = new Hashtag();
                    $hashtag->setHashtag($apiHashtag->text)->setTweet($tweet);

                    $em->persist($hashtag);
                    $em->flush();
                }
            }
        }

        $limit = 5;
        $tweetsPaginator = $rTweet->getPaginated($page, $limit);
        $maxPages = ceil($tweetsPaginator->count() / $limit);
        $thisPage = $page;

        $topRetweeted = $rTweet->getTopRetweeted();

        return $this->render('TwitterBundle:Default:index.html.twig', [
            'tweets' => $tweetsPaginator,
            'topRetweeted' => $topRetweeted,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
}
