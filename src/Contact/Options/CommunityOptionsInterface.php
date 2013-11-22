<?php
/**
 * Japaveh Webdesign copyright message placeholder
 *
 * @category    Contact
 * @package     Options
 * @author      Johan van der Heide <info@japaveh.nl>
 * @copyright   Copyright (c) 2004-2013 Japaveh Webdesign (http://japaveh.nl)
 */
namespace Contact\Options;

/**
 * Interface CommunityOptionsInterface
 * @package Contact\Options
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
     * @return boolean
     */
    public function getCommunityViaMembers();

    /**
     * @param $communityViaProjectParticipation
     *
     * @return CommunityOptionsInterface
     */
    public function setCommunityViaProjectParticipation($communityViaProjectParticipation);

    /**
     * @return boolean
     */
    public function getCommunityViaProjectParticipation();
}
