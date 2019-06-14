<?php

    if (!defined('ABSPATH')) exit;

    $uuid = $_POST['uuid'];
    $slug = $_POST['slug'];

    $wpdb->query('INSERT INTO ' . $config['DATABASE_TABLE'] . ' (uuid, slug, created) VALUES ("' . $uuid . '", "' . $slug . '", "' . time() . '")');

?>