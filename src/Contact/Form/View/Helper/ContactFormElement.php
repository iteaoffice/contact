<?php
/**
 * ZfcTwitterBootstrap
 */

namespace Contact\Form\View\Helper;

use Zend\Form\Element\Select;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\FormLabel;
use Zend\View\Helper\EscapeHtml;
use ZfcTwitterBootstrap\Form\View\Helper\FormDescription;

/**
 * Form Element
 */
class ContactFormElement extends ZendFormElement
{
    /**
     * @var \Zend\Form\View\Helper\FormLabel
     */
    protected $labelHelper;

    /**
     * @var ZendFormElement
     */
    protected $elementHelper;

    /**
     * @var \Zend\View\Helper\EscapeHtml
     */
    protected $escapeHelper;

    /**
     * @var \Zend\Form\View\Helper\FormElementErrors
     */
    protected $elementErrorHelper;

    /**
     * @var FormDescription
     */
    protected $descriptionHelper;

    /**
     * @var string
     */
    protected $groupWrapper = '<div class="form-group%s" id="control-group-%s">%s</div>';

    /**
     * @var string
     */
    protected $controlWrapper = '<div class="col-lg-9" id="controls-%s">%s%s%s</div>';

    /**
     * Set Label Helper
     *
     * @param  \Zend\Form\View\Helper\FormLabel $labelHelper
     *
     * @return self
     */
    public function setLabelHelper(FormLabel $labelHelper)
    {
        $labelHelper->setView($this->getView());
        $this->labelHelper = $labelHelper;

        return $this;
    }

    /**
     * Get Label Helper
     *
     * @return \Zend\Form\View\Helper\FormLabel
     */
    public function getLabelHelper()
    {
        if (!$this->labelHelper) {
            $this->setLabelHelper($this->view->plugin('formlabel'));
        }

        return $this->labelHelper;
    }

    /**
     * Set EscapeHtml Helper
     *
     * @param  \Zend\View\Helper\EscapeHtml $escapeHelper
     *
     * @return self
     */
    public function setEscapeHtmlHelper(EscapeHtml $escapeHelper)
    {
        $escapeHelper->setView($this->getView());
        $this->escapeHelper = $escapeHelper;

        return $this;
    }

    /**
     * Get EscapeHtml Helper
     *
     * @return \Zend\View\Helper\EscapeHtml
     */
    public function getEscapeHtmlHelper()
    {
        if (!$this->escapeHelper) {
            $this->setEscapeHtmlHelper($this->view->plugin('escapehtml'));
        }

        return $this->escapeHelper;
    }

    /**
     * Set Element Helper
     *
     * @param  \Zend\Form\View\Helper\FormElement $elementHelper
     *
     * @return self
     */
    public function setElementHelper(ZendFormElement $elementHelper)
    {
        $elementHelper->setView($this->getView());
        $this->elementHelper = $elementHelper;

        return $this;
    }

    /**
     * Get Element Helper
     *
     * @return \Zend\Form\View\Helper\FormElement
     */
    public function getElementHelper()
    {
        if (!$this->elementHelper) {
            $this->setElementHelper($this->view->plugin('formelement'));
        }

        return $this->elementHelper;
    }

    /**
     * Set Element Error Helper
     *
     * @param  \Zend\Form\View\Helper\FormElementErrors $errorHelper
     *
     * @return self
     */
    public function setElementErrorHelper(FormElementErrors $errorHelper)
    {
        $errorHelper->setView($this->getView());

        $this->elementErrorHelper = $errorHelper;

        return $this;
    }

    /**
     * Get Element Error Helper
     *
     * @return \Zend\Form\View\Helper\FormElementErrors
     */
    public function getElementErrorHelper()
    {
        if (!$this->elementErrorHelper) {
            $this->setElementErrorHelper($this->view->plugin('formelementerrors'));
        }

        return $this->elementErrorHelper;
    }

    /**
     * Set Description Helper
     *
     * @param FormDescription
     *
     * @return self
     */
    public function setDescriptionHelper(FormDescription $descriptionHelper)
    {
        $descriptionHelper->setView($this->getView());
        $this->descriptionHelper = $descriptionHelper;

        return $this;
    }

    /**
     * Get Description Helper
     *
     * @return FormDescription
     */
    public function getDescriptionHelper()
    {
        if (!$this->descriptionHelper) {
            $this->setDescriptionHelper($this->view->plugin('ztbformdescription'));
        }

        return $this->descriptionHelper;
    }

    /**
     * Set Group Wrapper
     *
     * @param  string $groupWrapper
     *
     * @return self
     */
    public function setGroupWrapper($groupWrapper)
    {
        $this->groupWrapper = (string)$groupWrapper;

        return $this;
    }

    /**
     * Get Group Wrapper
     *
     * @return string
     */
    public function getGroupWrapper()
    {
        return $this->groupWrapper;
    }

