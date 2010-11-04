<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'not_empty'    => ':field不能为空',
	'matches'      => ':field必须跟(:param1)相同',
	'regex'        => ':field格式不符合要求',
	'exact_length' => ':field长度必须为(:param1)位',
	'min_length'   => ':field长度不得小于(:param1)位',
	'max_length'   => ':field长度不得超过(:param1)位',
	'in_array'     => ':field必须为一个有效的选项',
	'digit'        => ':field必须为数字',
	'decimal'      => ':field必须含(:param1)位小数',
	'range'        => ':field有效范围(:param1 ~ :param2)',
        'email'        => '必须填写真实有效的电子邮箱',
        'url'          => ':field必须为有效的链接',
        'numeric'      => ':field必须为数字',
        'chinese'      => ':field必须为中文字符',
        'num_eq'       => ':field应该等于:param1',
        'date'         => ':field不是有效的时期格式',
        'phone'        => ':field不是有效的号码格式',
        'Upload::type' => '上传文件类型无效',
        'Upload::not_empty' => '请选择上传文件',
        'Upload::size' => '上传文件超过尺寸'
);