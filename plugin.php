<?php

    /*

        Plugin Name: FastPages
        Plugin URI: https://fastpages.io/
        Description: Integrate your FastPages-campaigns directly onto your WordPress-website!
        Version: 1.2.2
        Author: FastPages
        Author URI: https://fastpages.io/
        
    */
    
    if (!defined('ABSPATH')) exit;

    $config = [
        'PLUGIN_VERSION' => '1.2.1',
        'API_ENDPOINT' => 'https://app.fastpages.io/api',
        'DATABASE_TABLE' => $wpdb->prefix . 'fastpages',
        'OPTION_NAMES' => [
            'TOKEN' => 'fastpages_token'
        ],
        'VIEWS_DIR' => __DIR__ . '/views/',
        'ACTIONS_DIR' => __DIR__ . '/actions/',
        'CONTROLLERS_DIR' => __DIR__ . '/controllers/'
    ];

    include __DIR__ . '/api.php';

    add_action('admin_menu', 'fp_admin_pages');
    add_action('admin_enqueue_scripts', 'fpc_admin_sources');
    add_action('parse_request', 'fp_endpoint', 0);

    register_activation_hook(__FILE__, 'fp_enable');
    register_deactivation_hook(__FILE__, 'fp_disable');

    $api = new FastPagesAPI([
        'config' => $config
    ]);

    function fp_endpoint() {

        global $api;
        global $config;
        
        global $wp;
        global $wpdb;

        $slugs = explode(
            '/', 
            $wp->request
        );
        $slug = $slugs[0];

        $query = 'SELECT uuid FROM ' . $config['DATABASE_TABLE'] . ' WHERE slug = "' . $slug . '"';
        $uuid = $wpdb->get_var($query);

        if ($uuid !== null) {

            $project = $api->single($uuid);

            if (($project !== null) && (!isset($project->error))) {
                
                $domains = $project->domains;

                $request = wp_remote_get(
                    'https://' . $domains[0]->domain . '/' . (isset($slugs[1]) ? $slugs[1] : ''), [
                        'httpversion' => '2.0',
                        'user-agent' => 'WordPress/FastPages-Plugin; ' . home_url(),
                        'timeout' => 10,
                        'sslverify' => true,
                        'headers' => [
                            'Plugin-Version' => $config['PLUGIN_VERSION']
                        ]
                    ]
                );
                
                fp_response(
                    wp_remote_retrieve_body($request),
                    $slug
                );

            }
            else {

                echo 'Error: Campaign is no longer published or has been removed by author.';

            }

            exit;

        }

    }

    function fp_admin() {

        global $api;
        global $config;

        global $wp;
        global $wpdb;

        $action = (isset($_GET['action']) ? $_GET['action'] : null);
        $method = $_SERVER['REQUEST_METHOD'];
        $token = get_option(
            $config['OPTION_NAMES']['TOKEN']
        );

        if ($action !== null) {

            include $config['ACTIONS_DIR'] . $action . '.php';

            echo '<script>window.location.replace("admin.php?page=fastpages");</script>';

        }

        if ($token !== false) {

            include $config['CONTROLLERS_DIR'] . 'projects.php';

        }
        else {

            include $config['CONTROLLERS_DIR'] . 'authenticate.php';

        }

    }

    function fp_admin_pages() {

        add_menu_page('FastPages', 'FastPages', 'manage_options', 'fastpages', 'fp_admin', plugins_url(basename(__DIR__) . '/images/icon.svg'), 4);
    
    }

    function fpc_admin_sources() {

        wp_enqueue_style('custom_wp_admin_css', plugins_url('admin.css', __FILE__));

    }

    function fp_enable() {

        global $wpdb;
        
        if (!class_exists('DOMDocument')) {
            
            exit('Whoops! The DOMDocument PHP-extension doesn\'t seem to be enabled or either installed, please contact your hostmaster.');
            
        }

        $database = str_replace(
            [
                '[TABLE_NAME]', 
                '[TABLE_CHARSET]'
            ],
            [
                $wpdb->prefix . 'fastpages',
                $wpdb->get_charset_collate()
            ],
            file_get_contents(__DIR__ . '/database.sql')
        );
        
        $wpdb->query($database);

    }

    function fp_disable() {

        global $wpdb;

        delete_option(
            $config['OPTION_NAMES']['TOKEN']
        );

        $wpdb->query('DROP TABLE ' . $wpdb->prefix . 'fastpages');

    }
    
    function fp_parse($body, $slug) {
        
        $dom = new DOMDocument();
        
        $replace = [
            '/bundle.client.js' => 'https://x.project.fastpages.io/bundle.client.js'
        ];
        
        libxml_use_internal_errors(true);
        
        $dom->loadHTML($body);
        
        $finder = new DomXPath($dom);
        
        $menu_items = $finder->query('//*[contains(@class, "menu-items")]');
        foreach ($menu_items as $menu_item) {
            
            $links = $menu_item->getElementsByTagName('a');
            
            foreach ($links as $link) {
                
                $href = $link->getAttribute('href');
                
                $link->setAttribute('target', '_self');
                
                $link->setAttribute('href', 
                    get_site_url() . '/' . $slug . $href
                );
                
            }
            
        }
        
        $script_items = $finder->query('//script');
        foreach ($script_items as $script_item) {
            
            $src = $script_item->getAttribute('src');
            
            if (strlen($src) !== 0 && $src !== null) {
                
                if (array_key_exists($src, $replace)) {
                    
                    $script_item->setAttribute('src', $replace[$src]);
                    
                }
                
            }
            
        }
        
        return $dom->saveHTML();
        
    }
    
    function fp_response($body, $slug) {
        
        global $config;
        
        header('Content-type: text/html; charset=utf-8');
        header('X-Plugin-Version: FastPages/' . $config['PLUGIN_VERSION']);
        
        $body = fp_parse($body, $slug);
        
        echo html_entity_decode(
            $body
        );
        
        exit;
        
    }

?>