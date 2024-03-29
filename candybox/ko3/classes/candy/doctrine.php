<?php

# 一些路径的定义
defined('CANDYPATH') OR define('CANDYPATH', DOCROOT.'candybox/');
define('DOCTRINEPATH', CANDYPATH.'Doctrine/');
define('ORMPATH', APPPATH.'orm/');

class Candy_Doctrine
{
    public static $_db;
    public static $_doctrined;

    /**
     * 初始化工作，可以不手动执行
     */
    public static function init()
    {
        if( ! self::$_doctrined){
            $compiled_file = CANDYPATH.'Doctrine.compiled.php';
            if(file_exists($compiled_file)){
                require $compiled_file;
                self::$_doctrined = 'compiled';
            } else {
                require DOCTRINEPATH.'Core.php';
                spl_autoload_register('Doctrine_Core::autoload');
                self::$_doctrined = 'normal';
            }
            // 基础设置
            $manager = Doctrine_Manager::getInstance();
            $manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, 1);
            $manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, 1);
            spl_autoload_register('Candy_Doctrine::autoloadModel');
        }
    }

    /**
     * 自动加载model以及base model
     * @param <type> $class
     * @return <type>
     */
    public static function autoloadModel($class)
    {
        $import = new Doctrine_Import_Schema();
        $folder = $import->getOption('baseClassesDirectory');

        $model_path = ORMPATH.$class.'.php';
        $base_path = ORMPATH.$folder.'/'.$class.'.php';

        if(file_exists($model_path)){
            require $model_path;
            return true;
        }

        if(file_exists($base_path)){
            require $base_path;
            return true;
        }

        return false;
    }

    /**
     * 编译 doctrine 成单文件
     * @param <type> $drivers
     */
    public static function compile($drivers=array('mysql','sqlite'))
    {
        self::init();
        Doctrine_Compiler::compile(null, $drivers);
    }

    /**
     * 调用doctrine创建一个数据库连接
     * @param <type> $db_uri mysql://user:pass@where/dbname
     * @param <type> $conn_name unique name
     * @param <type> $charset default = utf8
     * @return <type> connection
     */
    public static function conn($config='db')
    {
        self::init();

        // 获取设置信息
        $db_info = Kohana::config($config);

        try{
            $conns = $db_info['connections'];
            foreach($conns as $conn_name => $db){
                $conn = Doctrine_Manager::connection($db['uri'], $conn_name);
                $conn->setCharset($db['charset']);
                self::$_db[$conn_name] = $conn;
            }
            if($db_info['cache'] == TRUE){
                self::cache();
            }
        } catch (Exception $e) {
            throw new Exception("
                candy doctrine connection config file is not correct.
                please check apppath/config/{$config} is exists.
            ");
        }
    }

    /**
     * 使用 result sqlite cache 以及 query apc cache
     */
    public static function cache()
    {
        self::init();
        $manager = Doctrine_Manager::getInstance();
        $cache_file = CANDYPATH.'rc.cache';
        $cache_conn = Doctrine_Manager::connection('sqlite:///'.$cache_file, 'result_cache');
        $cache_driver = new Doctrine_Cache_Db(array('connection' => $cache_conn, 'tableName' => 'result'));
        if( ! file_exists($cache_file)){
            $cache_driver->createTable();
        }
        $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $cache_driver);
    }

    /**
     * 根据当前的数据库生成model
     * @param <array> $conns
     */
    public static function db2model($conns, $path=null)
    {
        self::init();
        $orm_model_path = $path ? $path : ORMPATH;
        Doctrine_Core::generateModelsFromDb($orm_model_path, $conns);
    }

    /**
     * 手动加载需要的 model
     * @param <array> $models
     */
    public static function loadModel(array $models)
    {
        self::init();
        foreach($models as $model){
            require ORMPATH.'generated/Base'.$model.'.php';
            require ORMPATH.$model.'.php';
        }
    }
}