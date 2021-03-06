<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2004-2017 Jield BV (https://jield.nl)
 * @license     https://jield.net/license.txt proprietary
 * @link        https://jield.net
 */

declare(strict_types=1);

namespace ContactTest;

use Contact\Module;
use Laminas\Mvc\Application;
use Laminas\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Laminas\View\HelperPluginManager;
use Testing\Util\AbstractServiceTest;

use function is_string;

/**
 * Class ModuleTest
 *
 * @package ContactTest
 */
class ModuleTest extends AbstractServiceTest
{
    public function testCanFindConfiguration(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        self::assertArrayHasKey('service_manager', $config);
        self::assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    public function testInstantiationOfConfigAbstractFactories(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $abstractFacories = $config[ConfigAbstractFactory::class] ?? [];

        foreach ($abstractFacories as $service => $dependencies) {
            //Skip the Filters
            if (strpos($service, 'Filter') !== false) {
                continue;
            }
            if (strpos($service, 'Handler') !== false) {
                continue;
            }
            if (strpos($service, 'FormElement') !== false) {
                continue;
            }

            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {
                if ($dependency === 'Application') {
                    $dependency = Application::class;
                }
                if ($dependency === 'Config') {
                    $dependency = [];
                }
                if ($dependency === 'BjyAuthorize\Config') {
                    $dependency = [];
                }
                if ($dependency === 'ViewHelperManager') {
                    $dependency = HelperPluginManager::class;
                }
                if (is_string($dependency)) {
                    $instantiatedDependencies[] = $this->getMockBuilder($dependency)->disableOriginalConstructor()
                        ->getMock();
                } else {
                    $instantiatedDependencies[] = [];
                }
            }

            $instance = new $service(...$instantiatedDependencies);

            self::assertInstanceOf($service, $instance);
        }
    }
}
