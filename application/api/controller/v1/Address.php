<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/5/24
 * Time: 15:38
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\UserAddress;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Controller;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];


    //    protected $beforeActionList = [
    //        'first' => ['only' => 'second,third']
    //    ];
    //
    //    protected function first(){
    //        echo 'first';
    //    }
    //
    //    //API接口
    //    public function second(){
    //        echo 'second';
    //    }
    //
    //    public function third(){
    //        echo 'third';
    //    }


    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id', $uid)
            ->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }

    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();

        // 根据Token来获取uid
        // 根据uid来查找用户数据，判断用户是否存在，如果不存在抛出异常。
        // 获取用户从客户端提交来的地址信息
        // 根据用户地址信息是否存在，从而判断是添加地址还是更新地址

        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user)
        {
            throw new UserException();
        }

        $dataArray = $validate->getDataByRule(input('post.'));


        $userAddress = $user->address;
        if (!$userAddress)
        {
            $user->address()
                ->save($dataArray);
        }
        else
        {
            $user->address->save($dataArray);
        }
        //        return $user;
        return json(new SuccessMessage(), 201);
    }
}