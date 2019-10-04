<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    Project
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/project for the canonical source repository
 */

declare(strict_types=1);

namespace Contact\Form\Office;

use Doctrine\ORM\EntityManager;
use Program\Entity\Call\Call;
use Evaluation\Entity\Report as EvaluationReport;
use Evaluation\Entity\Report\Type as ReportType;
use Evaluation\Service\EvaluationReportService;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use function array_combine;
use function array_reverse;
use function date;
use function range;
use function sprintf;

/**
 * Class ContactFilter
 * @package Contact\Form\Office
 */
final class ContactFilter extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setAttribute('method', 'get');
        $this->setAttribute('action', '');

        $filterFieldset = new Fieldset('filter');

        $filterFieldset->add([
            'type'       => Element\Select::class,
            'name'       => 'active',
            'options'    => [
                'inline'        => true,
                'label'         => _('txt-status'),
                'value_options' => [
                    'all'    => _('txt-all'),
                    'active' => _('txt-active'),
                ],
            ],
            'attributes' => [
                'value' => 'active',
            ]
        ]);

        $this->add($filterFieldset);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'submit',
            'attributes' => [
                'id'    => 'submit',
                'class' => 'btn btn-primary',
                'value' => _('txt-filter'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'clear',
            'attributes' => [
                'id'    => 'cancel',
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);
    }
}
