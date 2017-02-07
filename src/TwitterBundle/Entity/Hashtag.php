<?php

namespace TwitterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hashtag")
 * @ORM\Entity(repositoryClass="TwitterBundle\Repository\HashtagRepository")
 */
class Hashtag
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="tweet_id", type="integer")
     */
    private $tweet_id;

    /**
     * @var string
     * @ORM\Column(name="hashtag", type="string")
     */
    private $hashtag;

    /**
     * @var Tweet
     * @ORM\ManyToOne(targetEntity="Tweet", inversedBy="hashtags")
     * @ORM\JoinColumn(name="tweet_id", referencedColumnName="id")
     */
    private $tweet;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tweet_id
     *
     * @param integer $tweetId
     *
     * @return Hashtag
     */
    public function setTweetId($tweetId)
    {
        $this->tweet_id = $tweetId;

        return $this;
    }

    /**
     * Get tweet_id
     *
     * @return integer
     */
    public function getTweetId()
    {
        return $this->tweet_id;
    }

    /**
     * Set hashtag
     *
     * @param string $hashtag
     *
     * @return Hashtag
     */
    public function setHashtag($hashtag)
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    /**
     * Get hashtag
     *
     * @return string
     */
    public function getHashtag()
    {
        return $this->hashtag;
    }

    /**
     * Set tweet
     *
     * @param \TwitterBundle\Entity\Tweet $tweet
     *
     * @return Hashtag
     */
    public function setTweet(\TwitterBundle\Entity\Tweet $tweet = null)
    {
        $this->tweet = $tweet;

        return $this;
    }

    /**
     * Get tweet
     *
     * @return \TwitterBundle\Entity\Tweet
     */
    public function getTweet()
    {
        return $this->tweet;
    }
}
