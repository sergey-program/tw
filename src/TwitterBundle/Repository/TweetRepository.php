<?php

namespace TwitterBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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

        $query = $this->addFilterConditions($query, $filter);

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
        $query = $this->addFilterConditions($this->createQueryBuilder('t'), $filter);
        $query->orderBy('t.created_at', 'DESC')->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()->setFirstResult($limit * ($page - 1))->setMaxResults($limit);

        return $paginator;
    }

    /**
     * @param QueryBuilder $query
     * @param array        $filter
     *
     * @return QueryBuilder
     */
    private function addFilterConditions($query, $filter)
    {
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

        return $query;
    }

    /**
     * @param int $limit
     * @param int $lastDays
     *
     * @return array
     */
    public function getTweetsForUpdate($limit, $lastDays)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->orderBy('t.updated_at', 'DESC')
            ->setMaxResults($limit);

        if (is_numeric($lastDays)) {
            $query
                ->where('t.created_at >= :created_at')
                ->setParameter('created_at', time() - (60 * 60 * 24 * $lastDays));
        }

        return $query->getQuery()->getResult();
    }
}
