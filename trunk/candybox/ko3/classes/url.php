<?php defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL
{
    static function query(array $params = NULL, $with_get = true)
    {
        if ($params === NULL && $with_get == TRUE) {
            // Use only the current parameters
            $params = $_GET;
        }
        else {
            if($with_get == TRUE)
                $params = array_merge($_GET, $params);
        }

        if (empty($params)) {
            // No query parameters
            return '';
        }

        return '?'.http_build_query($params, '', '&');
    }
}