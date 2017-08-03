<?php namespace App\Http\Controllers\Api;

//use AdminMenu;

class IndexController extends BaseApiController
{

    public function getIndex()
    {
        $data = ['code' => 1, 'message' => '请输入接口名称!'];
        return Response()->json($data);
    }

    public function getMenu()
    {
//        $menus = (new AdminMenu())->visibleLinks();

        $data = ['code' => 1, 'message' => '正确', 'data' => ''];
        return Response()->json($data);
    }
}
