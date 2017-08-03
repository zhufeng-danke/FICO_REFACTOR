<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function can($permission)
    {
        return $this->visitor()->can($permission);
    }

    public function assertCan($permission, $errMsg = null)
    {
        if (!$this->can($permission)) {
            $this->error($errMsg ?? "没有权限“{$permission}”");
        }
    }

    public function assertIs($role, $errMsg = null)
    {
        if (!role($role)) {
            $this->error($errMsg ?? "您不是“{$role}”成员");
        }
    }

    public function visitor()
    {
        return \CorpAuth::user();
    }

}
