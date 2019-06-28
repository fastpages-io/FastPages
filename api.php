<?php

    if (!defined('ABSPATH')) exit;

    class FastPagesAPI {

        var $config;
        var $secured;

        function __construct($init) {

            $this->config = $init['config'];
            $this->secured = [
                '/project'
            ];

        }

        private function call($endpoint, $method = 'GET', $data = []) {

            $ch = curl_init();

            $api = $this->config['API_ENDPOINT'];

            $headers = [
                'Accept: application/json', 
                'Plugin: FastPages ' . $this->config['PLUGIN_VERSION']
            ];

            if ($this->secure($endpoint)) {

                $token = get_option(
                    $this->config['OPTION_NAMES']['TOKEN']
                );

                $headers[] = 'x-api-key: ' . $token;
                
            }
            
            curl_setopt($ch, CURLOPT_URL, $api . $endpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            if ($method == 'POST') {

                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                    json_encode($data)
                );

            }
            else {

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

            }
 
            $response = json_decode(
                curl_exec($ch)
            );
        
            curl_close($ch);

            return $response;

        }
        
        private function secure($endpoint) {
            
            foreach ($this->secured as $name) {
                
                if (stripos($endpoint, $name) !== false) {
                    
                    return true;
                    
                }
                
            }
            
            
            return false;
            
        }

        public function authenticate($email, $password) {

            $call = $this->call(
                '/authentication/login', 
                'POST', 
                [
                    'email' => $email,
                    'password' => $password
                ]
            );

            return $call;

        }

        public function projects() {

            $call = $this->call(
                '/project'
            );

            return $call;

        }

        public function single($uuid) {

            $call = $this->call(
                '/project/' . $uuid
            );

            return $call;

        }

    }

?>