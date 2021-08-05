<?php

namespace App\Repository;

use App\Dto\SearchRequestDTO;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    // /**
    //  * @return News[] Returns an array of News objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?News
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findWithParams(SearchRequestDTO $params): array
    {

        $qb = $this->createQueryBuilder('n');
        if ($params->limit) {
            $qb->setMaxResults($params->limit);
            $qb->setFirstResult($params->offset);
        }
        if ($params->order_by) {
            $qb->orderBy("n.$params->order_by", $params->order_dir);
        }
        if ($params->date_start) {
            $qb->andWhere("n.publishedAt >= :date_start")
                ->andWhere("n.publishedAt <= :date_end")
                ->setParameter(":date_start", $params->date_start->format('Y-m-d 00:00:00'))
                ->setParameter(":date_end", $params->date_end->format('Y-m-d 23:59:59'));
        }
        if ($params->ids) {
            $qb->andWhere("n.id IN(:ids)")
                ->setParameter(":ids", $params->ids);
        }

        return $qb->getQuery()->getResult();
    }

    // With builder, function requires bundle
    public function findCountsByDates(\DateTime $date_start, \DateTime $date_end): array
    {
        $qb = $this->createQueryBuilder('n')
            ->select('DATE(n.publishedAt) as date, COUNT(n.id) AS count')
            ->where("n.publishedAt >= :date_start")
            ->andWhere("n.publishedAt <= :date_end")
            ->setParameter(":date_start", $date_start->format('Y-m-d 00:00:00'))
            ->setParameter(":date_end", $date_end->format('Y-m-d 23:59:59'))
            ->groupBy("date");

        return $qb->getQuery()->getResult();
    }

    // With native query, no bundles required
    public function findCountsByDates2(\DateTime $date_start, \DateTime $date_end): array
    {
        $em = $this->getEntityManager();
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'string');
        $rsm->addScalarResult('count', 'count', 'integer');
        $sql = "SELECT DATE(published_at) AS `date`, COUNT(*) AS `count`
          FROM `news`
          WHERE published_at >= ? AND published_at <= ?
          GROUP BY `date`";
        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $date_start->format('Y-m-d 00:00:00'));
        $query->setParameter(2, $date_end->format('Y-m-d 23:59:59'));

        return $query->getResult();
    }
}
