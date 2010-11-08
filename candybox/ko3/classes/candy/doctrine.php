<?php

# һЩ·���Ķ���
defined('CANDYPATH') OR define('CANDYPATH', DOCROOT.'candybox/');
define('DOCTRINEPATH', CANDYPATH.'Doctrine/');
define('ORMPATH', APPPATH.'orm/');

class Candy_Doctrine
{
    public static $_db;
    public static $_doctrined;

    /**
     * ��ʼ�����������Բ��ֶ�ִ��
     */
    public static function init()
    {
        if( ! self::$_doctrined){
            $compiled_file = CANDYPATH.'Doctrine.compiled.php';
            if(file_exists($compiled_file)){
                require_once $compiled_file;
                self::$_doctrined = 'compiled';
            } else {
                require_once DOCTRINEPATH.'Core.php';
                spl_autoload_register(array('Doctrine_Core','autoload'));
                self::$_doctrined = 'normal';
            }
            // ��������
            $manager = Doctrine_Manager::getInstance();
            $manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, 1);
            $manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, 1);
        }
    }

    /**
     * ���� doctrine �ɵ��ļ�
     * @param <type> $drivers
     */
    public static function compile($drivers=array('mysql','sqlite'))
    {
        self::init();
        Doctrine_Compiler::compile(null, $drivers);
    }

    /**
     * ����doctrine����һ�����ݿ�����
     * @param <type> $db_uri mysql://user:pass@where/dbname
     * @param <type> $conn_name unique name
     * @param <type> $charset default = utf8
     * @return <type> connection
     */
    public static function conn($config='db')
    {
        self::init();

        // ��ȡ������Ϣ
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
     * ʹ�� result sqlite cache �Լ� query apc cache
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
     * ���ݵ�ǰ�����ݿ�����model
     * @param <array> $conns
     */
    public static function db2model($conns, $path=null)
    {
        self::init();
        $orm_model_path = $path ? $path : ORMPATH;
        Doctrine_Core::generateModelsFromDb($orm_model_path, $conns);
    }

    /**
     * �ֶ�������Ҫ�� model
     * @param <array> $models
     */
    public static function loadModel(array $models)
    {
        self::init();
        foreach($models as $model){
            require_once ORMPATH.'generated/Base'.$model.'.php';
            require_once ORMPATH.$model.'.php';
        }
    }
}