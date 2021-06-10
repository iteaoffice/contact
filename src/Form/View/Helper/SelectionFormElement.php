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

use Laminas\Form\ElementInterface;
use LaminasBootstrap5\Form\View\Helper\FormElement;

/**
 * Class SelectionSelect
 *
 * @package Contact\Form\View\Helper
 */
final class SelectionFormElement extends FormElement
{
    public function __invoke(ElementInterface $element = null, $type = self::TYPE_HORIZONTAL, bool $formElementOnly = false)
    {
        $this->type = $type;

        $this->view->headLink()->appendStylesheet('/assets/bootstrap-select-1.14-dev/dist/css/bootstrap-select.min.css');
        $this->view->headScript()->appendFile('/assets/bootstrap-select-1.14-dev/dist/js/bootstrap-select.min.js', 'text/javascript');
        $this->view->inlineScript()->appendScript("$('.selectpicker-selection').selectpicker();", 'text/javascript');

        if ($element) {
            return $this->render($element);
        }

        return $this;
    }

    public function render(ElementInterface $element): string
    {
        $element->setValueOptions($element->getValueOptions());

        $element->setAttribute('class', 'form-control selectpicker selectpicker-selection');
        $element->setAttribute('data-live-search', 'true');

        $element->setValue($element->getValue());

        return parent::render($element);
    }
}
