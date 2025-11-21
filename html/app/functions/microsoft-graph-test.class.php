<?php
    // Main Microsoft-Graph Class File - Version 1.0 (20-01-2023) 
    /* 
     * File: microsoft-graph.class.php 
     * Description: COnexión Microsoft Graph API
     * Version: 1.0
     * Created: 20-01-2023
     * Modified: 20-01-2023 
     * Author: Mario Ibarguen 
     */ 
      
    /***************** Changes *********************** 
    * 
    * fecha | descripción 
    * 
    **************************************************/ 

    class MicrosoftGraph {
        public $client_id;
        public $tenant;
        public $client_secret;
        public $redirect_uri;
        public $base_url = 'https://graph.microsoft.com/v1.0/';
        public $auth_code;
        public $token = array();
        public $token_refresh;
        public $is_connected = false;
        
        public function get_code() {
    		$url = "https://login.microsoftonline.com/".$this->tenant."/oauth2/v2.0/devicecode?";
            $url .= "state=";  //This at least semi-random string is likely good enough as state identifier
            $url .= "&scope=".urlencode('offline_access user.read mail.read mail.readwrite mail.send');  //This scope seems to be enough, but you can try "&scope=profile+openid+email+offline_access+User.Read" if you like
            $url .= "&response_type=code";
            $url .= "&response_mode=query";
            $url .= "&client_id=".$this->client_id;
            $url .= "&redirect_uri=".urlencode($this->redirect_uri);
            header("Location: " . $url);

            return $json;
        }

    	public function get_token($guzzle, $refresh_token) {
            try {
                $url = 'https://login.microsoftonline.com/'.$this->tenant.'/oauth2/v2.0/token';

                if ($refresh_token) {
                    $response = $guzzle->post($url,
                        [
                            'form_params' => [
                                'client_id' => $this->client_id,
                                // 'client_secret' => $this->client_secret,
                                'scope' => 'https://graph.microsoft.com/.default',
                                'refresh_token' => $this->token_refresh,
                                'grant_type' => 'refresh_token',
                            ]
                        ]
                    );
                } else {
                    $response = $guzzle->post($url,
                        [
                            'form_params' => [
                                'client_id' => $this->client_id,
                                'client_secret' => $this->client_secret,
                                'scope' => 'https://graph.microsoft.com/.default',
                                'device_code' => $this->auth_code,
                                'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                            ]
                        ]
                    );
                }

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
            }

            return $jsonData;
        }

        public function get_folder_id($guzzle) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
            }

            return $jsonData;
        }

        public function get_folder_id_2($guzzle) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders?%24skip=10',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
            }

            return $jsonData;
        }

        public function get_folder_id_3($guzzle) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders?%24skip=20',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
            }

            return $jsonData;
        }

        public function get_mails_folder($guzzle, $folder_id) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders/'.$folder_id.'/messages?top=100',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                return $code;
            }

            return $jsonData;
        }

        public function get_mail_info($guzzle, $folder_id, $mail_id) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders/'.$folder_id.'/messages/'.$mail_id,
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                return $code;
            }

            return $jsonData;
        }

        public function get_mail_attachments($guzzle, $folder_id, $mail_id) {
            try {
                $response = $guzzle->get($this->base_url.'me/mailFolders/'.$folder_id.'/messages/'.$mail_id.'/attachments',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                return $code;
            }

            return $jsonData;
        }

        public function mail_move($guzzle, $folder_id, $mail_id, $folder_id_dest) {
            try {
                $response = $guzzle->post($this->base_url.'me/mailFolders/'.$folder_id.'/messages/'.$mail_id.'/move',
                    [
                     'json' => 
                        [
                            'destinationId' => $folder_id_dest
                        ],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
                return $code;
            }

            return $jsonData;
        }


        

        // public function get_email_body($mid=null,$format='html') { 
        //     if(!$this->is_connected || is_null($mid)) 
        //         return false; 
    		
    	// 	$body = "";
    		
    	// 	if(strtolower($format) == 'html')
    	// 		$body = $this->get_part($this->marubox, $mid, "TEXT/HTML"); 
            
    	// 	if ($body == "") 
        //         $body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN"); 
        //     if ($body == "") { 
        //         return ""; 
        //     } 
        //     return $body; 
        // } 

    	public function get_attachments_body($mid=null) { 
            if(!$this->is_connected || is_null($mid)) 
                return false; 

            $structure = imap_fetchstructure($this->marubox,$mid); 
            $attachments_body = array();
                                
            if(isset($structure->parts[0]->parts) && count($structure->parts[0]->parts)>0) {
                for($j = 0; $j < count($structure->parts[0]->parts); $j++) {
                   $attachments_body[$j] = array(
                      'is_attachment' => false,
                      'filename' => '',
                      'name' => '',
                      'attachment' => '');

                   if($structure->parts[0]->parts[$j]->ifdparameters) {
                     foreach($structure->parts[0]->parts[$j]->dparameters as $object) {
                       if(strtolower($object->attribute) == 'filename') {
                         $attachments_body[$j]['is_attachment'] = true;
                         $attachments_body[$j]['filename'] = imap_utf8($object->value);

                       }
                     }
                   }

                   if($structure->parts[0]->parts[$j]->ifparameters) {
                     foreach($structure->parts[0]->parts[$j]->parameters as $object) {
                       if(strtolower($object->attribute) == 'name') {
                         $attachments_body[$j]['is_attachment'] = true;
                         $attachments_body[$j]['name'] = imap_utf8($object->value);
                       }
                     }
                   }
                   
                   if($attachments_body[$j]['is_attachment']) {
                     $id_imagen="1.".($j+1);
                     $attachments_body[$j]['attachment'] = imap_fetchbody($this->marubox, $mid, $id_imagen);
                     if($structure->parts[0]->parts[$j]->encoding == 3) { // 3 = BASE64
                       $attachments_body[$j]['attachment'] = base64_decode($attachments_body[$j]['attachment']);
                     }
                     elseif($structure->parts[0]->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                       $attachments_body[$j]['attachment'] = quoted_printable_decode($attachments_body[$j]['attachment']);
                     }
                   } 
                }
            } 
            return $attachments_body; 
        }

        public function mail_send($guzzle, $from, $subject, $body, $toRecipients, $ccRecipients, $bccRecipients, $attachments) {
            try {
                $response = $guzzle->post($this->base_url.'me/sendMail',
                    [
                     'json' => 
                        [
                            'message' => [
                                'isDeliveryReceiptRequested' => true,
                                'isRead' => true,
                                'isReadReceiptRequested' => true,
                                'subject' => $subject,
                                'body' => $body,
                                'from' => [
                                  'emailAddress' => [
                                    'address' => $from
                                  ]
                                ],
                                'toRecipients' => $toRecipients,
                                'ccRecipients' => $ccRecipients,
                                'bccRecipients' => $bccRecipients,
                                'attachments' => $attachments,
                            ]
                        ],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                return $code;
            }

            return $jsonData;
        }

        public function get_users($guzzle) {
            try {
                $response = $guzzle->get($this->base_url.'users?$count=true&$search="displayName:mario"&$orderBy=displayName&$select=id,displayName,mail',
                    [
                     'json' => 
                        [],
                        'headers' => [ 'Authorization' => 'Bearer '.$this->token],
                    ]
                );

                $jsonData = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                $reporte_error=$e->getMessage(); // error messages from anything else!
                //Validación excepciones
                settype($reporte_error, 'string');
                if (stristr($reporte_error, '401 Unauthorized')) {
                  $code=401;
                }
                echo $reporte_error;
            }

            return $jsonData;
        }
    } 
?>