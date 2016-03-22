<?php

/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
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
     * @return mixed
     */
    public function getFacebookTemplate();

    /**
     * @param $facebookTemplate
     * @return mixed
     */
    public function setFacebookTemplate($facebookTemplate);
}
