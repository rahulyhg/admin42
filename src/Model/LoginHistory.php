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

namespace Admin42\Model;

use Core42\Model\AbstractModel;

/**
 * @method LoginHistory setId() setId(int $id)
 * @method int getId() getId()
 * @method LoginHistory setUserId() setUserId(int $userId)
 * @method int getUserId() getUserId()
 * @method LoginHistory setStatus() setStatus(string $status)
 * @method string getStatus() getStatus()
 * @method LoginHistory setIp() setIp(string $ip)
 * @method string getIp() getIp()
 * @method LoginHistory setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 */
class LoginHistory extends AbstractModel
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    /**
     * @var array
     */
    protected $properties = [
        'id',
        'userId',
        'status',
        'ip',
        'created',
    ];
}
