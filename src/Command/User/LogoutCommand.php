<?php

/*
 * admin42
 *
 * @package admin42
 * @link https://github.com/kiwi-suite/admin42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://www.kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <tech@kiwi-suite.com>
 */


namespace Admin42\Command\User;

use Admin42\Authentication\AuthenticationService;
use Core42\Command\AbstractCommand;

class LogoutCommand extends AbstractCommand
{
    /**
     *
     */
    protected function execute()
    {
        $this->getServiceManager()->get(AuthenticationService::class)->clearIdentity();
    }
}
