{extend name="public/container"}

{block name="content"}

<div class="row">

    <div class="col-sm-12">

        <div class="ibox">

            <div class="ibox-content">

                <div class="table-responsive">

                    <table class="table table-striped  table-bordered">

                        <thead>

                        <tr>

                            <th class="text-center">编号</th>
                            <th class="text-center">用户信息</th>
                            <th class="text-center">积分</th>
                            <th class="text-center">商品信息</th>
                            <th class="text-center">收货地址</th>
                            <th class="text-center">添加时间</th>
                            <th class="text-center">状态</th>
                            <th class="text-center">操作</th>

                        </tr>

                        </thead>

                        <tbody class="">

                        {volist name="list" id="vo"}

                        <tr>

                            <td class="text-center">

                                {$vo.id}

                            </td>

                            <td class="text-center">

                               用户昵称: {$vo.nickname}/用户id:{$vo.uid}

                            </td>



                            <td class="text-center" style="color: #00aa00;">

                                {$vo.integral}

                            </td>

                            <td class="text-center">
                                ID:{$vo.product_id}
                                名字:{$vo.store_name}<br>
                                兑换数量:{$vo.num}
                            </td>
                            <td class="text-center">
                                {$vo.province}
                                {$vo.city}
                                {$vo.district}
                                {$vo.detail} <br/>
                                {$vo.real_name} <br/>
                                {$vo.phone} <br/>
                                兑换数量:{$vo.num}
                            </td>
                            <td class="text-center">

                                {$vo.add_time|date='Y-m-d H:i:s',###}

                            </td>
                            <td class="text-center">

                                {if condition="$vo['status'] eq 1"}

                                已发货<br/>


                                {elseif condition="$vo['status'] eq -1"/}

                                未通过审核<br/>

                                {else/}

                                <div>未发货</div>

                                <button data-url="{:url('succ',['id'=>$vo['id']])}" class="j-success btn btn-primary btn-xs" type="button"><i class="fa fa-check"></i>发货</button>

                                {/if}

                            </td>

                            <td class="text-center">

                            </td>

                        </tr>

                        {/volist}

                        </tbody>

                    </table>

                </div>
		{include file="public/inner_page"}


            </div>

        </div>

    </div>

</div>

{/block}

{block name="script"}

<script>

    (function(){

        $(".open_image").on('click',function (e) {

            var image = $(this).data('image');

            $eb.openImage(image);

        })
	$('.j-success').on('click',function(){

            var url = $(this).data('url');
var btn = $(this);
	    $eb.$swal('delete',function(){

                $eb.axios.post(url).then(function(res){
                    if(res.data.code == 200){
                        $eb.$swal('success',res.data.msg);
			btn.prev().html('已发货');
			btn.remove();
			window.frames[$(".page-tabs-content .active").index()].location.reload();
		    }else{
                        $eb.$swal('error',res.data.msg||'操作失败!');
		    }
		    
                }
).catch(function(err){
	console.log(err);
});
            },{

                title:'确定发货?',

                text:'通过后无法撤销，请谨慎操作！',

                confirm:'确认发货'

            });

        });

    }());

</script>

{/block}

