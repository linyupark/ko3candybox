<?php

define('VEXT', 'phtml');

class Candy_Ko3 {
    // 默认处于开发模式
    public static $IN_DEVELOPE = TRUE;

    public static function boot() {
        $request = Request::instance($_SERVER['PATH_INFO']);

        try {

            echo $request->execute()->send_headers()->response;

        } catch(Exception $e) {

            if(self::$IN_DEVELOPE == TRUE) {
                throw $e;
                exit;
            }

            if($request->status == 404)
                echo View::factory('errors/404');
            else {
                $view['msg'] = $e->getMessage();
                echo View::factory('errors/500', $view);
            }
        }
    }

    public static function route() {
        Route::set('default', '(<directory>(/<controller>(/<action>(/<id>))))')
                ->defaults(array(
                'directory'  => 'home',
                'controller' => 'index',
                'action'     => 'index',
        ));
    }
}