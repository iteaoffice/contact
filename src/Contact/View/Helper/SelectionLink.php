<?php
/**
 * ITEA Office copyright message placeholder
 *
 * @category    Contact
 * @package     View
 * @subpackage  Helper
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2014 ITEA Office (http://itea3.org)
 */
namespace Contact\View\Helper;

use Contact\Entity\Selection;

/**
 * Create a link to an selection
 *
 * @category    Selection
 * @package     View
 * @subpackage  Helper
 */
class SelectionLink extends LinkAbstract
{
    /**
     * @var Selection
     */
    protected $selection;

    /**
     * @param Selection $selection
     * @param string    $action
     * @param string    $show
     * @param null      $page
     * @param null      $alternativeShow
     *
     * @return string
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function __invoke(
        Selection $selection = null,
        $action = 'view',
        $show = 'name',
        $page = null,
        $alternativeShow = null
    ) {
        $this->setSelection($selection);
        $this->setAction($action);
        $this->setShow($show);
        $this->setPage($page);

        /**
         * If the alternativeShow is not null, use it an otherwise take the page
         */
        if (!is_null($alternativeShow)) {
            $this->setAlternativeShow($alternativeShow);
        } else {
            $this->setAlternativeShow($page);
        }

        $this->setShowOptions(
            [
                'name' => $this->getSelection()->getSelection(),
            ]
        );
        $this->addRouterParam('page', $page);

        $this->addRouterParam('id', $this->getSelection()->getId());

        return $this->createLink();
    }

    public function parseAction()
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/selection-manager/new');
                $this->setText($this->translate("txt-new-selection"));
                break;
            case 'list':
                $this->setRouter('zfcadmin/selection-manager/list');
                $this->setText($this->translate("txt-list-selections"));

                foreach ($this->getServiceLocator()->get('application')->getMvcEvent()->getRequest()->getQuery(
                ) as $key => $param) {
                    $this->addQueryParam($key, $param);
                }
                $this->addQueryParam('page', $this->getPage());

                break;
            case 'edit':
                $this->setRouter('zfcadmin/selection-manager/edit');
                $this->setText(
                    sprintf($this->translate("txt-edit-selection-%s"), $this->getSelection()->getSelection())
                );
                break;
            case 'view':
                $this->setRouter('zfcadmin/selection-manager/view');
                $this->setText(
                    sprintf($this->translate("txt-view-selection-%s"), $this->getSelection()->getSelection())
                );
                break;
            default:
                throw new \Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }

    /**
     * @return Selection
     */
    public function getSelection()
    {
        if (is_null($this->selection)) {
            $this->selection = new Selection();
        }

        return $this->selection;
    }

    /**
     * @param Selection $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }
}