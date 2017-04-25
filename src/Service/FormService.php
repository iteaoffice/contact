<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/contact for the canonical source repository
 */

namespace Contact\Service;

use Contact\Entity\EntityAbstract;
use Contact\Form\CreateObject;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class FormService
 *
 * @package Contact\Service
 */
class FormService extends ServiceAbstract
{
    /**
     * @param      $className
     * @param null $entity
     * @param      $data
     *
     * @return Form
     */
    public function prepare($className, $entity = null, array $data = []): Form
    {
        $form = $this->getForm($className, $entity, true);
        $form->setData($data);

        return $form;
    }

    /**
     * @param null $className
     * @param EntityAbstract|null $entity
     * @param bool $bind
     * @return Form
     */
    public function getForm($className = null, EntityAbstract $entity = null, bool $bind = true): Form
    {
        if (!is_null($className) && is_null($entity)) {
            $entity = new $className();
        }

        if (!$entity instanceof EntityAbstract) {
            throw new \InvalidArgumentException("No entity created given");
        }

        $formName = 'Contact\\Form\\' . $entity->get('entity_name') . 'Form';
        $filterName = 'Contact\\InputFilter\\' . $entity->get('entity_name') . 'Filter';

        /**
         * The filter and the form can dynamically be created by pulling the form from the serviceManager
         * if the form or filter is not give in the serviceManager we will create it by default
         */
        if (!$this->getServiceLocator()->has($formName)) {
            $form = new CreateObject($this->getEntityManager(), new $entity());
        } else {
            $form = $this->getServiceLocator()->get($formName);
        }

        if ($this->getServiceLocator()->has($filterName)) {
            /** @var InputFilter $filter */
            $filter = $this->getServiceLocator()->get($filterName);
            $form->setInputFilter($filter);
        }

        $form->setAttribute('role', 'form');
        $form->setAttribute('action', '');
        $form->setAttribute('class', 'form-horizontal');

        if ($bind) {
            $form->bind($entity);
        }

        return $form;
    }
}
