<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2015 ITEA Office (https://itea3.org)
 */

namespace Contact\Service;

use Contact\Entity\Contact;
use Contact\Entity\Selection;
use Contact\Entity\SelectionContact;
use Contact\Entity\SelectionSql;
use Doctrine\ORM\QueryBuilder;

/**
 * SelectionService.
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 */
class SelectionService extends ServiceAbstract
{

    /** @param int $id
     * @return SelectionService;
     */
    public function setSelectionId($id)
    {
        $this->setSelection($this->findEntityById('selection', $id));

        return $this;
    }

    /**
     * @return bool
     */
    public function isSql()
    {
        return !is_null($this->selection->getSql());
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return is_null($this->selection) || is_null($this->selection->getId());
    }

    /**
     * @return int
     */
    public function getAmountOfContacts()
    {
        try {
            return sizeof($this->getContactService()->findContactsInSelection($this->selection));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function findTags()
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('selection'))->findTags();
    }

    /**
     * @param null $query
     *
     * @return QueryBuilder
     */
    public function searchSelection($query = null)
    {
        return $this->getEntityManager()->getRepository($this->getFullEntityName('selection'))
            ->searchSelections($query);
    }

    /**
     * Selections can be fixed (via the selection_contact) or dynamic (via de SQL).
     *
     * @param Contact $contact
     *
     * @return Selection[]
     */
    public function findSelectionsByContact(Contact $contact)
    {
        $selections = $this->getEntityManager()->getRepository($this->getFullEntityName('selection'))
            ->findFixedSelectionsByContact($contact);

        /**
         * @var $selection Selection
         */
        foreach ($this->findAll('selection') as $selection) {
            /**
             * Skip the deleted selections and the ones the user is in
             */
            if (is_null($selection->getDateDeleted()) && !is_null($selection->getSql())
                && $this->getContactService()->contactInSelection($contact, $selection)
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
    public function updateSelectionContacts(Selection $selection, array $data)
    {
        /**
         * First update the selection based on the type
         */
        if ((int)$data['type'] === Selection::TYPE_FIXED) {
            //remove the query
            if (!is_null($sql = $selection->getSql())) {
                $this->removeEntity($sql);
            }

            //Update the contacts
            if (!empty($data['added'])) {
                foreach (explode(',', $data['added']) as $contactId) {
                    $contact = $this->getContactService()->findEntityById('contact', $contactId);

                    if (!$contact->isEmpty() && !$this->getContactService()->contactInSelection($contact, $selection)) {
                        $selectionContact = new SelectionContact();
                        $selectionContact->setContact($contact);
                        $selectionContact->setSelection($selection);
                        $this->newEntity($selectionContact);
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
                $selectionSql = new SelectionSql();
                $selectionSql->setSelection($selection);
            }
            $selectionSql->setQuery($data['sql']);
            $this->updateEntity($selectionSql);
        }
    }


    /**
     * @param \Contact\Entity\Selection $selection
     *
     * @return SelectionService;
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;

        return $this;
    }

    /**
     * @return \Contact\Entity\Selection
     */
    public function getSelection()
    {
        return $this->selection;
    }
}
