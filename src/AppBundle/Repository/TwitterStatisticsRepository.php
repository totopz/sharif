<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TwitterStatisticsRepository extends EntityRepository
{
    /**
     * Get statics data
     *
     * @param \DateTime $startData
     * @param \DateTime $endDate
     * @return array
     */
    public function getStatistics(\DateTime $startData, \DateTime $endDate)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.createdAt >= :startDate')
            ->setParameter('startDate', $startData)
            ->andWhere('s.createdAt <= :endDate')
            ->setParameter('endDate', $endDate)
            ->orderBy('s.createdAt', 'ASC')
            ->getQuery()
            ->getScalarResult();
    }
}
