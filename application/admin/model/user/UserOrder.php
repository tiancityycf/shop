<?php

namespace app\admin\model\user;

use traits\ModelTrait;
use basic\ModelBasic;
use app\admin\model\wechat\WechatUser;
use think\Db;
/**
 * 用户消费新增金额明细 model
 * Class User
 * @package app\admin\model\user
 */

class UserOrder extends ModelBasic
{
    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

}