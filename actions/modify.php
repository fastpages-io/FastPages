<?php

    if (!defined('ABSPATH')) exit;

    $uuid = $_POST['uuid'];
    $slug = $_POST['slug'];

    $wpdb->query('UPDATE ' . $config['DATABASE_TABLE'] . ' SET slug = "' . $slug . '" WHERE uuid = "' . $uuid . '"');

?>