    /**
     * Set Control Wrapper
     *
     * @param  string $controlWrapper ;
     *
     * @return self
     */
    public function setControlWrapper($controlWrapper)
    {
        $this->controlWrapper = (string)$controlWrapper;

        return $this;
    }

    /**
     * Get Control Wrapper
     *
     * @return string
     */
    public function getControlWrapper()
    {
        return $this->controlWrapper;
    }

    /**
     * Render
     *
     * @param  Select|ElementInterface $element
     * @param  string                  $groupWrapper
     * @param  string                  $controlWrapper
     *
     * @return string
     */
    public function render(ElementInterface $element, $groupWrapper = null, $controlWrapper = null)
    {
        $labelHelper = $this->getLabelHelper();
        $escapeHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorHelper = $this->getElementErrorHelper();
        $descriptionHelper = $this->getDescriptionHelper();
        $groupWrapper = $groupWrapper ?: $this->groupWrapper;
        $controlWrapper = $controlWrapper ?: $this->controlWrapper;
        /**
         * Disable by default the inArrayValidator
         */
        $element->setDisableInArrayValidator(true);
        $renderer = $elementHelper->getView();

        $id = $element->getAttribute('id') ?: $element->getAttribute('name');
        $element->setAttribute('class', 'form-control');

        $controlLabel = '';
        $label = $element->getLabel();
        if (strlen($label) === 0) {
            $label = $element->getOption('label') ?: $element->getAttribute('label');
        }

        if ($label && !$element->getOption('skipLabel')) {

            $controlLabel .= $labelHelper->openTag(
                [
                    'class' => 'col-lg-3 ' . ($element->getOption(
                            'wrapCheckboxInLabel'
                        ) ? 'checkbox' : 'control-label'),
                ] + ($element->hasAttribute('id') ? ['for' => $id] : [])
            );

            if (null !== ($translator = $labelHelper->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $labelHelper->getTranslatorTextDomain()
                );
            }
            if ($element->getOption('wrapCheckboxInLabel')) {
                $controlLabel .= $elementHelper->render($element) . ' ';
            }
            if ($element->getOption('skipLabelEscape')) {
                $controlLabel .= $label;
            } else {
                $controlLabel .= $escapeHelper($label);
            }
            $controlLabel .= $labelHelper->closeTag();
        }

        if ($element->getOption('wrapCheckboxInLabel')) {
            $controls = $controlLabel;
            $controlLabel = '';
        } else {
            $controls = $elementHelper->render($element);
        }

        $controls = str_replace(
            ['<select'],
            ['<select class="selectpicker" data-live-search="true"'],
            $controls
        );

        /***
         * Now apply the magic
         */
        if ($element->isMultiple()) {
            $controls = str_replace(
                ['data-live-search="true"'],
                ['multiple data-live-search="true"'],
                $controls
            );
        }

        $html = $controlLabel . sprintf(
                $controlWrapper,
                $id,
                $controls,
                $descriptionHelper->render($element),
                $elementErrorHelper->render($element)
            );


        $addtClass = ($element->getMessages()) ? ' has-error' : '';

        return sprintf($groupWrapper, $addtClass, $id, $html);
    }

    /**
     * Magical Invoke
     *
     * @param  \Zend\Form\ElementInterface $element
     * @param  string                      $groupWrapper
     * @param  string                      $controlWrapper
     *
     * @return string|self
     */
    public function __invoke(ElementInterface $element = null, $groupWrapper = null, $controlWrapper = null)
    {
        //Inject the javascript in the header
        $this->view->headLink()->appendStylesheet('/assets/css/bootstrap-select.min.css');
        $this->view->headScript()
            ->appendFile(
                '/assets/js/bootstrap-select.min.js',
                'text/javascript'
            );
        $this->view->headScript()
            ->appendFile(
                '/assets/js/ajax-bootstrap-select.min.js',
                'text/javascript'
            );

        $this->view->inlineScript()
            ->appendScript(
                "var options = {
        ajax: {
            url: '" . $this->view->url('zfcadmin/contact-manager/search') . "',
            type: 'POST',
            dataType: 'json',
            data: {
                search: '{{{q}}}'
            }
        },
        locale: {
            emptyTitle: 'Select and Begin Typing'
        },
        log: 3,
        preprocessData: function (data) {
            var i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push($.extend(true, data[i], {
                        text: data[i].Name,
                        value: data[i].Email,
                        data: {
                        subtext: data[i].Email
                        }
                    }));
                }
            }
// You must always return a valid array when processing data. The
// data argument passed is a clone and cannot be modified directly.
            return array;
        }
    };
    $('.selectpicker').selectpicker().ajaxSelectPicker(options);",
                'text/javascript'
            );


        if ($element) {
            return $this->render($element, $groupWrapper, $controlWrapper);
        }

        return $this;
    }
}
