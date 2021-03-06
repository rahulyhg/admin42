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

use Zend\Form\Element;

class Youtube extends Element implements AngularAwareInterface
{
    use ElementTrait;

    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this;
    }
}
