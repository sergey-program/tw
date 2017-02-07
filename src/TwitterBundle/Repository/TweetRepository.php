<?php

namespace TwitterBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class TweetRepository
 *
 * @package TwitterBundle\Repository
 */
class TweetRepository extends EntityRepository
{
    /**
     * @param int         $limit
     * @param int         $days
     * @param null|string $hashtag
     *
     * @return array
     */
    public function getTopRetweeted($limit = 10, $days = 10, $hashtag = null)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->where('t.created_at >= :created_at')
            ->setParameter('created_at', time() - (60 * 60 * 24 * $days))
            ->orderBy('t.retweet_count', 'DESC')
            ->setMaxResults($limit);

        if ($hashtag) {
            $query
                ->leftJoin('t.hashtags', 'h')
                ->andWhere('h.hashtag = :hashtag')
                ->setParameter('hashtag', $hashtag);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int         $page
     * @param int         $limit
     * @param null|string $hashtag
     *
     * @return Paginator
     */
    public function getPaginated($page = 1, $limit = 10, $hashtag = null)
    {
        $query = $this->createQueryBuilder('t');

        if ($hashtag) {
            $query
                ->leftJoin('t.hashtags', 'h')
                ->where('h.hashtag = :hashtag')
                ->setParameter('hashtag', $hashtag);
        }

        $query
            ->orderBy('t.created_at', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()->setFirstResult($limit * ($page - 1))->setMaxResults($limit);

        return $paginator;
    }
}
