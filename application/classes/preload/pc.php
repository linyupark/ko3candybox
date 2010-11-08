<?php

class Preload_Pc extends Candy_Controller
{
    function before()
    {
        parent::before();
    }

    /**
     * 当控制器需要使用数据库调用此函数
     */
    static function db()
    {
        // Candy_Doctrine::conn();
    }

    /**
     * 当控制器需要使用acl使用此函数
     */
    static function acl()
    {
        /*
        $request = $this->request;

        // roles
        $this->_acl('guest');
        $this->_acl->add_role('freeze');
        $this->_acl->add_role('user');
        $this->_acl->add_role('admin');

        // rules
        $this->_acl->deny('guest', 'pc_account', 'index');

        $role = $this->_role();

        // handler
        if( ! $this->_acl->is_allowed($role, $this->_resource, $request->action)){
            if($role == 'guest'){
                $this->_flash('您需要登录本站才能继续操作！');
                $this->_redirect('pc/account/login?redir='.$request->url());
            } else {
                $this->_redirect('pc/home/deny?redir='.$request->url());
            }
        }*/
    }
}