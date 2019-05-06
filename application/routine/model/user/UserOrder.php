<?php
/**
 *
 * @author: 猜猜我是谁
 * @day: 2019/03/07
 */

namespace app\routine\model\user;

use basic\ModelBasic;
use service\SystemConfigService;
use think\Request;
use think\Session;
use traits\ModelTrait;

/**
 * 用户订单model
 * Class UserOrder
 * @package app\routine\model\user
 */
class UserOrder extends ModelBasic
{
    use ModelTrait;
    /**
    * @title 根据用户id 获取用户兑换订单列表
    * @param uid 用户id
    * @param status 订单状态 0 待发货，1已发货
    */
    public static function getUserOrderList($uid,$status = '',$first = 0,$limit = 8)
    {
        return self::statusByWhere($status)->where('uo.uid',$uid)
            ->field('uo.*,sp.image,sp.store_name,sp.store_info')
            ->order('uo.add_time DESC')->limit($first,$limit)->select()->toArray();
    }

    public static function searchUserOrder($uid,$order_id){
    	return self::statusByWhere()->where('uo.uid',$uid)->where('uo.id',$order_id)->field('uo.*,sp.image,sp.store_name,sp.store_info')
            ->order('uo.add_time DESC')->find();
    }
    /**
    * @title 根据status值返回对应的model对象
    * @param status 订单状态 0 待发货，1已发货
    */
    public static function statusByWhere($status = 9999,$model = null){
    	 if($model == null) $model = self::alias('uo')->join("StoreProduct sp",'sp.id=uo.product_id','LEFT');
    	 switch (intval($status)) {
    	 	case 0:
    	 		return $model->where('uo.status',0);
    	 		break;
    	 	case 1:
    	 		return $model->where('uo.status',1);
    	 		break;
    	 	
    	 	default:
    	 		return $model;
    	 		break;
    	 }
    }
}
