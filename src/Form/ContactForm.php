<?php
/**
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Form;

use Contact\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Organisation\Form\Element\Organisation;
use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class ContactFieldset
 *
 * @package Contact\Form
 */
final class ContactForm extends Form
{
    public function __construct(EntityManager $entityManager)
    {
        $contact = new Contact();
        parent::__construct($contact->get('underscore_entity_name'));

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('action', '');

        $projectFieldset = new ObjectFieldset($entityManager, $contact);
        $projectFieldset->setUseAsBaseFieldset(true);
        $this->add($projectFieldset);

        $this->add(
            [
                'type'    => Organisation::class,
                'name'    => 'organisation',
                'options' => [
                    'label'      => _('txt-organisation'),
                    'help-block' => _('txt-organisation-help-block'),
                ],
            ]
        );

        $this->add(
            [
                'type'    => Element\Text::class,
                'name'    => 'branch',
                'options' => [
                    'label'      => _('txt-branch'),
                    'help-block' => _('txt-branch-help-block'),
                ],
            ]
        );

        $this->add(
            [
                'type'    => Element\File::class,
                'name'    => 'file',
                'options' => [
                    'label'      => _('txt-contact-photo-label'),
                    'help-block' => _('txt-contact-photo-help-block'),
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'value' => _('txt-submit'),
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
                'name'       => 'delete',
                'attributes' => [
                    'class' => 'btn btn-danger',
                    'value' => _('txt-delete'),
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'reactivate',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-reactivate'),
                ]
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'deactivate',
                'attributes' => [
                    'class' => 'btn btn-warning',
                    'value' => _('txt-deactivate'),
                ]
            ]
        );
    }
}
