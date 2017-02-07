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
     * @param int $limit
     * @param int $days
     *
     * @return array
     */
    public function getTopRetweeted($limit = 10, $days = 10)
    {
        return $this->createQueryBuilder('t')
            ->where('t.created_at >= :created_at')
            ->setParameter('created_at', time() - (60 * 60 * 24 * $days))
            ->orderBy('t.retweet_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return Paginator
     */
    public function getPaginated($page = 1, $limit = 10)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->orderBy('t.created_at', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
