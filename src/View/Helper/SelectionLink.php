<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Selection;

/**
 * Class SelectionLink
 *
 * @package Contact\View\Helper
 */
class SelectionLink extends LinkAbstract
{
    public function __invoke(
        Selection $selection = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null,
        Contact $contact = null
    ): string {
        $this->setSelection($selection);
        $this->setAction($action);
        $this->setShow($show);
        $this->setPage($page);
        $this->setContact($contact);


        $this->setShowOptions(
            [
                'name' => $this->getSelection()->getSelection(),
            ]
        );
        $this->addRouterParam('page', $page);
        $this->addRouterParam('id', $this->getSelection()->getId());
        $this->addRouterParam('contactId', $this->getContact()->getId());

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/selection/new');
                $this->setText($this->translate('txt-new-selection'));
                break;
            case 'copy':
                $this->setRouter('zfcadmin/selection/copy');
                $this->setText($this->translate('txt-copy-selection'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/selection/list');
                $this->setText($this->translate('txt-list-selections'));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/selection/edit');
                $this->setText($this->translate('txt-edit-selection'));
                break;
            case 'generate-deeplinks':
                $this->setRouter('zfcadmin/selection/generate-deeplinks');
                $this->setText($this->translate('txt-generate-deeplinks'));
                break;
            case 'add-contact':
                $this->setRouter('zfcadmin/selection/add-contact');
                $this->setText(
                    sprintf(
                        $this->translate('txt-add-%s-to-selection'),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'edit-contacts':
                $this->setRouter('zfcadmin/selection/edit-contacts');
                $this->setText($this->translate('txt-edit-contacts-selection'));
                break;
            case 'export-csv':
                $this->setRouter('zfcadmin/selection/export');
                $this->addRouterParam('type', 'csv');
                $this->setText($this->translate('txt-export-selection-to-csv'));
                break;
            case 'export-excel':
                $this->setRouter('zfcadmin/selection/export');
                $this->addRouterParam('type', 'excel');
                $this->setText($this->translate('txt-export-selection-to-excel'));
                break;
            case 'view':
                $this->setRouter('zfcadmin/selection/view');
                $this->setText($this->translate('txt-view-selection'));
                break;
        }
    }
}
