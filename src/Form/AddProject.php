<?php

/**
 * Jield copyright message placeholder.
 *
 * @category    Contact
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2019 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace Contact\Form;

use Project\Entity\Project;
use Project\Service\ProjectService;
use Laminas\Form\Element;
use Laminas\Form\Form;

/**
 * Class AddProject
 *
 * @package Contact\Form
 */
final class AddProject extends Form
{
    public function __construct(ProjectService $projectService)
    {
        parent::__construct('addProject');
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '');
        $this->setAttribute('class', 'form-horizontal');

        $projects = [];
        /** @var Project $project */
        foreach ($projectService->findAllProjects()->getResult() as $project) {
            $projects[$project->getId()] = $project->parseFullName();
        }

        $this->add(
            [
                'type'    => Element\Select::class,
                'name'    => 'project',
                'options' => [
                    'label'         => _('txt-project'),
                    'value_options' => $projects,
                    'help-block'    => _('txt-select-a-project-to-find-its-existing-partners')
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
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
    }
}
