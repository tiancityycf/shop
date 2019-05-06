<?php

namespace app\routine\model;

use think\Request;
use think\Session;
use think\Model;

class Kouhong extends Model
{

	protected $connection = [
		// 数据库类型
		'type'            => 'mysql',
		// 服务器地址
		'hostname'        => 'localhost',
		// 数据库名
		'database'        => 'kouhong',
		// 用户名
		'username'        => 'kouhong',
		// 密码
		'password'        => 'SwAWF4dfWA6eGXf4',
		// 数据库表前缀
		'prefix'          => 't_',
		];
	protected $table = 't_user';
	/**
	 * @param int $uid
	 * @return bool
	 */
	public function invite_gold_uid($params){
		$data = [];
		$data['invite_gold_uid'] = $params['uid'];
		self::where("openid",$params['open_id'])->update($data);
	}


}

