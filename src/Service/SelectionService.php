<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Contact\Service;

use Contact\Entity;
use Contact\Repository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;

/**
 * Class SelectionService
 *
 * @package Contact\Service
 */
class SelectionService extends AbstractService
{
    private ContactService $contactService;
    private SelectionContactService $selectionContactService;

    public function __construct(
        EntityManager $entityManager,
        ContactService $contactService,
        SelectionContactService $selectionContactService
    ) {
        parent::__construct($entityManager);

        $this->contactService          = $contactService;
        $this->selectionContactService = $selectionContactService;
    }

    public function canDeleteSelection(Entity\Selection $selection): bool
    {
        $cannotRemoveSelection = [];

        if (! $selection->getMailing()->isEmpty()) {
            $cannotRemoveSelection[] = 'This selection has mailings';
        }

        if (! $selection->getAccess()->isEmpty()) {
            $cannotRemoveSelection[] = 'This selection has access';
        }

        if (! $selection->getMeeting()->isEmpty()) {
            $cannotRemoveSelection[] = 'This selection has meetings';
        }

        if (! $selection->getMeetingCost()->isEmpty()) {
            $cannotRemoveSelection[] = 'This selection has meeting costs';
        }

        if (! $selection->getMeetingOptionCost()->isEmpty()) {
            $cannotRemoveSelection[] = 'This selection has meeting option costs';
        }

        return count($cannotRemoveSelection) === 0;
    }

    public function canDeleteType(Entity\Selection\Type $type): bool
    {
        $cannotDeleteType = [];

        if (! $type->getSelection()->isEmpty()) {
            $cannotDeleteType[] = 'This type has selections';
        }


        return count($cannotDeleteType) === 0;
    }

    public function findSelectionById(int $id): ?Entity\Selection
    {
        return $this->entityManager->getRepository(Entity\Selection::class)->find($id);
    }

    public function findSelectionTypeById(int $id): ?Entity\Selection\Type
    {
        return $this->entityManager->getRepository(Entity\Selection\Type::class)->find($id);
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

    public function getAmountOfContacts(Entity\Selection $selection): int
    {
        try {
            $repository = $this->entityManager->getRepository(Entity\Contact::class);

            return $repository->findAmountOfContactsInSelection($selection);
        } catch (DBALException $e) {
            return 0;
        }
    }

    public function findTags(): array
    {
        /** @var Repository\Selection $repository */
        $repository = $this->entityManager->getRepository(Entity\Selection::class);

        return $repository->findTags();
    }

    public function findTypes(): array
    {
        return $this->entityManager->getRepository(Entity\Selection\Type::class)
            ->findBy([], ['name' => Criteria::ASC]);
    }

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
            if (
                null !== $selection->getSql()
                && $this->selectionContactService->contactInSelection(
                    $contact,
                    $selection
                )
            ) {
                $selections[] = $selection;
            }
        }

        ksort($selections);

        return $selections;
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

            $contacts = (array)($data['contacts'] ?? []);

            //Update the contacts
            foreach ($contacts as $contactId) {
                $contact = $this->contactService->findContactById((int)$contactId);
                if (null !== $contact) {
                    $this->addContactToSelection($selection, $contact);
                }
            }
            //Update the contacts
            foreach ($selection->getSelectionContact() as $selectionContact) {
                if (! in_array($selectionContact->getContact()->getId(), $contacts, false)) {
                    $this->delete($selectionContact);
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
        if (! $this->selectionContactService->contactInSelection($contact, $selection)) {
            $selectionContact = new Entity\SelectionContact();
            $selectionContact->setContact($contact);
            $selectionContact->setSelection($selection);
            $this->save($selectionContact);
        }
    }

    public function duplicateSelection(Entity\Selection $selection, Entity\Selection $source): void
    {
        $this->save($selection);

        //Transfer the contacts
        if ($this->isSql($source)) {
            $sql = new Entity\SelectionSql();
            $sql->setSelection($selection);
            $sql->setQuery($source->getSql()->getQuery());
            $this->save($sql);
        }

        if (! $this->isSql($source)) {
            /** @var Repository\Selection $repository */
            $repository = $this->entityManager->getRepository(Entity\Selection::class);

            $repository->copySelectionContactsFromSourceToDestination($source, $selection);
        }
    }

    public function isSql(Entity\Selection $selection): bool
    {
        return null !== $selection->getSql();
    }
}
