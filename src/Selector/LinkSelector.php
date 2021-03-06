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

namespace Admin42\Selector;

use Admin42\TableGateway\LinkTableGateway;
use Core42\Selector\AbstractSelector;
use Core42\Selector\CacheAbleTrait;

class LinkSelector extends AbstractSelector
{
    use CacheAbleTrait;

    /**
     * @var int
     */
    protected $linkId;

    /**
     * @param int $linkId
     * @return $this
     */
    public function setLinkId($linkId)
    {
        $this->linkId = $linkId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getCacheName()
    {
        return "link";
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return "l" . $this->linkId;
    }

    /**
     * @return mixed
     */
    protected function getUncachedResult()
    {
        return $this->getTableGateway(LinkTableGateway::class)->selectByPrimary((int) $this->linkId);
    }
}
