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

namespace Project;

use Contact\Controller\Plugin;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
        Plugin\MergeContact::class => [
            EntityManager::class, TranslatorInterface::class
        ],
        Plugin\GetFilter::class => [
            'Application'
        ],
    ]
];