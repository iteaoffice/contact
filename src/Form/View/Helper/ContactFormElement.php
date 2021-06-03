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
use Laminas\Form\ElementInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\View\HelperPluginManager;
use LaminasBootstrap5\Form\View\Helper\FormElement;

/**
 * Class ContactFormElement
 *
 * @package Contact\Form\View\Helper
 */
final class ContactFormElement extends FormElement
{
    private ContactService $contactService;

    public function __construct(
        ContactService $contactService,
        HelperPluginManager $viewHelperManager,
        Translator $translator
    ) {
        parent::__construct($viewHelperManager, $translator);

        $this->contactService = $contactService;
    }

    public function __invoke(ElementInterface $element = null, $type = self::TYPE_HORIZONTAL, bool $formElementOnly = false)
    {
        $this->type = $type;

        $this->view->headLink()->appendStylesheet('/assets/bootstrap-select-1.14-dev/dist/css/bootstrap-select.min.css');
//        $this->view->headScript()->appendFile('/assets/bootstrap-select-1.14-dev/dist/js/bootstrap-select.min.js','text/javascript');
        $this->view->headScript()->appendFile('/assets/bootstrap-select-1.14-dev/dist/js/bootstrap-select.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/assets/ajax-bootstrap-select/dist/css/ajax-bootstrap-select.css');
        $this->view->headScript()->appendFile('/assets/ajax-bootstrap-select/dist/js/ajax-bootstrap-select.js', 'text/javascript');
        $this->view->inlineScript()->appendScript("$('.selectpicker-contact').selectpicker().ajaxSelectPicker();", 'text/javascript');
//        $this->view->inlineScript()->appendScript("$('.selectpicker-contact').selectpicker();",'text/javascript');

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
