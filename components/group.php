<?php

defined('ABSPATH') or die();

class clcnf_group
{
    /**
     * Реализация Singleton
     */
    private static $_instance;

    private $table_name = 'client_config_groups';

    function __construct()
    {
        add_action('wp_ajax_clcnf_group_create', [$this, 'create']);
    }

    function install()
    {
        global $wpdb;
        
        $table_name  = $wpdb->prefix . $this::table_name;
        $charset_col = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(10) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            position int(10) DEFAULT '1' NOT NULL,
            PRIMARY KEY (id)
        ) $charset_col";

        $result = $wpdb->query($sql);

        if ($result === FALSE) {
            wp_die('Ошибка: ' . $wpdb->last_error);
        }

        flush_rewrite_rules();
    }

    function uninstall()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $this::table_name;

        $sql = "DROP TABLE IF EXISTS $table_name";

        $result = $wpdb->query($sql);

        if ($result === FALSE) {
            wp_die('Ошибка: ' . $wpdb->last_error);
        }
    }

    function create()
    {
        global $wpdb;

        $errors = [];
        $data   = [];

        if (empty($_POST['name']['value']))
        {
            $errors[] = [
                'text' => 'Имя обязательно.',
                'selector' => $_POST['name']['selector']
            ];
        }

        $table_name = $wpdb->prefix . $this->table_name;
        $name = $_POST['name']['value'];
        $sql = "SELECT * FROM $table_name WHERE `name` = '$name' LIMIT 1";

        $result = $wpdb->get_results($sql);

        if (!empty($result))
        {
            $errors[] = [
                'text' => 'Такое имя занято.',
                'selector' => $_POST['name']['selector']
            ];
        }

        if (!empty($errors)) {

            $data['status'] = false;
            $data['errors']  = $errors;
        
        } else {

            $table_name = $wpdb->prefix . $this->table_name;

            $sql_data = [
                'name' => $name
            ];

            // if ($_POST['position']['value'])
            // {
            //     $sql_data['position'] = $_POST['position']['value'];
            // }

            $result = $wpdb->insert($table_name, $sql_data);

            $data['status'] = true;
            $data['message'] = 'Успешно создано.';
        }

        echo json_encode($data);
        exit;
    }

    function html()
    {
        ?>
        <form id="clcnf_group_form">
            <h3>Создать группу</h3>
            <div id="clcnf_group_msg" class="clcnf_msg"></div>
            <div class="form-element">
                <label for="clcnf_group_name">Название:</label>
                <input type="text" id="clcnf_group_name" name="clcnf_group_name" />
            </div>
            <div class="form-element">
                <label for="clcnf_group_position">Позиция:</label>
                <input
                    type="number"
                    name="clcnf_group_position"
                    id="clcnf_group_position"
                    placeholder="1"
                    min="1"
                />
            </div>
            <?php submit_button('Создать', 'primary', false, false); ?>
        </form>

        <script>
            const msg_node    = $('#clcnf_group_msg');
            const form_inputs = $('#clcnf_group_form form input');
            const form_node   = $('#clcnf_group_form');

            form_node.on('submit', function(event) {
                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'clcnf_group_create',
                        name: {
                            selector: '#clcnf_group_name',
                            value: $('#clcnf_group_name').val()
                        },
                        position: {
                            selector: '#clcnf_group_position',
                            value: $('#clcnf_group_position').val()
                        }
                    },
                    dataType: 'json',
                    encode: true,
                    error: function(error) {
                        msg_node.text('Код ошибки: ' + error.status);
                        msg_node.addClass('display');
                    },
                    success: function(res) {
                        form_inputs.removeClass('error');
                        msg_node.text('');
                        msg_node.hide();
                        msg_node.removeClass(['notice', 'notice-error', 'notice-success']);
                        
                        if (res.status === false) {
                            for (let error of res.errors) {
                                $(error.selector).addClass('error');
                                document.getElementById('clcnf_group_msg').innerHTML += `<p>${error.text}</p>`;
                            }
                            msg_node.addClass('notice notice-error');
                            msg_node.is(':visible') ? msg_node.fadeOut(400, () => msg_node.fadeIn()) : msg_node.fadeIn();
                        }

                        if (res.status === true) {
                            msg_node.addClass('notice notice-success');
                            document.getElementById('clcnf_group_msg').innerHTML = `<p>${res.message}</p>`;
                            msg_node.is(':visible') ? msg_node.fadeOut(400, () => msg_node.fadeIn()) : msg_node.fadeIn();
                            form_node.trigger('reset');
                        }

                        console.log(res);
                    }
                });

                event.preventDefault();
            });
        </script>
        <?php
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


clcnf_group::instance();