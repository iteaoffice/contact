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

namespace Contact\Service;

use Contact\Entity;
use Contact\Repository;

/**
 * Class SelectionService
 *
 * @package Contact\Service
 */
class SelectionService extends ServiceAbstract
{

    /**
     * @param $id
     *
     * @return null|Entity\Selection|object
     */
    public function findSelectionById($id): ?Entity\Selection
    {
        return $this->getEntityManager()->getRepository(Entity\Selection::class)->find($id);
    }

    /**
     * This function returns all the SQL selections
     *
     * @return array|Entity\Selection[]
     */
    public function findSqlSelections(): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Selection::class);

        return $repository->findSqlSelections();
    }

    /**
     * This function returns all the SQL selections
     *
     * @return array|Entity\Selection[]
     */
    public function findNonSqlSelections(): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Selection::class);

        return $repository->findNonSqlSelections();
    }

    /**
     * @param Entity\Selection $selection
     *
     * @return bool
     */
    public function isSql(Entity\Selection $selection): bool
    {
        return !is_null($selection->getSql());
    }

    /**
     * @param Entity\Selection $selection
     *
     * @return int
     */
    public function getAmountOfContacts(Entity\Selection $selection): ?int
    {
        /** @var \Contact\Repository\Contact $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Contact::class);

        return $repository->findAmountOfContactsInSelection($selection);
    }

    /**
     * @return array
     */
    public function findTags(): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Selection::class);

        return $repository->findTags();
    }

    /**
     * Selections can be fixed (via the selection_contact) or dynamic (via de SQL).
     *
     * @param Entity\Contact $contact
     *
     * @return Entity\Selection[]
     */
    public function findSelectionsByContact(Entity\Contact $contact): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->getEntityManager()->getRepository(Entity\Selection::class);

        $selections = $repository->findFixedSelectionsByContact($contact);

        /**
         * @var $selection Entity\Selection
         */
        foreach ($this->findAll(Entity\Selection::class) as $selection) {
            /**
             * Skip the deleted selections and the ones the user is in
             */
            if (!is_null($selection->getSql()) && $this->getContactService()->contactInSelection($contact, $selection)
            ) {
                $selections[] = $selection;
            }
        }

        /*
         * Fill the array with keys to enable sorting
         */
        $result = array_combine($selections, $selections);
        ksort($result);

        return $result;
    }

    /**
     * @param Entity\Selection $selection
     * @param array $data
     *
     * array (size=5)
     * 'type' => string '2' (length=1)
     * 'added' => string '20388' (length=5)
     * 'removed' => string '' (length=0)
     * 'sql' => string '' (length=0)
     *
     *
     */
    public function updateSelectionContacts(Entity\Selection $selection, array $data): void
    {
        /**
         * First update the selection based on the type
         */
        if ((int)$data['type'] === Entity\Selection::TYPE_FIXED) {
            //remove the query
            if (!is_null($sql = $selection->getSql())) {
                $this->removeEntity($sql);
            }

            //Update the contacts
            if (!empty($data['added'])) {
                foreach (explode(',', $data['added']) as $contactId) {
                    $contact = $this->getContactService()->findContactById($contactId);

                    if (!$contact->isEmpty()) {
                        $this->addContactToSelection($selection, $contact);
                    }
                }
            }

            //Update the contacts
            if (!empty($data['removed'])) {
                foreach (explode(',', $data['removed']) as $contactId) {
                    foreach ($selection->getSelectionContact() as $selectionContact) {
                        if ($selectionContact->getContact()->getId() === (int)$contactId) {
                            $this->removeEntity($selectionContact);
                        }
                    }
                }
            }
        } else {
            $selectionSql = $selection->getSql();
            if (is_null($selectionSql)) {
                $selectionSql = new Entity\SelectionSql();
                $selectionSql->setSelection($selection);
            }
            $selectionSql->setQuery($data['sql']);
            $this->updateEntity($selectionSql);
        }
    }

    /**
     * @param Entity\Selection $selection
     * @param Entity\Contact $contact
     */
    public function addContactToSelection(Entity\Selection $selection, Entity\Contact $contact): void
    {
        if (!$this->getContactService()->contactInSelection($contact, $selection)) {
            $selectionContact = new Entity\SelectionContact();
            $selectionContact->setContact($contact);
            $selectionContact->setSelection($selection);
            $this->newEntity($selectionContact);
        }
    }
}
