<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Project
 * @package     Options
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    CommunityOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * Trigger to see if the community consists of members
     *
     * @var bool
     */
    protected $communityViaMembers = false;
    /**
     * Bool to see if a project is an EU project
     *
     * @var bool
     */
    protected $communityViaProjectParticipation = false;

    /**
     * @param boolean $communityViaMembers
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaMembers($communityViaMembers)
    {
        $this->communityViaMembers = $communityViaMembers;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCommunityViaMembers()
    {
        return $this->communityViaMembers;
    }

    /**
     * @param boolean $communityViaProjectParticipation
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaProjectParticipation($communityViaProjectParticipation)
    {
        $this->communityViaProjectParticipation = $communityViaProjectParticipation;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getCommunityViaProjectParticipation()
    {
        return $this->communityViaProjectParticipation;
    }
}