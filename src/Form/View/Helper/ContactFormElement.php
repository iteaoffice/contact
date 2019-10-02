<?php
/**
 * Jield BV all rights reserved.
 *
 * @category    Equipment
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Contact\Form\View\Helper;

use Contact\Entity\Contact;
use Contact\Service\ContactService;
use Zend\Form\ElementInterface;
use Zend\I18n\Translator\Translator;
use Zend\View\HelperPluginManager;
use Zf3Bootstrap4\Form\View\Helper\FormElement;

/**
 * Class ContactFormElement
 *
 * @package Contact\Form\View\Helper
 */
final class ContactFormElement extends FormElement
{
    /**
     * @var ContactService
     */
    private $contactService;

    public function __construct(
        ContactService $contactService,
        HelperPluginManager $viewHelperManager,
        Translator $translator
    ) {
        parent::__construct($viewHelperManager, $translator);

        $this->contactService = $contactService;
    }

    public function __invoke(ElementInterface $element = null, bool $inline = false)
    {
        $this->inline = $inline;

        $this->view->headLink()
            ->appendStylesheet('/assets/css/bootstrap-select.min.css');
        $this->view->headLink()
            ->appendStylesheet('/assets/css/ajax-bootstrap-select.min.css');
        $this->view->headScript()->appendFile(
            '/assets/js/bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->headScript()->appendFile(
            '/assets/js/ajax-bootstrap-select.min.js',
            'text/javascript'
        );
        $this->view->inlineScript()->appendScript(
            "
                $('.selectpicker-contact').selectpicker().ajaxSelectPicker();",
            'text/javascript'
        );


        if ($element) {
            return $this->render($element);
        }

        return $this;
    }

    public function render(ElementInterface $element): string
    {
        $element->setValueOptions($element->getValueOptions());

        $element->setAttribute('class', 'form-control selectpicker selectpicker-contact');
        $element->setAttribute('data-live-search', 'true');
        $element->setAttribute('data-abs-ajax-url', 'admin/contact/search.html');

        $element->setValue($element->getValue());

        //When we have a value, inject the corresponding contact in the value options
        if (null !== $element->getValue()) {
            $value = $element->getValue();
            if ($element->getValue() instanceof Contact) {
                $value = $element->getValue()->getId();
            }

            $contact = $this->contactService->findContactById((int)$value);
            if (null !== $contact) {
                $element->setValueOptions([$contact->getId() => $contact->getFormName()]);
            }
        }


        return parent::render($element);
    }
}
