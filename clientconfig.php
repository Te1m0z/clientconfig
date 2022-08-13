<?php

/*
Plugin Name: Client Config WP
Plugin URI: https://github.com/te1m0z/
Description: Плагин конфигурации сайта
Version: 1.0.0
Author: Dmitriy Balamozhnov
Text Domain: clientconfig
Author URI: https://github.com/te1m0z/
*/

defined('ABSPATH') or die();


/**
 * Главный класс плагина
 * Получить доступ к нему можно из любой точки сайта
 */
class ClientConfig
{
    /**
     * Реализация Singleton
     */
    private static $_instance;

    /**
     * Первоначальная загрузка
     * создание констант плагина
     * подключение класса init
     */
    function __construct()
    {
        define('CLCNF_DIR',  dirname(__FILE__));
        define('CLCNF_URL',  plugins_url('', __FILE__));

        include_once(CLCNF_DIR . '/components/init.php');
        include_once(CLCNF_DIR . '/components/group.php');
        include_once(CLCNF_DIR . '/components/option.php');

        register_activation_hook(__FILE__,   [$this, 'activation']);
        register_deactivation_hook(__FILE__, [$this, 'deactivation']);
        register_uninstall_hook(__FILE__,    [$this, 'deactivation']);
    }

    /**
     * Получение настройки по ключу
     */
    public function get($option_name = null)
    {
        // return $this->api->get($option_name);
    }

    public static function activation()
    {
        clcnf_group::instance()->install();
        clcnf_option::instance()->install();
    }

    public static function deactivation()
    {
        /**
         * Сначала удаление таблицы wp_client_config
         */
        clcnf_option::instance()->uninstall();
        /**
         * Затем удаление таблицы wp_client_config_groups
         */
        clcnf_group::instance()->uninstall();
    }

    /**
     * Реализация Singleton
     */
    public static function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}

function cnf()
{
    return ClientConfig::instance();
}

cnf();
