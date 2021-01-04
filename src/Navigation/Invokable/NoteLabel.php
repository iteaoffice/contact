<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Navigation\Invokable;

use Contact\Entity\Contact;
use Contact\Entity\Note;
use General\Navigation\Invokable\AbstractNavigationInvokable;
use Laminas\Navigation\Page\Mvc;

/**
 * Class NoteLabel
 *
 * @package Note\Navigation\Invokable
 */
final class NoteLabel extends AbstractNavigationInvokable
{
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
