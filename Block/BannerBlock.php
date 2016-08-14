<?php

namespace Awaresoft\BannerBundle\Block;

use Awaresoft\Sonata\BlockBundle\Block\BaseBlockService;
use Awaresoft\BannerBundle\Entity\Banner;
use Awaresoft\BannerBundle\Entity\BannerRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\Metadata;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BannerBlock
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BannerBlock extends BaseBlockService
{
    /**
     * Default template
     */
    const DEFAULT_TEMPLATE = 'AwaresoftBannerBundle:Block:block_banner.html.twig';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => self::DEFAULT_TEMPLATE,
            'collection' => null,
        ]);
    }

    /**
     * @param FormMapper $formMapper
     * @param BlockInterface $block
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if (!$block->getSetting('collection') instanceof Banner) {
            $this->load($block);
        }

        $site = $block->getPage() ? $block->getPage()->getSite() : null;

        $formMapper->add('settings', 'sonata_type_immutable_array', [
            'keys' => [
                ['collection', 'entity', [
                    'class' => 'ApplicationBannerBundle:Banner',
                    'required' => true,
                    'query_builder' => function (BannerRepository $r) use ($site) {
                        return $r->queryTreeRoots($site);
                    },
                ]],
                ['template', null, [
                    'attr' => [
                        'placeholder' => self::DEFAULT_TEMPLATE,
                    ],
                    'empty_data' => self::DEFAULT_TEMPLATE,
                ]],
            ],
        ]);
    }

    /**
     * Execute block
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $site = $blockContext->getBlock()->getPage()->getSite();
        $collection = $blockContext->getBlock()->getSetting('collection');

        $banners = $this->getBannerRepository()->findBy([
            'enabled' => true,
            'site' => $site,
            'parent' => $collection,
        ]);

        return $this->renderResponse($blockContext->getTemplate(), [
            'banners' => $banners,
            'bannersCount' => count($banners),
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
        ], $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'SonataBlockBundle', [
            'class' => 'fa fa-newspaper-o',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $block->setSetting('collection', is_object($block->getSetting('collection')) ? $block->getSetting('collection')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $block->setSetting('collection', is_object($block->getSetting('collection')) ? $block->getSetting('collection')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        $collectionId = $block->getSetting('collection', null);

        if (is_int($collectionId)) {
            $collection = $this->getBannerRepository()->find($collectionId);
            $block->setSetting('collection', $collection);
        }
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getBannerRepository()
    {
        return $this->getEntityManager()->getRepository('ApplicationBannerBundle:Banner');
    }
}