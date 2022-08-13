<?php

defined('ABSPATH') or die();


class clcnf_error {

    function __construct()
    {
                
    }

    public function print($msg = '')
    {
        return '<p class="error">' . $msg . '</p>';
    }
}

if (class_exists('clcnf_error')) {
    cnf()->error = new clcnf_error();
}