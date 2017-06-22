<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
declare(strict_types=1);

namespace Contact\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements CommunityOptionsInterface
{
    /**
     * Turn off strict options mode.
     */
    protected $__strictMode__ = false;
    /**
     * Trigger to see if the community consists of members.
     *
     * @var bool
     */
    protected $communityViaMembers = false;
    /**
     * Bool to see if a project is an EU project.
     *
     * @var bool
     */
    protected $communityViaProjectParticipation = false;

    /**
     * Template for rendering of the facebook
     *
     * @var string
     */
    protected $facebookTemplate = 'contact/facebook/facebook';

    /**
     * @return bool
     */
    public function getCommunityViaMembers()
    {
        return $this->communityViaMembers;
    }

    /**
     * @param bool $communityViaMembers
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaMembers($communityViaMembers)
    {
        $this->communityViaMembers = $communityViaMembers;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCommunityViaProjectParticipation()
    {
        return $this->communityViaProjectParticipation;
    }

    /**
     * @param bool $communityViaProjectParticipation
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaProjectParticipation($communityViaProjectParticipation)
    {
        $this->communityViaProjectParticipation = $communityViaProjectParticipation;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookTemplate(): string
    {
        return $this->facebookTemplate;
    }

    /**
     * @param string $facebookTemplate
     *
     * @return $this
     */
    public function setFacebookTemplate(string $facebookTemplate)
    {
        $this->facebookTemplate = $facebookTemplate;

        return $this;
    }
}
