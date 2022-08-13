<?php

defined('ABSPATH') or die();

class clcnf_init
{
    /**
     * Реализация Singleton
     */
    private static $_instance;
    
    function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    function init()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    function admin_html()
    {
        clcnf_group::instance()->html();
    }

    /**
     * Добавление пункта в админ панель
     */
    function admin_menu()
    {
        $page_name = add_menu_page(
            'Client Config',
            'Client Config',
            'delete_users',
            'client_config',
            [$this, 'admin_html'],
            'dashicons-admin-tools',
            3
        );

        add_action('admin_print_scripts-' . $page_name, [$this, 'enqueue_scripts']);
    }

    /**
     * Добавление кастом стилей и скриптов
     */
    static function enqueue_scripts()
    {
        wp_enqueue_style('main-css',    CLCNF_URL . '/styles.css');
        wp_enqueue_script('preload-js', CLCNF_URL . '/js/preload.js');
        wp_enqueue_script('fields-js',  CLCNF_URL . '/js/fields.js', ['jquery'], false, true);
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

clcnf_init::instance();
