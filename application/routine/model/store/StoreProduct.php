<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/12
 */

namespace app\routine\model\store;


use app\admin\model\store\StoreProductAttrValue as StoreProductAttrValuemodel;
use app\admin\model\user\User as Usermodel;
use app\admin\model\user\UserBill as UserBillmodel;
use app\admin\model\user\UserOrder as UserOrdermodel;

use basic\ModelBasic;
use traits\ModelTrait;

class StoreProduct extends ModelBasic
{
    use  ModelTrait;

    protected function getSliderImageAttr($value)
    {
        return json_decode($value,true)?:[];
    }

    public static function getValidProduct($productId,$field = '*')
    {
        return self::where('is_del',0)->where('is_show',1)->where('id',$productId)->field($field)->find();
    }

    public static function validWhere()
    {
        return self::where('is_del',0)->where('is_show',1)->where('mer_id',0);
    }

    /**
     * 新品产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNewProduct($field = '*',$limit = 0)
    {
        $model = self::where('is_new',1)->where('is_del',0)->where('mer_id',0)
            ->where('stock','>',0)->where('is_show',1)->field($field)
            ->order('sort DESC, id DESC');
        if($limit) $model->limit($limit);
        return $model->select();
    }


    /**
     * 热卖产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getHotProduct($field = '*',$limit = 0,$where)
    {
//        $model = self::where('is_hot',1)->where('is_del',0)->where('mer_id',0)
//            ->where('stock','>',0)->where('is_show',1)->field($field)
//            ->order('sort DESC, id DESC');
        $where['is_del'] = 0;
        $where['mer_id'] = 0;
        $where['is_show'] = 1;
        $model = self::where($where)
            ->where('stock','>',0)->field($field)
            ->order('sort DESC, id DESC');
        if($limit) $model->limit($limit);
        return $model->select();
    }

    /**
     * 热卖产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getHotProductLoading($field = '*',$offset = 0,$limit = 0)
    {
        $model = self::where('is_hot',1)->where('is_del',0)->where('mer_id',0)
            ->where('stock','>',0)->where('is_show',1)->field($field)
            ->order('sort DESC, id DESC');
        if($limit) $model->limit($offset,$limit);
        return $model->select();
    }

    /**
     * 免费兑换
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function freeBuy($request)
    {
        $model = self::where('id',$request['product_id'])->find();

        Usermodel::beginTrans();
        $info = Usermodel::where("uid",$request['uid'])->lock(true)->find();

	$num = empty($request['num'])?1:$request['num'];
	$address_id = $request['address_id'];
	$total = $num * $model['price'];

        if(empty($address_id)){
            Usermodel::rollbackTrans();
            return ['errcode'=>1,'errmsg'=>'请选择收货地址'];
        }
        if($total>$info['integral']){
            Usermodel::rollbackTrans();
            return ['errcode'=>1,'errmsg'=>'积分不足'];
        }
        try{
            $udata = [];
            $udata['integral'] = ['dec',$total];
            $uwhere = [];
            $uwhere['uid'] = $request['uid'];
            Usermodel::update($udata,$uwhere);
            $data = [];
            $data['uid'] = $request['uid'];
            $data['add_time'] = time();
            $data['product_id'] = $request['product_id'];
            $data['address_id'] = $address_id;
            $data['integral'] = $total;
            $data['num'] = $num;
            UserOrdermodel::set($data);
            $data = [];
            $data['uid'] = $request['uid'];
            $data['link_id'] = 1; 
            $data['pm'] = 0; 
            $data['title'] = '积分兑换'; 
            $data['category'] = 'integral'; 
            $data['type'] = 'free_buy'; 
            $data['add_time'] = time();
            $data['number'] = $total;
            $data['balance'] = $total;
            $data['mark'] = '积分兑换消耗了'.$total.'积分';
            $data['status'] = 1;
            UserBillmodel::set($data);
            Usermodel::commitTrans();
        }catch (\Exception $e){
            Usermodel::rollbackTrans();
            return ['errcode'=>1,'errmsg'=>$e->getMessage()];
        }
        return ['errcode'=>0];
    }

    /**
     * 精品产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getBestProduct($field = '*',$limit = 0)
    {
        $model = self::where('is_best',1)->where('is_del',0)->where('mer_id',0)
            ->where('stock','>',0)->where('is_show',1)->field($field)
            ->order('sort DESC, id DESC');
        if($limit) $model->limit($limit);
        return $model->select();
    }


    /**
     * 优惠产品
     * @param string $field
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getBenefitProduct($field = '*',$limit = 0)
    {
        $model = self::where('is_benefit',1)
            ->where('is_del',0)->where('mer_id',0)->where('stock','>',0)
            ->where('is_show',1)->field($field)
            ->order('sort DESC, id DESC');
        if($limit) $model->limit($limit);
        return $model->select();
    }

    public static function cateIdBySimilarityProduct($cateId,$field='*',$limit = 0)
    {
        $pid = StoreCategory::cateIdByPid($cateId)?:$cateId;
        $cateList = StoreCategory::pidByCategory($pid,'id') ?:[];
        $cid = [$pid];
        foreach ($cateList as $cate){
            $cid[] = $cate['id'];
        }
        $model = self::where('cate_id','IN',$cid)->where('is_show',1)->where('is_del',0)
            ->field($field)->order('sort DESC,id DESC');
        if($limit) $model->limit($limit);
        return $model->select();
    }

    public static function isValidProduct($productId)
    {
        return self::be(['id'=>$productId,'is_del'=>0,'is_show'=>1]) > 0;
    }

    public static function getProductStock($productId,$uniqueId = '')
    {
        return  $uniqueId == '' ?
            self::where('id',$productId)->value('stock')?:0
            : StoreProductAttr::uniqueByStock($uniqueId);
    }

    public static function decProductStock($num,$productId,$unique = '')
    {
        if($unique){
            $res = false !== StoreProductAttrValuemodel::decProductAttrStock($productId,$unique,$num);
            $res = $res && self::where('id',$productId)->setInc('sales',$num);
        }else{
            $res = false !== self::where('id',$productId)->dec('stock',$num)->inc('sales',$num)->update();
        }
        return $res;
    }

}
