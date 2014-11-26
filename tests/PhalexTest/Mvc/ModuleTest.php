<?php

namespace PhalexTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Phalex\Mvc\Module;
use Phalex\Mvc\Exception\RuntimeException;
use Phalex\Mvc\Exception\InvalidArgumentException;
use Zend\Stdlib\ArrayUtils;

/**
 * Description of ModuleTest
 *
 * @author quangtm
 */
class ModuleTest extends TestCase
{
    public function testConstructRaiseExptionEmptyParameters()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Invalid parameters for init phalcon extesion');
        new Module([], []);
    }

    public function testConstructRaiseExceptionNotFoundClass()
    {
        $moduleName = 'Application';
        $this->setExpectedException(RuntimeException::class, sprintf('Cannot autoload module "%s"', $moduleName));
        new Module([$moduleName], ['./test']);
    }

    public function testConstructSuccessWithoutCache()
    {
        $moduleNames = ['Application', 'Backend'];

        $module = new Module($moduleNames, ['./module']);
        foreach ($moduleNames as $moduleName) {
            $this->assertTrue(class_exists("$moduleName\\Module"));
        }

        $expectedRegisteredModules = [
            'Application' => [
                'className' => 'Application\\Module',
                'path'      => './module/Application/Module.php'
            ],
            'Backend'     => [
                'className' => 'Backend\\Module',
                'path'      => './module/Backend/Module.php'
            ],
        ];

        $expectedModulesConfig = ArrayUtils::merge(require './module/Application/config/module.config.php', require './module/Backend/config/module.config.php');
        foreach ($expectedModulesConfig as $moduleName => $moduleConfig) {
            if (isset($expectedModulesConfig[$moduleName]['view'])) {
                $expectedModulesConfig[$moduleName]['view'] = realpath($moduleConfig['view']);
            }
        }

        $expectedModulesAutoload = ArrayUtils::merge(require './module/Application/config/autoload.config.php', require './module/Backend/config/autoload.config.php');
        foreach ($expectedModulesAutoload as $moduleName => $configAutoload) {
            foreach ($configAutoload as $key => $value) {
                $expectedModulesAutoload[$moduleName][$key] = realpath($value);
            }
        }

        $this->assertEquals($expectedRegisteredModules, $module->getRegisteredModules());
        $this->assertEquals($expectedModulesConfig, $module->getModulesConfig());
        $this->assertEquals($expectedModulesAutoload, $module->getModulesAutoloadConfig());
    }

    public function testGetModulesAutoloadConfigRaiseException()
    {
        $moduleNames = ['Application', 'Backend'];
        $moduleMock  = new ModuleMock($moduleNames, ['./module']);
        $this->setExpectedException(RuntimeException::class, sprintf('The autoloader configuration for module "%s" is invalid', 'Application'));
        $moduleMock->getModulesAutoloadConfig();
    }

    public function testGetModulesConfigRaiseException()
    {
        $moduleNames = ['Application', 'Backend'];
        $moduleMock  = new ModuleMock($moduleNames, ['./module']);
        $this->setExpectedException(RuntimeException::class, sprintf('The view path for module "%s" is invalid', 'Application'));
        $moduleMock->getModulesConfig();
    }
}
