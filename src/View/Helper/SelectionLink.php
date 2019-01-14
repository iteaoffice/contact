<?php

/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\Contact;
use Contact\Entity\Selection;

/**
 * Create a link to an selection.
 *
 * @category    Selection
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

        /*
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        $this->setAlternativeShow($page);
        if (!\is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        }

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

    /**
     * @throws \Exception
     */
    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/selection/new');
                $this->setText($this->translate('txt-new-selection'));
                break;
            case 'list':
                $this->setRouter('zfcadmin/selection/list');
                $this->setText($this->translate('txt-list-selections'));

                foreach ($this->getServiceManager()->get('application')->getMvcEvent()->getRequest()->getQuery() as $key => $param) {
                    $this->addQueryParam($key, $param);
                }
                $this->addQueryParam('page', $this->getPage());

                break;
            case 'edit':
                $this->setRouter('zfcadmin/selection/edit');
                $this->setText(
                    sprintf(
                        $this->translate("txt-edit-selection-%s"),
                        $this->getSelection()->getSelection()
                    )
                );
                break;
            case 'generate-deeplinks':
                $this->setRouter('zfcadmin/selection/generate-deeplinks');
                $this->setText(
                    sprintf(
                        $this->translate("txt-generate-deeplinks")
                    )
                );
                break;
            case 'add-contact':
                if (\is_null($this->getContact())) {
                    throw new \Exception('Contact cannot be empty when adding a contact to a selection');
                }

                $this->setRouter('zfcadmin/selection/add-contact');
                $this->setText(
                    sprintf(
                        $this->translate("txt-add-%s-to-selection"),
                        $this->getContact()->getDisplayName()
                    )
                );
                break;
            case 'edit-contacts':
                $this->setRouter('zfcadmin/selection/edit-contacts');
                $this->setText(
                    sprintf(
                        $this->translate('txt-edit-contacts-selection-%s'),
                        $this->getSelection()->getSelection()
                    )
                );
                break;
            case 'export-csv':
                $this->setRouter('zfcadmin/selection/export');
                $this->addRouterParam('type', 'csv');
                $this->setText(
                    sprintf(
                        $this->translate('txt-export-selection-to-%s-csv'),
                        $this->getSelection()->getSelection()
                    )
                );
                break;
            case 'export-excel':
                $this->setRouter('zfcadmin/selection/export');
                $this->addRouterParam('type', 'excel');
                $this->setText(
                    sprintf(
                        $this->translate('txt-export-selection-to-%s-excel'),
                        $this->getSelection()->getSelection()
                    )
                );
                break;
            case 'view':
                $this->setRouter('zfcadmin/selection/view');
                $this->setText(
                    sprintf(
                        $this->translate('txt-view-selection-%s'),
                        $this->getSelection()->getSelection()
                    )
                );
                break;
            default:
                throw new \Exception(sprintf('%s is an incorrect action for %s', $this->getAction(), __CLASS__));
        }
    }
}
