<?php  defined('SYSPATH') or die('No direct script access.');

class Candy_Controller extends Kohana_Controller_Template
{
    public $template = 'layout/xhtml10t';

    protected $_sess;
    protected $_acl;
    protected $_resource;
    protected $_flash_msgs = array();

    // REQUEST PRELOAD
    function before()
    {
        parent::before();

        // AJAX REQUEST DISABLE AUTO_RENDER
        if(Request::$is_ajax){
            $this->auto_render = false;
        }
        // LANG SET
        $this->_i18n();

        // SESSION SET
        $sid = Arr::get($_GET, 'SESSID');
        $this->_sess = Session::instance(null, $sid);
        view::set_global('_SESS', $this->_sess);

        // REQEUST SET
        $request = $this->request;
        View::set_global('_D', $request->directory);
        View::set_global('_C', $request->controller);
        View::set_global('_A', $request->action);
        View::set_global('_rURI', $request->uri);
        View::set_global('_URI', $request->detect_uri());
        View::set_global('_URL', $request->detect_uri().URL::query());

        // FLASH MESSAGE
        $flash_msgs = $this->_sess->get('_flash');
        if(count($flash_msgs) > 0){
            View::set_global('_FLASH', $flash_msgs);
            $this->_sess->delete('_flash');
        }

        // SET FORM DATA
        if($this->_sess->get('_post')){
            View::set_global('_FORM', $this->_sess->get('_post'));
            $this->_sess->delete('_post');
        }
    }

    /**
     * 保存表单的内容
     */
    function _save_post_form()
    {
        if($_POST){
            $this->_sess->set('_post', $_POST);
        }
    }

    /**
     * 使用 acl
     * @param <type> $default_role
     */
    function _acl($default_role='guest')
    {
        $request = $this->request;

        $this->_acl = new ACL();
        $this->_acl->add_role($default_role);
        $role = $this->_role($default_role, false);
        $this->_resource = $request->directory.'_'.$request->controller;
        $this->_acl->add_resource($this->_resource);
        $this->_acl->allow($role, $this->_resource, $request->action);
    }

    /**
     * 设置或者获取 role
     * @param <type> $role
     * @param <type> $force
     * @return <type>
     */
    function _role($role=null, $force=true)
    {
        $_role = $this->_sess->get('_role');

        // 设置默认的role
        if( ! $_role && $role){
            $this->_sess->set('_role', $role);
            return $role;
        }

        // 强制替换
        if($_role && $role && $force){
            $this->_sess->set('_role', $role);
            return $role;
        }

        return $_role;
    }

    /**
     * 设置语言类型
     * @param <type> $lang
     */
    function _i18n($lang='zh-cn')
    {
        I18n::$lang = $lang;
    }

    /**
     * 切换布局文件
     * @param <type> $file 布局view路径
     */
    function _layout($file)
    {
        $this->template = View::factory($file);
    }

    /**
     * 重定向简写
     * @param <type> $url
     */
    function _redirect($url)
    {
        $this->request->redirect($url);
    }

    /**
     * 刷新当前地址
     */
    function _refresh()
    {
        $this->request->redirect($this->request->detect_uri().URL::query());
    }

    /**
     * 等效 Kohana::config
     * @param <type> $group
     * @return <type>
     */
    function _conf($group)
    {
        return Kohana::config($group);
    }

    /**
     * 显示最新的一条log记录到debug
     */
    function _log()
    {
        $log_file = APPPATH.'logs/'.date('Y/m/d').EXT;

        if(file_exists($log_file)){
            $arr = file($log_file);
            $this->_debug(array_pop($arr));
        }
    }

    /**
     * 设置页面显示的title
     * @param <type> $title html title 名称
     * @param <type> $sep 分隔符
     */
    function _title($title, $sep=' - ')
    {
        $join_title = '';
        if(isset($this->template->_title)){
            $join_title = Html::chars($title).$sep.$this->template->_title;
        }
        else{
            $join_title = Html::chars($title);
        }
        $this->template->_title = $join_title;
    }

    /**
     * 加入自定义的head信息
     * @param <type> $string
     */
    function _head($string)
    {
        if(isset($this->template->_head)){
            $this->template->_head .= $string."\n";
        } else {
            $this->template->_head = $string."\n";
        }
    }

    /**
     * 改变主题
     * @param <type> $theme_name
     */
    function _theme($theme_name)
    {
        $url = 'static/themes/'.$theme_name.'/'.$theme_name;
        $this->_head(Html::style($url.'.css'));
        $this->_head(Html::script($url.'.js'));
    }

    /**
     * 加入页面关键字描述
     * @param <type> $string
     */
    function _keywords($string)
    {
        if(isset($this->template->_keywords)){
            $this->template->_keywords .= $string.",";
        } else {
            $this->template->_keywords = $string.",";
        }
    }

    /**
     * 渲染模板
     * @param <type> $position
     * @param <type> $path
     * @param <type> $params
     */
    function _render($position, $path, $params=null)
    {
        $this->template->$position = View::factory($path, $params);
    }

    /**
     * 增加一层渲染
     * @param <type> $top_or_bottom
     * @param <type> $position
     * @param <type> $path
     * @param <type> $params
     */
    function _render_add($top_or_bottom, $position, $path, $params=null)
    {
        if($top_or_bottom == 'top'){
            $this->template->$position = View::factory($path, $params).$this->template->$position;
        }
        if($top_or_bottom == 'bottom'){
            $this->template->$position .= View::factory($path, $params);
        }
    }

    /**
     * controller 输出 debug 信息
     */
    function _debug()
    {
        $variables = func_get_args();

        $output = Kohana::debug($variables);

        // 执行效率输出
        $app = Profiler::application();
        $exec_sec = number_format($app['current']['time'], 5);
        $exec_mem = number_format($app['current']['memory']/(1024*1024), 5);
        $exec_output = '<i>'.$exec_sec.'(s) / '.$exec_mem.'(m)</i>';

        if(isset($this->template->_debug) AND $this->auto_render){
            $this->template->_debug .= $output.$exec_output;
        } else {
            return "<div class='candy-debug'>{$output}{$exec_output}</div>";
        }
    }

    /**
     * flash message set
     * @param <type> $string 内容
     * @param <type> $class 样式
     */
    function _flash($string, $class='candy-notice')
    {
        $this->_flash_msgs[] = array(
            'class' => $class,
            'content' => $string,
        );

        $this->_sess->set('_flash', $this->_flash_msgs);
    }

    /**
     * 强制将当前页面输出内容下载
     * @param <type> $ofilename
     */
    function _resp2download($ofilename)
    {
        $headers['Content-Type'] = 'application/force-download';
        $headers['Content-Disposition'] = 'inline;filename="'.$ofilename.'"';
        $headers['Pragma'] = 'no-cache';
        $this->request->headers = $headers;
    }

    function after()
    {
        parent::after();
    }
}