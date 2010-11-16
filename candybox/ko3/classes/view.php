<?php defined('SYSPATH') or die('No direct script access.');

class View extends Kohana_View {

    /**
     * Sets the view filename.
     *
     *     $view->set_filename($file);
     *
     * @param   string  view filename
     * @return  View
     * @throws  Kohana_View_Exception
     */
    public function set_filename($file)
    {
            if (($path = Kohana::find_file('views', $file, VEXT)) === FALSE)
            {
                    throw new Kohana_View_Exception('The requested view :file could not be found', array(
                            ':file' => $file,
                    ));
            }

            // Store the file path locally
            $this->_file = $path;

            return $this;
    }

    /**
     * 直接渲染 request
     * @param <type> $uri
     * @param <type> $params
     * @return <string>
     */
    static function render_request($uri, $params=array())
    {
        if(count($params) > 0){
            $_GET = $params;
        }
        return Request::factory($uri)->execute();
    }

    /**
     * 返回链接tab
     * @param <type> $uri
     * @param array $links
     * @param <type> $deep_num 第几层tab
     * @param <type> $style
     * @param <type> $default_tab
     * @return <type>
     */
    static function render_tab($uri, array $links, $deep_num=1, $style='candy-tab', $default_tab='')
    {
        $view['uri'] = $uri;
        $view['links'] = $links;
        $view['key'] = 'tab'.$deep_num;
        $view['cur_tab'] = Arr::get($_GET, $view['key'], $default_tab);
        $view['style'] = $style;

        // 生成query
        $query = array();
        foreach($_GET as $key => $val){
            if( ! preg_match('/tab[1-9]/', $key)){
                $query[$key] = null;
            }
            else {
                $deep = (int)substr($key, -1);
                if($deep <= $deep_num){
                    $query[$key] = $val;
                } else {
                    $query[$key] = null;
                }
            }
        }
        $view['query'] = $query;

        return View::factory('addons/tab_link', $view);
    }

    /**
     * 渲染kindeditor
     * @param <type> $id textarea id
     * @param <type> $attrs ke.attrs
     * @param <type> $init load ke js script
     * @return <type>
     */
    static function render_keditor($id, $attrs=array(), $init=true)
    {
        $base_path = 'candybox/editor/ke/';

        if($init){
            echo Html::script($base_path.'init.js');
        }

        $view['skinsPath'] = URL::base().$base_path.'skins/';
        $view['pluginsPath'] = URL::base().$base_path.'plugins/';

        if(@$attrs['allowUpload']){
            $view['imageUploadJson'] = URL::base().$base_path.'upload_json.php';
        }

        if(@$attrs['allowFileManager']){
            $view['fileManagerJson'] = URL::base().$base_path.'file_manager_json.php';
        }

        $view['id'] = $id;
        $view['attrs'] = $attrs;
        return View::factory('addons/keditor', $view);
    }

    /**
     * 显示 flash message 区块
     * @return <type>
     */
    static function render_flash()
    {
        return View::factory('addons/flash_msg');
    }
}
