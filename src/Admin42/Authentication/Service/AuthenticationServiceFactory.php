<?php
namespace Admin42\Authentication\Service;

use Admin42\Authentication\AuthenticationService;
use Core42\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sessionStorage = new Session(
            'admin42_auth',
            'storage',
            $serviceLocator->get('Zend\Session\Service\SessionManager')
        );
        $authenticationService = new AuthenticationService($sessionStorage);
        $authenticationService->setTableGateway($serviceLocator->get('TableGateway')->get('Admin42\User'));

        return $authenticationService;
    }
}