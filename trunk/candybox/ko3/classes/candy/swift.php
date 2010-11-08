<?php

defined('CANDYPATH') OR define('CANDYPATH', DOCROOT.'candybox/');

class Candy_Swift
{
    protected static $_transport;
    protected static $_config;
    protected static $_mailer;

    /**
     * �ʼ����ܳ�ʼ��
     * @param <type> $mailer
     */
    public static function init($mailer)
    {
        require_once CANDYPATH.'Swift/swift_required.php';

        self::$_mailer = $mailer;

        $config = Kohana::config('mailer.'.$mailer);
        self::$_config = $config;

        $transport = Swift_SmtpTransport::newInstance($config['smtp'])
                        ->setPort($config['port'])
                        ->setUsername($config['username'])
                        ->setPassword($config['password']);

        if(isset($config['enc'])){
            $transport->setEncryption($config['enc']);
        }

        self::$_transport = $transport;
    }

    /**
     * ����html��ʽ���ʼ�
     * @param <type> $mailer config key
     * @param array $from �����ַ => ������
     * @param array $to �ʼ���ַ => �ռ���
     * @param <type> $subject ����
     * @param <type> $body ����
     */
    public static function sendhtml($mailer, array $from, array $to, $subject, $body)
    {
        self::init($mailer);

        $message = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($from)
                    ->setTo($to)
                    ->setBody($body, 'text/html');

        Swift_Mailer::newInstance(self::$_transport)->send($message);
    }
}