<?php

namespace Awaresoft\BannerBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Awaresoft\BannerBundle\Entity\Banner;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadBannerDevData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadBannerDevData extends AwaresoftAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 13;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return array('dev');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->createBanners($manager);
    }

    protected function createBanners(ObjectManager $manager)
    {
        $faker = $this->getFaker();
        $media1 = $this->getReference('sonata-media-1');
        $media2 = $this->getReference('sonata-media-2');

        $root = new Banner();
        $root
            ->setEnabled(true)
            ->setTitle('root')
            ->setSite($this->getReference('page-site'));

        $manager->persist($root);

        $banner = new Banner();
        $banner
            ->setEnabled(true)
            ->setDescription($faker->text(255))
            ->setTitle($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($root)
            ->setMedia($media1);

        $manager->persist($banner);

        $banner2 = new Banner();
        $banner2
            ->setEnabled(true)
            ->setDescription($faker->text(255))
            ->setTitle($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($banner)
            ->setMedia($media2);

        $manager->persist($banner2);

        $banner3 = new Banner();
        $banner3
            ->setEnabled(true)
            ->setDescription($faker->text(255))
            ->setTitle($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($banner2)
            ->setMedia($media1);

        $manager->persist($banner3);

        $banner4 = new Banner();
        $banner4
            ->setEnabled(true)
            ->setDescription($faker->text(255))
            ->setTitle($faker->realText(20))
            ->setSite($this->getReference('page-site'))
            ->setParent($root)
            ->setMedia($media2);

        $manager->persist($banner4);

        $manager->flush();
    }
}
