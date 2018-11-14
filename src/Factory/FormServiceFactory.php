<?php
/**
 * ITEA Office all rights reserved
 *
 * PHP Version 7
 *
 * @category    General
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/main for the canonical source repository
 */
declare(strict_types=1);

namespace Contact\Factory;

use Contact\Service\FormService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FormServiceFactory
 *
 * @package General\Factory
 */
final class FormServiceFactory implements FactoryInterface
{
    /**
     * Create an instance of the requested class name.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @return FormService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormService
    {
        /** @var FormService $formService */
        return new $requestedName($container, $container->get(EntityManager::class));
    }
}
