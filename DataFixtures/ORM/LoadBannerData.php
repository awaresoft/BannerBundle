<?php

namespace Awaresoft\BannerBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Awaresoft\BannerBundle\Entity\Banner;
use Awaresoft\SettingBundle\Entity\SettingHasFields;
use Doctrine\Common\Persistence\ObjectManager;
use Application\UserBundle\Entity\Setting;

/**
 * Class LoadBannerData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadBannerData extends AwaresoftAbstractFixture
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
        return array('dev', 'prod');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->loadSettings($manager);
    }

    protected function loadSettings(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting
            ->setName('BANNER')
            ->setEnabled(false)
            ->setHidden(true)
            ->setInfo('Banner global parameters.');
        $manager->persist($setting);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('MAX_DEPTH');
        $settingField->setValue('1');
        $settingField->setInfo('Set max depth for banner items. If you want to specific max depth for selected banner, please add option MAX_DEPTH_[BANNER_NAME]');
        $settingField->setEnabled(false);
        $manager->persist($settingField);

        $manager->flush();
    }
}
