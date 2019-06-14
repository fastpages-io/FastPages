<?php

    if (!defined('ABSPATH')) exit;

    $uuid = $_POST['uuid'];

    $wpdb->query('DELETE FROM ' . $config['DATABASE_TABLE'] . ' WHERE uuid = "' . $uuid . '"');

?>