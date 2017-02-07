<?php

namespace TwitterBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tweet")
 * @ORM\Entity(repositoryClass="TwitterBundle\Repository\TweetRepository")
 */
class Tweet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="created_at", type="bigint", options={"unsigned"=true})
     */
    private $created_at;

    /**
     * @var string
     * @ORM\Column(name="id_str", type="string")
     */
    private $id_str;

    /**
     * @var string
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var int
     * @ORM\Column(name="retweet_count", type="integer")
     */
    private $retweet_count;

    /**
     * @var int
     * @ORM\Column(name="favorited", type="integer")
     */
    private $favorited;

    /**
     * @ORM\OneToMany(targetEntity="Hashtag", mappedBy="tweet", cascade={"persist"})
     */
    private $hashtags;

    /**
     * Tweet constructor.
     */
    public function __construct()
    {
        $this->hashtags = new ArrayCollection();
    }

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
     * Set created_at
     *
     * @param integer $createdAt
     *
     * @return Tweet
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set id_str
     *
     * @param string $idStr
     *
     * @return Tweet
     */
    public function setIdStr($idStr)
    {
        $this->id_str = $idStr;

        return $this;
    }

    /**
     * Get id_str
     *
     * @return string
     */
    public function getIdStr()
    {
        return $this->id_str;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Tweet
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set retweet_count
     *
     * @param integer $retweetCount
     *
     * @return Tweet
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweet_count = $retweetCount;

        return $this;
    }

    /**
     * Get retweet_count
     *
     * @return integer
     */
    public function getRetweetCount()
    {
        return $this->retweet_count;
    }

    /**
     * Set favorited
     *
     * @param integer $favorited
     *
     * @return Tweet
     */
    public function setFavorited($favorited)
    {
        $this->favorited = $favorited;

        return $this;
    }

    /**
     * Get favorited
     *
     * @return integer
     */
    public function getFavorited()
    {
        return $this->favorited;
    }

    /**
     * Add hashtags
     *
     * @param \TwitterBundle\Entity\Hashtag $hashtags
     *
     * @return Tweet
     */
    public function addHashtag(\TwitterBundle\Entity\Hashtag $hashtags)
    {
        $this->hashtags[] = $hashtags;

        return $this;
    }

    /**
     * Remove hashtags
     *
     * @param \TwitterBundle\Entity\Hashtag $hashtags
     */
    public function removeHashtag(\TwitterBundle\Entity\Hashtag $hashtags)
    {
        $this->hashtags->removeElement($hashtags);
    }

    /**
     * Get hashtags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHashtags()
    {
        return $this->hashtags;
    }
}
