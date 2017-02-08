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
     * @param int   $limit
     * @param int   $days
     * @param array $filter
     *
     * @return array
     */
    public function getTopRetweeted($limit = 10, $days = 10, $filter = [])
    {
        $query = $this
            ->createQueryBuilder('t')
            ->where('t.created_at >= :created_at')
            ->setParameter('created_at', time() - (60 * 60 * 24 * $days))
            ->orderBy('t.retweet_count', 'DESC')
            ->setMaxResults($limit);

        if (isset($filter['hashtags']) && !empty($filter['hashtags']) && is_array($filter['hashtags'])) {
            $query
                ->leftJoin('t.hashtags', 'h')
                ->andWhere('h.hashtag IN (:hashtags)')
                ->setParameter('hashtags', array_values($filter['hashtags']));
        }

        if (isset($filter['searchString']) && !empty($filter['searchString'])) {
            $query
                ->andWhere('t.text LIKE :searchString')
                ->setParameter('searchString', '%' . $filter['searchString'] . '%');
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int   $page
     * @param int   $limit
     * @param array $filter
     *
     * @return Paginator
     */
    public function getPaginated($page = 1, $limit = 10, $filter = [])
    {
        $query = $this->createQueryBuilder('t');

        if (isset($filter['hashtags']) && !empty($filter['hashtags']) && is_array($filter['hashtags'])) {
            $query
                ->leftJoin('t.hashtags', 'h')
                ->where('h.hashtag IN (:hashtags)')
                ->setParameter('hashtags', array_values($filter['hashtags']));
        }

        if (isset($filter['searchString']) && !empty($filter['searchString'])) {
            $query
                ->andWhere('t.text LIKE :searchString')
                ->setParameter('searchString', '%' . $filter['searchString'] . '%');
        }

        $query
            ->orderBy('t.created_at', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()->setFirstResult($limit * ($page - 1))->setMaxResults($limit);

        return $paginator;
    }
}
