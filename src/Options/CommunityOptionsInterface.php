<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */
namespace Contact\Options;

/**
 * Interface CommunityOptionsInterface.
 */
interface CommunityOptionsInterface
{
    /**
     * @param $communityViaMembers
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaMembers($communityViaMembers);

    /**
     * @return bool
     */
    public function getCommunityViaMembers();

    /**
     * @param $communityViaProjectParticipation
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaProjectParticipation($communityViaProjectParticipation);

    /**
     * @return bool
     */
    public function getCommunityViaProjectParticipation();

    /**
     * @return string
     */
    public function getFacebookTemplate();

    /**
     * @param $facebookTemplate
     *
     * @return string
     */
    public function setFacebookTemplate(string $facebookTemplate);
}
