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

    /**
     * @param $where
     * @return array
     */
    public static function systemPage($where)
    {
        $model = new self;
	$model = $model->alias("uo")->join("eb_user u","u.uid=uo.uid")->join("eb_store_product sp","sp.id=uo.product_id")
		->join("eb_user_address ua","ua.id=uo.address_id",'LEFT')
		->field("sp.store_name,u.nickname,uo.*,ua.*")->order("uo.id desc");
        return self::page($model);
    }
 public static function changeSuccess($id)
    {
        $status = 1;
        $data =self::get($id);
        return self::edit(compact('status'),$id);
    }

}
