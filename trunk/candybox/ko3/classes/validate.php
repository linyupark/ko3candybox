<?php defined('SYSPATH') or die('No direct script access.');

class Validate extends Kohana_Validate
{
    public function as_array($withGET = FALSE)
    {
        $data = $this->getArrayCopy();

        if($_POST){
            foreach($_POST as $k => $v){
                $data[$k] = isset($data[$k])?$data[$k]:$v;
            }
        }

        if($withGET AND $_GET){
            foreach($_GET as $k => $v){
                $data[$k] = isset($data[$k])?$data[$k]:$v;
            }
        }

        return $data;
    }

    /**
     * 指定的key不能为空
     * @param <type> $fields array('key', 'key2, ...)
     */
    public function not_empty_all($fields)
    {
        foreach($fields as $field){
            parent::rule($field, 'not_empty');
        }
    }

    // 中文校验
    public static function chinese($str)
    {
        return (bool) preg_match("/^[\x7f-\xff]+$/", $str);
    }

    // 数字相等
    public static function num_eq($num_x, $num_y)
    {
        return (bool) ((int)$num_x == (int)$num_y);
    }
}