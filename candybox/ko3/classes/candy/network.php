<?php

class Candy_Network
{
    /**
     * 通过指定URL地址模拟各种通讯，并返回结果。
     * @param <string> $method POST/GET/DELETE/UPDATE...
     * @param <string> $url
     * @param <array> $data
     * @param <int> $timeout default 10
     * @return <string>
     */
    public static function httpRequest($method, $url, $data, $timeout=10)
    {
        $method = strtoupper($method);
        $content = http_build_query($data, '', '&');

        if($method == 'GET'){
            $url .= '?'.$content;
            $content = null;
        }

        $context['http'] = array(
            'method' => strtoupper($method),
            'timeout' => $timeout,
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $content,
        );

        $context = stream_context_create($context);

        return file_get_contents($url, false, $context);
    }
}