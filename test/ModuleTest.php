<?php
/**
 * ITEA copyright message placeholder
 *
 * @category    ProjectTest
 * @package     Entity
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2004-2017 ITEA Office (https://itea3.org)
 */

declare(strict_types=1);

namespace ContactTest;

use Contact\Module;
use Testing\Util\AbstractServiceTest;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\View\HelperPluginManager;

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

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey(ConfigAbstractFactory::class, $config);
    }

    public function testInstantiationOfConfigAbstractFactories(): void
    {
        $module = new Module();
        $config = $module->getConfig();

        $abstractFacories = $config[ConfigAbstractFactory::class] ?? [];

        foreach ($abstractFacories as $service => $dependencies) {

            $instantiatedDependencies = [];
            foreach ($dependencies as $dependency) {

                if ($dependency === 'Application') {
                    $dependency = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
                }
                if ($dependency === 'Config') {
                    $dependency = [];
                }
                if ($dependency === 'ViewHelperManager') {
                    $dependency = $this->getMockBuilder(HelperPluginManager::class)->disableOriginalConstructor()
                        ->getMock();
                }
                $instantiatedDependencies[] = $dependency;
            }

            $instance = new $service(...$instantiatedDependencies);

            $this->assertInstanceOf($service, $instance);
        }

    }
}