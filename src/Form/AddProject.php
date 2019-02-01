<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Project\Entity\Project;
use Project\Service\ProjectService;
use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Class SelectionContacts
 * @package Contact\Form
 */
final class AddProject extends Form
{
    public const ROLE_EXTERNAL_EXPERT = 'expert';
    public const ROLE_REVIEWER        = 'reviewer';
    public const ROLE_ASSOCIATE       = 'associate';

    public function __construct(ProjectService $projectService)
    {
        parent::__construct();
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $projects = [];
        /** @var Project $project */
        foreach ($projectService->findAllProjects()->getResult() as $project) {
            $projects[$project->getId()] = $project->parseFullName();
        }

        $this->add([
            'type'       => Element\Select::class,
            'name'       => 'project',
            'options'    => [
                'label'         => _("txt-project"),
                'value_options' => $projects,
            ],
        ]);

        $this->add([
            'type'       => Element\Radio::class,
            'name'       => 'role',
            'options'    => [
                'label'         => _("txt-role"),
                'value_options' => [
                    self::ROLE_ASSOCIATE       => _("txt-associate"),
                    self::ROLE_REVIEWER        => _("txt-reviewer"),
                    self::ROLE_EXTERNAL_EXPERT => _("txt-external-expert"),
                ],
            ],
            'attributes' => [
                'value' => self::ROLE_ASSOCIATE
            ]
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => _('txt-submit'),
            ],
        ]);

        $this->add([
            'type'       => Element\Submit::class,
            'name'       => 'cancel',
            'attributes' => [
                'class' => 'btn btn-warning',
                'value' => _('txt-cancel'),
            ],
        ]);
    }
}
