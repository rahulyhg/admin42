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


namespace Admin42\FormElements;

use Zend\Hydrator\Strategy\StrategyInterface;

interface StrategyAwareInterface
{
    /**
     * @return string|StrategyInterface
     */
    public function getStrategy();
}
