<?php

class Preload_Pc extends Candy_Controller
{
    public $template = 'layout/pc/g';

    function before()
    {
        parent::before();
    }

    /**
     * 加载哪些附加功能
     * @param array $coms
     */
    function _use(array $coms)
    {
        if(in_array('db', $coms)){
            // Candy_Doctrine::conn();
            // Candy_Doctrine::db2model(array('zuaa_lite'));
        }

        if(in_array('acl', $coms)){

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
}