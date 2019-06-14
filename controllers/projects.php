<?php

    if (!defined('ABSPATH')) exit;

    $projects = $api->projects();
    $connected = [];

    $query = 'SELECT * FROM ' . $config['DATABASE_TABLE'];
    
    foreach ($wpdb->get_results($query) as $connect)
        $connected[$connect->uuid] = $connect->slug;

    include $config['VIEWS_DIR'] . 'projects.php';

?>