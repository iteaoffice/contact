<?php

/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Thumbor\Url\Builder;
use Zend\View\Helper\Url;

/**
 * Class LinkAbstract.
 */
abstract class ImageAbstract extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected $router;
    /**
     * @var string
     */
    protected $imageId;
    /**
     * @var array List of parameters needed to construct the URL from the router
     */
    protected $routerParams = [];
    /**
     * @var array Classes to be given to the link
     */
    protected $classes = [];
    /**
     * @var bool
     */
    protected $lightBox = false;
    /**
     * @var int|int
     */
    protected $width;
    /**
     * @var int|null
     */
    protected $height;
    /**
     * @var array
     */
    protected $filter = [];

    public function createImageUrl(bool $onlyUrl = false): string
    {
        $url = $this->getHelperPluginManager()->get(Url::class);

        //Grab the ServerURL from the config to avoid problems with CLI code
        $serverUrl = $this->getServiceManager()->get("Config")['deeplink']['serverUrl'];

        $config = $this->getServiceManager()->get('content_module_config');

        $thumberLink = Builder::construct(
            $config['image']['server'],
            $config['image']['secret'],
            $serverUrl . $url($this->router, $this->routerParams)
        )
            ->resize($this->width, $this->height)
            ->halign('center')
            ->smartCrop(false);

        foreach ($this->filter as $filter) {
            $thumberLink->addFilter($filter);
        }

        $imageUrl = '<img src="%s" id="%s" class="%s">';

        $image = sprintf(
            $imageUrl,
            $thumberLink,
            $this->imageId,
            implode(' ', $this->classes)
        );

        if ($onlyUrl) {
            return (string)$thumberLink;
        }

        if (!$this->lightBox) {
            return $image;
        }


        $thumberLinkFull = Builder::construct(
            $config['image']['server'],
            $config['image']['secret'],
            $serverUrl . $url($this->router, $this->routerParams)
        );


        return '<a href="' . $thumberLinkFull . '" class="thumbnail fancybox-thumbs" data-fancybox-group="album-6">'
            . $image . '</a>';
    }

    public function addRouterParam($key, $value, $allowNull = true): void
    {
        if (!$allowNull && null === $value) {
            throw new \InvalidArgumentException(sprintf('null is not allowed for %s', $key));
        }
        if (null !== $value) {
            $this->routerParams[$key] = $value;
        }
    }

    public function addFilter(string $filter): void
    {
        $this->filter[] = $filter;
    }


    public function getRouter():?string
    {
        return $this->router;
    }

    public function setRouter(string $router): void
    {
        $this->router = $router;
    }

    public function getRouterParams(): array
    {
        return $this->routerParams;
    }

    public function getImageId()
    {
        return $this->imageId;
    }

    public function setImageId($imageId): void
    {
        $this->imageId = $imageId;
    }

    public function addClasses($classes): ImageAbstract
    {
        foreach ((array)$classes as $class) {
            $this->classes[] = $class;
        }

        return $this;
    }

    public function setLightBox(bool $lightBox): void
    {
        $this->lightBox = $lightBox;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth($width): ImageAbstract
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight():?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): ImageAbstract
    {
        $this->height = $height;
        return $this;
    }
}
