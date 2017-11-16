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

namespace Contact\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Contact\Entity\Contact;
use Contact\Entity\Note;
use Zend\Navigation\Page\Mvc;

/**
 * Class NoteLabel
 *
 * @package Note\Navigation\Invokable
 */
class NoteLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     */
    public function __invoke(Mvc $page): void
    {
        $label = $this->translate('txt-nav-note');

        if ($this->getEntities()->containsKey(Note::class)) {
            /** @var Note $note */
            $note = $this->getEntities()->get(Note::class);

            $this->getEntities()->set(Contact::class, $note->getContact());

            $page->setParams(
                array_merge(
                    $page->getParams(),
                    [
                        'id' => $note->getId(),
                    ]
                )
            );
            $label = sprintf("%s", $note->getNote());
        }
        $page->set('label', $label);
    }
}
