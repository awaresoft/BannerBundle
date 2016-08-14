<?php
namespace Awaresoft\BannerBundle\Entity;

use Awaresoft\Sonata\MediaBundle\Entity\Media;
use Awaresoft\Sonata\PageBundle\Entity\Page;
use Awaresoft\Sonata\PageBundle\Entity\Site;
use Awaresoft\TreeBundle\Entity\AbstractTreeNode;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 * @Gedmo\Tree(type="nested")
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Banner extends AbstractTreeNode
{
    const TREE_MAIN_COLUMN = 'title';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="tree_left")
     *
     * @Gedmo\TreeLeft
     *
     * @var int
     */
    protected $left;

    /**
     * @ORM\Column(type="integer", name="tree_right")
     *
     * @Gedmo\TreeRight
     *
     * @var int
     */
    protected $right;

    /**
     * @ORM\ManyToOne(targetEntity="Banner", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Gedmo\TreeParent
     *
     * @var Banner
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", nullable=true, name="tree_root")
     *
     * @Gedmo\TreeRoot
     *
     * @var Banner
     */
    protected $root;

    /**
     * @ORM\Column(name="tree_level", type="integer")
     *
     * @Gedmo\TreeLevel
     *
     * @var int
     */
    protected $level;

    /**
     * @ORM\OneToMany(targetEntity="Banner", mappedBy="parent")
     * @ORM\OrderBy({"left" = "ASC"})
     *
     * @var Banner[]
     */
    protected $children;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $description;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $externalUrl;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\PageBundle\Entity\Page")
     *
     * @var Page
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\PageBundle\Entity\Site")
     *
     * @var Site
     */
    protected $site;

    /**
     * @var string
     */
    protected $url;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\MediaBundle\Entity\Media", cascade={"persist", "remove"}, fetch="LAZY")
     *
     * @var Media
     */
    protected $media;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     *
     * @var string
     */
    protected $textColor;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     *
     * @var string
     */
    protected $bgColor;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $deletable;

    public function __construct()
    {
        $this->url = null;
        $this->enabled = true;
        $this->deletable = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param $left
     *
     * @return $this
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     *
     * @return $this
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * @return Banner
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Banner $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Banner
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param Banner $root
     *
     * @return $this
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Banner[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     *
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->page) {
            return $this->page->getUrl();
        }

        return $this->externalUrl;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return Banner
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param MediaInterface $media
     *
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * @param string $textColor
     *
     * @return $this
     */
    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;

        return $this;
    }

    /**
     * @return string
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * @param string $bgColor
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @param bool $deletable
     *
     * @return $this
     */
    public function setDeletable($deletable)
    {
        $this->deletable = $deletable;

        return $this;
    }
    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * @param string $externalUrl
     *
     * @return $this
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Check if page and externalUrl are set
     */
    public function prepareUrl()
    {
        if($this->page && $this->externalUrl) {
            $this->externalUrl = null;
        }
    }

    /**
     * @return string
     */
    public function getTitleFieldName()
    {
        return self::TREE_MAIN_COLUMN;
    }
}