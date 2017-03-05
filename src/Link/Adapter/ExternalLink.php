<?php

/*
 * admin42
 *
 * @package admin42
 * @link https://github.com/raum42/admin42
 * @copyright Copyright (c) 2010 - 2017 raum42 (https://raum42.at)
 * @license MIT License
 * @author raum42 <kiwi@raum42.at>
 */


namespace Admin42\Link\Adapter;

class ExternalLink implements AdapterInterface
{
    /**
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function assemble($value, $options = [])
    {
        return $this->getLinkData($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function getDisplayName($value)
    {
        return $this->getLinkData($value);
    }

    /**
     * @param $value
     * @return string
     */
    protected function getLinkData($value)
    {
        if (empty($value['url'])) {
            return '';
        }

        $value['url'] = \str_replace('http://', '', $value['url']);
        $value['url'] = \str_replace('https://', '', $value['url']);
        $value['url'] = \str_replace('mailto:', '', $value['url']);

        if (empty($value['type'])) {
            $value['type'] = 'http://';
        }

        return $value['type'] . $value['url'];
    }

    /**
     * @return array
     */
    public function getPartials()
    {
        return [
            'link/external.html' => 'link/external',
        ];
    }
}
