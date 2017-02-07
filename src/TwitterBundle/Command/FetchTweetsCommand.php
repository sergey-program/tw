<?php

namespace TwitterBundle\Command;

use Endroid\Twitter\Twitter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TwitterBundle\Entity\Hashtag;
use TwitterBundle\Entity\Tweet;
use TwitterBundle\Repository\HashtagRepository;
use TwitterBundle\Repository\TweetRepository;

/**
 * Class FetchTweetsCommand
 *
 * @package TwitterBundle\Command
 */
class FetchTweetsCommand extends ContainerAwareCommand
{
    /**
     *
     */
    public function configure()
    {
        $this
            ->setName('twitter:fetch-tweets')
            ->setDescription('Fetch latest tweets to DB. To update tweets use another command');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var Twitter $api */
        $api = $container->get('endroid.twitter');
        $apiResponse = $api->query('statuses/user_timeline', 'GET', 'json', ['screen_name' => $container->getParameter('twitter_feed')]);
        $apiTweets = json_decode($apiResponse->getContent());

        $doctrine = $container->get('doctrine');

        $em = $doctrine->getManager();

        /** @var TweetRepository $rTweet */
        $rTweet = $doctrine->getRepository('TwitterBundle:Tweet');
        /** @var HashtagRepository $rHashtag */
        $rHashtag = $doctrine->getRepository('TwitterBundle:Hashtag');

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

            $output->writeln('Tweet #' . $tweet->getId() . ' was updated.');

            $rHashtag->deleteByTweetID($tweet->getId());

            // insert new\updated if exists
            if (isset($apiTweet->entities->hashtags) && !empty($apiTweet->entities->hashtags)) {
                foreach ($apiTweet->entities->hashtags as $apiHashtag) {
                    $hashtag = new Hashtag();
                    $hashtag->setHashtag($apiHashtag->text)->setTweet($tweet);

                    $em->persist($hashtag);
                    $em->flush();

                    $output->writeln('Hashtags for tweet #' . $tweet->getId() . ' was updated.');
                }
            }
        }
    }
}
