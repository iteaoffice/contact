<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     Service
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\Service;

use Contact\Entity\Contact;
use Contact\Entity\Selection;
use Doctrine\ORM\QueryBuilder;

/**
 * SelectionService
 *
 * this is a generic wrapper service for all the other services
 *
 * First parameter of all methods (lowercase, underscore_separated)
 * will be used to fetch the correct model service, one exception is the 'linkModel'
 * method.
 *
 */
class SelectionService extends ServiceAbstract
{
    /**
     * @var Selection
     */
    protected $selection;

    /** @param int $id
     *
     * @return SelectionService;
     */
    public function setSelectionId($id)
    {
        $this->setSelection($this->findEntityById('selection', $id));

        return $this;
    }

    public function isSql()
    {
        return !is_null($this->selection->getSql());
    }

    /**
     * @return int
     */
    public function getAmountOfContacts()
    {
        return sizeof($this->getContactService()->findContactsInSelection($this->selection));
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
     * Selections can be fixed (via the selection_contact) or dynamic (via de SQL)
     *
     * @param Contact $contact
     *
     * @return Selection[]
     */
    public function findSelectionsByContact(Contact $contact)
    {

        $selections = $this->getEntityManager()->getRepository(
            $this->getFullEntityName('selection')
        )->findFixedSelectionsByContact(
            $contact
        );

        /**
         * Find now the dynamic selections
         */
        foreach ($this->findAll('selection') as $selection) {
            /**
             * @var $selection Selection;
             */
            if (!is_null($selection->getSql()) && $this->getContactService()->inSelection($selection)) {
                $selections[] = $selection;
            }
        }

        /**
         * Fill the array with keys to enable sorting
         */
        $result = array_combine($selections, $selections);
        ksort($result);

        return $result;
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
