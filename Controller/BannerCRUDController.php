<?php

namespace Awaresoft\BannerBundle\Controller;

use Awaresoft\Sonata\AdminBundle\Controller\CRUDController as AwaresoftCRUDController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\PageBlockType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;

class BannerCRUDController extends AwaresoftCRUDController
{
    use ControllerHelperTrait;

    /**
     * @inheritdoc
     */
    public function preDeleteAction($object)
    {
        $message = $this->checkObjectIsBlocked($object, $this->admin);
        $message .= $this->checkObjectHasRelations($object, $this->admin, [
            new PageBlockType($this->container, $object, 'awaresoft.banner.block.banner', 'collection'),
        ]);

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function batchActionDeleteIsRelevant(array $idx)
    {
        $message = null;

        foreach ($idx as $id) {
            $object = $this->admin->getObject($id);
            $message = $this->checkObjectIsBlocked($object, $this->admin);
            $message .= $this->checkObjectHasRelations($object, $this->admin, [
                new PageBlockType($this->container, $object, 'awaresoft.banner.block.banner', 'collection'),
            ]);
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
