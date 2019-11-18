<?php
/**
 * ZF3Turbo
 *
 * Zend Framework 3 Turbo Module
 * 
 * @link https://github.com/Mecanik/ZF3Turbo
 * @copyright Copyright (c) 2019 Norbert Boros ( a.k.a Mecanik )
 * @license https://github.com/Mecanik/ZF3Turbo/blob/master/LICENSE.md
 */

namespace Mecanik\ZF3Turbo;
use Zend\Mvc\MvcEvent;
use \Mecanik\ZF3Turbo\Service\ZF3TurboService;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    // onBootstrap() is called once all modules are initialized.
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $serviceManager = $event->getApplication()->getServiceManager();

        $config = $serviceManager->get('config');

        if(!isset($config['zf3turbo'])) {
            throw new Exception\RuntimeException('Unable to load configuration; did you forget to create zf3turbo.global.php ?');
        }

        $listener = new \Mecanik\ZF3Turbo\Listener\ZF3TurboListener($config['zf3turbo']);

        $listener->attach($eventManager);      
    }
}
