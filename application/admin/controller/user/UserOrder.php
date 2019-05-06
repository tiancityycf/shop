<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/11/11
 */
namespace app\admin\controller\user;
use app\admin\controller\AuthController;
use service\FormBuilder as Form;
use traits\CurdControllerTrait;
use service\UtilService as Util;
use service\JsonService;
use think\Request;
use think\Url;
use app\admin\model\user\User as UserModel;
use app\admin\model\user\UserOrder as UserOrderModel;
use basic\ModelBasic;
use service\HookService;
use behavior\user\UserBehavior;
use app\admin\model\store\StoreVisit;
/**
 * @package app\admin\controller\user
 */
class UserOrder extends AuthController
{
    use CurdControllerTrait;
    /**
     * 获取user表
     *
     * @return json
     */
    public function index(){
	//$list = UserOrderModel::alias("uo")->join("eb_user u","u.uid=uo.uid")->join("eb_store_product sp","sp.id=uo.product_id")->field("sp.store_name,u.nickname,uo.*")->order("uo.id desc")->select();
	//$this->assign("list",$list);
	    $where = [];
	$this->assign(UserOrderModel::systemPage($where));
	return $this->fetch();
    }

    public function succ($id)
    {
        if(!UserOrderModel::be(['id'=>$id,'status'=>0]))
            return JsonService::fail('操作记录不存在或状态错误!');
        $extract=UserOrderModel::get($id);
        if(!$extract)  return JsonService::fail('操作记录不存!');
        if($extract->status==1)  return JsonService::fail('您已发货,请勿重复发货!');
        $res = UserOrderModel::changeSuccess($id);
        if($res){
            return JsonService::successful('操作成功!');
        }else{
            return JsonService::fail('操作失败!');
        }
    }

}

