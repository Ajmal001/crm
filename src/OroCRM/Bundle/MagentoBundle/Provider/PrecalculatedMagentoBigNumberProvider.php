<?php

namespace OroCRM\Bundle\MagentoBundle\Provider;

use Doctrine\ORM\QueryBuilder;
use OroCRM\Bundle\ChannelBundle\Entity\Channel;

class PrecalculatedMagentoBigNumberProvider extends MagentoBigNumberProvider
{
    use PrecalculatedVisitProviderTrait;

    /**
     * {@inheritdoc}
     */
    protected function getManagerRegistry()
    {
        return $this->doctrine;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAclHelper()
    {
        return $this->aclHelper;
    }

    /**
     * @param array $dateRange
     *
     * @return int
     */
    public function getSiteVisitsValues($dateRange)
    {
        if (!$this->isPrecalculatedStatisticEnabled()) {
            return parent::getSiteVisitsValues($dateRange);
        }

        $queryBuilder = $this->getVisitsCountQueryBuilder($dateRange['start'], $dateRange['end']);

        return $this->getSingleIntegerResult($queryBuilder);
    }

    /**
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return QueryBuilder
     */
    private function getVisitsCountQueryBuilder(\DateTime $from = null, \DateTime $to = null)
    {
        $queryBuilder = $this->createUniqueVisitQueryBuilder();

        $queryBuilder->select('SUM(t.visitCount)')
            ->join('t.trackingWebsite', 'site')
            ->leftJoin('site.channel', 'channel')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull('channel.id'),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('channel.channelType', ':channel'),
                    $queryBuilder->expr()->eq('channel.status', ':status')
                )
            ))
            ->setParameter('channel', ChannelType::TYPE)
            ->setParameter('status', Channel::STATUS_ACTIVE);

        $this->applyDateLimit($queryBuilder, $from, $to);

        return $queryBuilder;
    }
}