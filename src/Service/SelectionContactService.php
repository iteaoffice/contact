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

use Contact\Entity\Contact;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use Doctrine\ORM\PersistentCollection;

/**
 * Class SelectionContactService
 *
 * @package Contact\Service
 */
class SelectionContactService extends AbstractService
{
    public function contactInSelection(Contact $contact, $selections): bool
    {
        if (!\is_array($selections) && !$selections instanceof PersistentCollection) {
            $selections = [$selections];
        }
        foreach ($selections as $selection) {
            if (!$selection instanceof Selection) {
                throw new \InvalidArgumentException('Selection should be instance of Selection');
            }
            if (null === $selection->getId()) {
                throw new \InvalidArgumentException('The given selection cannot be empty');
            }
            if ($this->findContactInSelection($contact, $selection)) {
                return true;
            }
        }

        return false;
    }

    public function findContactInSelection(Contact $contact, Selection $selection): bool
    {
        $repository = $this->entityManager->getRepository(Contact::class);

        if (null !== $selection->getSql()) {
            try {
                //We have a dynamic query, check if the contact is in the selection
                return $repository->isContactInSelectionSQL($contact, $selection->getSql());
            } catch (\Throwable $e) {
                print sprintf('Selection %s is giving troubles (%s)', $selection->getId(), $e->getMessage());
            }
        }
        /*
         * The selection contains contacts, do an extra query to find the contact
         */
        if (\count($selection->getSelectionContact()) > 0) {
            $findContact = $this->entityManager->getRepository(SelectionContact::class)->findOneBy(
                [
                    'contact'   => $contact,
                    'selection' => $selection,
                ]
            );
            /*
             * Return true when we found a contact
             */
            if (null !== $findContact) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Selection $selection
     * @param bool      $toArray
     *
     * @return Contact[]
     */
    public function findContactsInSelection(Selection $selection, bool $toArray = false): array
    {
        $repository = $this->entityManager->getRepository(Contact::class);
        /*
         * A selection can have 2 methods, either SQL or a contacts. We need to query both
         */
        if (null !== $selection->getSql()) {
            //We have a dynamic query, check if the contact is in the selection
            return $repository->findContactsBySelectionSQL($selection->getSql(), $toArray);
        }

        return $repository->findContactsBySelectionContact($selection, $toArray);
    }
}
