<?php

namespace TwitterBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Endroid\Twitter\Twitter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TwitterBundle\Entity\Tweet;
use TwitterBundle\Repository\TweetRepository;

/**
 * Class UpdateTweetsCommand
 *
 * @package TwitterBundle\Command
 */
class UpdateTweetsCommand extends ContainerAwareCommand
{
    /**
     *
     */
    public function configure()
    {
        $this
            ->setName('twitter:update-tweets')
            ->setDescription('Update N tweets in DB, needs to get latest data to create top list.');
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

        $output->writeln('Tweets limit (per run): ' . $container->getParameter('twitter_update_per_run') . '.');
        $output->writeln('Days limit: ' . $container->getParameter('twitter_update_last_days') . '.');

        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        /** @var TweetRepository $rTweet */
        $rTweet = $doctrine->getRepository('TwitterBundle:Tweet');

        /** @var Tweet[] $tweets */
        $tweets = $rTweet->getTweetsForUpdate(
            $container->getParameter('twitter_update_per_run'),
            $container->getParameter('twitter_update_last_days')
        );
        $output->writeln('Updating ' . count($tweets) . ' tweets.');

        if ($tweets) {
            foreach ($tweets as $tweet) {
                $output->write('Tweet id: ' . $tweet->getIdStr() . '... ');
                /** @var Twitter $api */
                $api = $container->get('endroid.twitter');
                $apiResponse = $api->query('statuses/show', 'GET', 'json', ['id' => $tweet->getIdStr()]);
                $apiTweet = json_decode($apiResponse->getContent());

                $tweet
                    ->setFavorited($apiTweet->favorited ? $apiTweet->favorited : 0)
                    ->setRetweetCount($apiTweet->retweet_count ? $apiTweet->retweet_count : 0)
                    ->setUpdatedAt(time());

                $em->persist($tweet);
                $em->flush();

                $output->writeln('updated.');
            }
        }
    }
}
