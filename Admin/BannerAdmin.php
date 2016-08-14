<?php

namespace Awaresoft\BannerBundle\Admin;

use Awaresoft\Sonata\PageBundle\Entity\PageRepository;
use Awaresoft\TreeBundle\Admin\AbstractTreeAdmin;
use Awaresoft\BannerBundle\Entity\Banner;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Class BannerAdmin
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BannerAdmin extends AbstractTreeAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/banner/banner';

    /**
     * @inheritdoc
     */
    protected $multisite = true;

    /**
     * @inheritdoc
     */
    protected $titleField = 'title';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
    }

    /**
     * @inheritdoc
     *
     * @param Menu $object
     */
    public function prePersist($object)
    {
        if ($object->getParent() && $object->getParent()->getSite() !== $object->getSite()) {
            $object->setSite($object->getParent()->getSite());
        }
    }

    /**
     * @inheritdoc
     *
     * @param Menu $object
     */
    public function preUpdate($object)
    {
        $this->prePersist($object);
    }

    /**
     * @param Banner $object
     *
     * @return void
     */
    public function postUpdate($object)
    {
        $object->prepareUrl();
    }

    /**
     * @param Banner $object
     *
     * @return void
     */
    public function postPersist($object)
    {
        $this->postUpdate($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('title')
            ->add('site')
            ->add('description')
            ->add('textColor', null, [
                'label' => $this->trans('admin.admin.label.text_color'),
                'template' => 'SonataAdminBundle:CRUD:show_color.html.twig',
            ])
            ->add('page', null, [
                'admin_code' => 'awaresoft.page.admin.cms',
            ])
            ->add('url', 'url')
            ->add('externalUrl', 'boolean')
            ->add('enabled')
            ->add('deletable')
            ->add('class')
            ->end();

        $showMapper
            ->with($this->trans('admin.admin.form.group.media'))
            ->add('media', 'html', [
                'template' => 'SonataAdminBundle:CRUD:show_image.html.twig',
            ])
            ->add('bgColor', null, [
                'label' => $this->trans('admin.admin.label.bg_color'),
                'template' => 'SonataAdminBundle:CRUD:show_color.html.twig',
            ])
            ->end();
    }

    protected function configureListFieldsExtend(ListMapper $listMapper)
    {
        $listMapper
            ->add('site')
            ->add('media', 'html', ['template' => 'SonataAdminBundle:CRUD:list_image.html.twig'])
            ->add('url', 'url')
            ->add('externalUrl', 'boolean')
            ->add('enabled', null, ['editable' => true]);

        $editable = false;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $editable = true;
        }

        $listMapper
            ->add('deletable', null, ['editable' => $editable]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->prepareFilterMultisite($datagridMapper);

        $datagridMapper
            ->add('title')
            ->add('parent')
            ->add('description')
            ->add('page', null, [], null, null, [
                'admin_code' => 'awaresoft.page.admin.cms',
            ])
            ->add('externalUrl')
            ->add('enabled')
            ->add('deletable');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /**
         * @var Banner $object
         */
        $object = $this->getSubject();
        $maxDepthLevel = $this->prepareMaxDepthLevel('BANNER');

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.url'), ['class' => 'col-md-6'])->end()
            ->with($this->trans('admin.admin.form.group.media'), ['class' => 'col-md-6'])->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('title')
            ->add('description', null, [
                'required' => false,
            ])
            ->add('textColor', 'sonata_type_color_selector', [
                'required' => false,
                'label' => $this->trans('admin.admin.label.text_color'),
            ])
            ->add('enabled', null, [
                'required' => false,
            ]);

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->add('deletable', null, [
                    'required' => false,
                ]);
        }

        if ($this->hasSubject() && !$object->getId()) {
            $formMapper
                ->add('site', null, ['required' => true, 'read_only' => true]);
        }

        $this->addParentField($formMapper, $maxDepthLevel, $object->getSite());

        $formMapper
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.url'))
            ->add('page', 'entity', [
                'class' => 'AwaresoftSonataPageBundle:Page',
                'choice_label' => 'name',
                'label' => $this->trans('admin.admin.label.redirect_to_page'),
                'required' => false,
                'query_builder' => function (PageRepository $pr) {
                    return $pr->findCmsPages();
                },
            ], [
                'admin_code' => 'awaresoft.page.admin.cms',
            ])
            ->add('externalUrl', 'text', [
                'required' => false,
            ])
            ->add('url', 'url', [
                'attr' => [
                    'readonly' => true,
                ],
                'required' => false,
            ])
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.media'))
            ->add('media', 'sonata_media_type', [
                'cascade_validation' => true,
                'provider' => 'sonata.media.provider.image',
                'context' => 'banner',
                'label' => $this->trans('admin.admin.label.image'),
                'required' => false,
            ])
            ->add('bgColor', 'sonata_type_color_selector', [
                'required' => false,
                'label' => $this->trans('admin.admin.label.bg_color'),
            ])
            ->end();

        $formMapper->setHelps([
            'url' => $this->trans('admin.admin.help.url_page_or_plaintext'),
            'title' => $this->trans('banner.admin.help.title'),
            'description' => $this->trans('banner.admin.help.description'),
            'media' => $this->trans('banner.admin.help.media_info'),
        ]);
    }
}
