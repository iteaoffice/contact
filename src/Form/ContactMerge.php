<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Content
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

namespace Contact\Form;

use Contact\Entity\Contact;
use Contact\Repository\Contact as ContactRepository;
use Doctrine\ORM\EntityManager;
use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class OrganisationMerge
 *
 * @package Organisation\Form
 */
final class ContactMerge extends Form
{
    public function __construct(EntityManager $entityManager = null, Contact $target = null)
    {
        parent::__construct('contact_merge');

        $this->setAttribute('id', 'contact-merge');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');

        $mainSuggestions = [];
        if ($entityManager !== null && $target !== null) {
            /** @var ContactRepository $repository */
            $repository = $entityManager->getRepository(Contact::class);
            $suggestions = $repository->findMergeCandidatesFor($target);
            /** @var Contact $contact */
            foreach ($suggestions as $contact) {
                $contactId = $contact->getId();

                $mainSuggestions[$contactId] = sprintf(
                    '%s - %s (%s)',
                    $contactId,
                    $contact->parseFullName(),
                    null === $contact->getContactOrganisation() ? '-'
                        : $contact->getContactOrganisation()->getOrganisation()->getOrganisation()
                );
            }
        }

        $this->add(
            [
                'type'    => Element\Radio::class,
                'name'    => 'source-main',
                'options' => [
                    'label'         => '',
                    'value_options' => $mainSuggestions
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Select::class,
                'name'       => 'source-search',
                'attributes' => [
                    'id' => 'source-search',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'preview',
                'attributes' => [
                    'id'    => 'btn-preview',
                    'class' => 'btn btn-primary',
                    'value' => _('txt-preview-merge'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'merge',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-merge'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'cancel',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-cancel'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'swap',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-swap-source-and-destination'),
                ],
            ]
        );
    }
}
