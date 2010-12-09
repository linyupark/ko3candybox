<?php

/**
 * 人人API模块
 */
class Candy_Renren
{
    protected $server = 'http://api.renren.com/restserver.do';
    protected $version = '1.0';
    protected $format = 'json';
    protected $key = '92f700680cad46c4819b0c153bca7490';
    protected $secret = '6be94a48cfbf4537aaaa2151a79eb3d6';
    protected $user;
    protected $session_key;

    /**
     * 
     * @param <type> $key api的key
     */
    public function __construct($key=null)
    {
        $this->key = $key;
        
        $init_opts = array('user', 'session_key');

        foreach($init_opts as $opt){
            $this->$opt = isset($_COOKIE[$this->key.'_'.$opt]) ? $_COOKIE[$this->key.'_'.$opt]:NULL;
        }
    }

    /**
     * 远程结果
     * @param array $params 各种参数
     * @return <string> 获取到的信息
     */
    public function call(array $params)
    {
        $params['api_key'] = $this->key;
        $params['session_key'] = $this->session_key;
        $params['v'] = $this->version;
        $params['format'] = $this->format;
        $params['sig'] = $this->sig($params);
        
        return Candy_Network::httpRequest('post', $this->server, $params);
    }

    /**
     * 生成 sig
     * @param array $params
     * @return <type>
     */
    public function sig(array $params)
    {
        ksort($params);
        $query = http_build_query($params, null, '&');
        $str = str_replace('&', '', $query);
        $str .= $this->secret;
        
        return md5($str);
    }

    public function uid()
    {
        return $this->user;
    }
}