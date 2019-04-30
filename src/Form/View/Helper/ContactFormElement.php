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

use Zend\Form\ElementInterface;
use Zf3Bootstrap4\Form\View\Helper\FormElement;

/**
 * Class ContactFormElement
 *
 * @package Contact\Form\View\Helper
 */
final class ContactFormElement extends FormElement
{
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

        return parent::render($element);
    }
}
