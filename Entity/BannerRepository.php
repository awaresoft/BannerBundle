<?php

namespace Awaresoft\BannerBundle\Entity;

use Awaresoft\Sonata\PageBundle\Entity\Site;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class BannerRepository
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BannerRepository extends NestedTreeRepository
{
    /**
     * @return Banner[]
     */
    public function findAllEnabled()
    {
        return $this->findBy(['enabled' => 1], ['left' => 'ASC']);
    }

    /**
     * @param Site $site
     *
     * @return Banner
     */
    public function queryTreeRoots($site = null)
    {
        $qb = $this
            ->createQueryBuilder('b')
            ->where('b.level = :level')
            ->andWhere('b.enabled = :enabled')
            ->setParameter('level', 0)
            ->setParameter('enabled', true);

        if ($site) {
            $qb
                ->andWhere('b.site = :site')
                ->setParameter('site', $site);
        }

        return $qb;
    }
}