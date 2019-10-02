<?php
/**
 * ITEA Office all rights reserved
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\View\Helper;

use Contact\Entity\OptIn;
use Exception;

/**
 * Class OptInLink
 *
 * @package Contact\View\Helper
 */
class OptInLink extends LinkAbstract
{
    /**
     * @var OptIn
     */
    private $optIn;

    public function __invoke(
        OptIn $optIn = null,
        $action = 'view',
        $show = 'name'
    ) {
        $this->optIn = $optIn;
        $this->setAction($action);
        $this->setShow($show);

        if (null !== $optIn) {
            $this->setShowOptions(
                [
                    'name' => $this->optIn->getOptIn(),
                ]
            );
            $this->addRouterParam('id', $this->optIn->getId());
        }

        return $this->createLink();
    }

    public function parseAction(): void
    {
        switch ($this->getAction()) {
            case 'new':
                $this->setRouter('zfcadmin/opt-in/new');
                $this->setText($this->translate("txt-new-opt-in"));
                break;
            case 'edit':
                $this->setRouter('zfcadmin/opt-in/edit');
                $this->setText(sprintf($this->translate("txt-edit-opt-in-%s"), $this->optIn->getOptIn()));
                break;
            case 'view':
                $this->setRouter('zfcadmin/opt-in/view');
                $this->setText(sprintf($this->translate("txt-view-opt-in-%s"), $this->optIn->getOptIn()));
                break;
            default:
                throw new Exception(sprintf("%s is an incorrect action for %s", $this->getAction(), __CLASS__));
        }
    }
}
