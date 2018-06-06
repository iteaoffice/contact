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
use Doctrine\ORM\EntityManager;

/**
 * Class SelectionService
 *
 * @package Contact\Service
 */
class SelectionService extends AbstractService
{
    /**
     * @var ContactService
     */
    private $contactService;
    /**
     * @var SelectionContactService
     */
    private $selectionContactService;

    public function __construct(
        EntityManager $entityManager,
        ContactService $contactService,
        SelectionContactService $selectionContactService
    ) {
        parent::__construct($entityManager);

        $this->contactService = $contactService;
        $this->selectionContactService = $selectionContactService;
    }

    public function findSelectionById(int $id): ?Entity\Selection
    {
        return $this->entityManager->getRepository(Entity\Selection::class)->find($id);
    }

    public function findSqlSelections(): array
    {
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

        return $repository->findSqlSelections();
    }

    public function findNonSqlSelections(): array
    {
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

        return $repository->findNonSqlSelections();
    }

    public function isSql(Entity\Selection $selection): bool
    {
        return null !== $selection->getSql();
    }

    public function getAmountOfContacts(Entity\Selection $selection): int
    {
        $repository = $this->entityManager->getRepository(Entity\Contact::class);

        return $repository->findAmountOfContactsInSelection($selection);
    }

    public function findTags(): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

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
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

        $selections = $repository->findFixedSelectionsByContact($contact);

        /**
         * @var $selection Entity\Selection
         */
        foreach ($this->findAll(Entity\Selection::class) as $selection) {
            /**
             * Skip the deleted selections and the ones the user is in
             */
            if (null !== $selection->getSql()
                && $this->selectionContactService->contactInSelection(
                    $contact,
                    $selection
                )
            ) {
                $selections[] = $selection;
            }
        }

        $result = array_combine($selections, $selections);
        ksort($result);

        return $result;
    }

    public function updateSelectionContacts(Entity\Selection $selection, array $data): void
    {
        /**
         * First update the selection based on the type
         */
        if ((int)$data['type'] === Entity\Selection::TYPE_FIXED) {
            //remove the query
            if (null !== ($sql = $selection->getSql())) {
                $this->delete($sql);
            }

            //Update the contacts
            if (!empty($data['added'])) {
                foreach (explode(',', $data['added']) as $contactId) {
                    $contact = $this->contactService->findContactById($contactId);

                    if (null !== $contact) {
                        $this->addContactToSelection($selection, $contact);
                    }
                }
            }

            //Update the contacts
            if (!empty($data['removed'])) {
                foreach (explode(',', $data['removed']) as $contactId) {
                    foreach ($selection->getSelectionContact() as $selectionContact) {
                        if ($selectionContact->getContact()->getId() === (int)$contactId) {
                            $this->delete($selectionContact);
                        }
                    }
                }
            }
        } else {
            $selectionSql = $selection->getSql();
            if (null === $selectionSql) {
                $selectionSql = new Entity\SelectionSql();
                $selectionSql->setSelection($selection);
            }
            $selectionSql->setQuery($data['sql']);
            $this->save($selectionSql);
        }
    }

    public function addContactToSelection(Entity\Selection $selection, Entity\Contact $contact): void
    {
        if (!$this->selectionContactService->contactInSelection($contact, $selection)) {
            $selectionContact = new Entity\SelectionContact();
            $selectionContact->setContact($contact);
            $selectionContact->setSelection($selection);
            $this->save($selectionContact);
        }
    }
}
