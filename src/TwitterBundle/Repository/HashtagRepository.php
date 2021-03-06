<?php

namespace TwitterBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class HashtagRepository
 *
 * @package TwitterBundle\Repository
 */
class HashtagRepository extends EntityRepository
{
    /**
     * @param int $tweetID
     *
     * @return mixed
     */
    public function deleteByTweetID($tweetID)
    {
        return $this
            ->createQueryBuilder('s')
            ->delete()
            ->where('s.tweet_id = :tweetID')
            ->setParameter('tweetID', $tweetID)
            ->getQuery()
            ->execute();
    }
}
