<?php

    if (!defined('ABSPATH')) exit;

    if ($method == 'POST') {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $authenticate = $api->authenticate(
            $email, 
            $password
        );

        $token = (isset($authenticate->token) ? $authenticate->token : null);

        if ($token !== null) {

            add_option($config['OPTION_NAMES']['TOKEN'], $token);

            echo '<script>window.location.replace("admin.php?page=fastpages");</script>';

        }
        else {

            echo 'invalid credentials!';

        }

    }

    include $config['VIEWS_DIR'] . 'authenticate.php';

?>