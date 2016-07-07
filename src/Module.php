<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\Console\Console;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Config;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/../config/module.config.php',
            include __DIR__ . '/../config/cli.config.php',
            include __DIR__ . '/../config/permissions.config.php',
            include __DIR__ . '/../config/assets.config.php',
            include __DIR__ . '/../config/navigation.config.php',
            include __DIR__ . '/../config/project.config.php',
            include __DIR__ . '/../config/translation.config.php',
            include __DIR__ . '/../config/admin.config.php',
            include __DIR__ . '/../config/media.config.php',
            include __DIR__ . '/../config/caches.config.php',
            include __DIR__ . '/../config/routing.config.php'
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $e->getApplication()->getEventManager()->getSharedManager()->attach(
            'Zend\Mvc\Controller\AbstractController',
            MvcEvent::EVENT_DISPATCH,
            function ($e) {
                $controller      = $e->getTarget();

                if (!($controller instanceof AbstractAdminController)) {
                    return;
                }

                $controller->layout()->setTemplate("admin/layout/layout");

                $sm = $e->getApplication()->getServiceManager();

                $sm->get('MvcTranslator')->setLocale('en-US');


                $viewHelperManager = $sm->get('ViewHelperManager');

                $config = $sm->get('config');
                $smConfig = new Config($config['admin']['view_helpers']);

                $smConfig->configureServiceManager($viewHelperManager);

                $headScript = $viewHelperManager->get('headScript');
                $headLink = $viewHelperManager->get('headLink');
                $basePath = $viewHelperManager->get('basePath');

                $headScript->appendFile($basePath('/assets/admin/core/js/vendor.min.js'));
                $headScript->appendFile($basePath('/assets/admin/core/js/admin42.min.js'));

                $headLink->appendStylesheet($basePath('/assets/admin/core/css/admin42.min.css'));

                $formElement = $viewHelperManager->get('formElement');
                $formElement->addClass('Admin42\FormElements\FileSelect', 'formfileselect');
                //$formElement->addClass('Admin42\FormElements\File', 'formfile');
                $formElement->addClass('Admin42\FormElements\Wysiwyg', 'formwysiwyg');
                $formElement->addClass('Admin42\FormElements\YouTube', 'formyoutube');
                $formElement->addClass('Admin42\FormElements\Link', 'formlink');
                $formElement->addClass('Admin42\FormElements\Tags', 'fromtags');
                $formElement->addClass('Admin42\FormElements\GoogleMap', 'formgooglemap');
            },
            100
        );

        if (Console::isConsole()) {
            return;
        }

        /* @var \Zend\Mvc\Application $application */
        $application    = $e->getTarget();
        $serviceManager = $application->getServiceManager();
        $eventManager   = $application->getEventManager();

        $guards = $serviceManager->get('Core42\Permission')->getGuards('admin42');
        foreach ($guards as $_guard) {
            $_guard->attach($eventManager);
        }
    }
}