<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

namespace Contact\Form;

use Contact\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Contact\Repository\Contact as ContactRepository;
use Zend\Form\Form;
use Zend\Form\Element;

/**
 * Class OrganisationMerge
 *
 * @package Organisation\Form
 */
class ContactMerge extends Form
{
    /**
     * OrganisationMerge constructor.
     * @param EntityManager $entityManager
     * @param Contact       $target
     */
    public function __construct(EntityManager $entityManager = null, Contact $target = null)
    {
        parent::__construct('contact_merge');

        $this->setAttribute('id', 'contact-merge');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $mainSuggestions = [];
        if (!is_null($entityManager) && !is_null($target)) {
            /** @var ContactRepository $repository */
            $repository = $entityManager->getRepository(Contact::class);
            $suggestions = $repository->findMergeCandidatesFor($target);
            /** @var Contact $contact */
            foreach ($suggestions as $contact) {
                $mainSuggestions[$contact->getId()] = sprintf(
                    '%s (%s)',
                    $contact->parseFullName(),
                    $contact->getContactOrganisation()->getOrganisation()->getCountry()->getCountry()
                );
            }
        }

        $this->add([
            'type'       => Element\Radio::class,
            'name'       => 'source-main',
            'options' => [
                'label' => '',
                'value_options' => $mainSuggestions
            ],
        ]);

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'source-search',
            'attributes' => [
                'id'    => 'source-search',
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'preview',
            'attributes' => [
                'id'    => 'btn-preview',
                'class' => 'btn btn-primary',
                'value' => _('txt-preview-merge'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'merge',
            'attributes' => [
                'class' => 'btn btn-danger',
                'value' => _('txt-merge'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'cancel',
            'attributes' => [
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'swap',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => _('txt-swap-source-and-destination'),
            ],
        ]);
    }
}
