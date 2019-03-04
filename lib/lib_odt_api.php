<?php

class odt_api {

    static protected $_app_key = ODT_APP_KEY;
    static protected $_app_secret = ODT_APP_SECRET;

    static protected $_errors = array();


    public static function getScripts($building_ref_id, $width, $height){
        self::$_errors = array();

        if(!$building_ref_id || !is_numeric($building_ref_id)){
            self::setError('Invalid value for Address');
        }
        if(!$width || !is_numeric($width)){
            self::setError('Invalid value for width.');
        }
        if(!$height || !is_numeric($height)){
            self::setError('Invalid value for Height.');
        }
        
        if(!empty( self::getError() )){
            return false;
        }

        $url = ODT_SERVER . 'r/response/oneday/developer/transport/';

        $data = array();
        $data['ref_id'] = $building_ref_id;
        $data['width'] = $width;
        $data['height'] = $height;

        $response = self::postRequest( $url, $data);
        return $response;
    }

    public static function searchBuildingName($keywords){
        self::$_errors = array();

        if(!$keywords){
            self::setError('Invalid Keywords.');
        }

        if(!empty( self::getError() )){
            return false;
        }

        $url = ODT_SERVER . 'bs/?term=' . $keywords;

        $data = array();

        $response = self::postRequest( $url, $data);
        return $response;
    }


    public static function postRequest($url, $data = array()){

        $response  = @Requests::post( $url, array(), $data);

        if(!$response) { 
            self::setError('API Connection Failed.');
            return false;
        }else{

            if($response->status_code == ODT_API_SUCCESS) {
                if(isset($response->body)){
                    $response_body = json_decode($response->body);

                    if(is_object($response_body)){
                        if($response_body->status == ODT_API_SUCCESS) { 
                            if(isset($response_body->data)) { 
                                return $response_body->data;
                            } else { 
                                return true;
                            }
                        }else{
                            if(isset($response_body->error)){
                                self::setError($response_body->error);
                                return false;
                            }
                            self::setError('Something went wrong. Response Status: ' . $response_body->status);
                            return false;
                        }
                    }else if(is_string($response->body)){
                        self::setError($response->body);
                        return false;
                    }
                    
                }else{
                    self::setError('Something went wrong. Missing Response Body.');
                    return false;
                }
            }else{

                self::setError('Something went wrong. Status Code: ' . $response->status_code);
                return false;
            }
        }
    }

    public static function setError($error){
        self::$_errors[] = $error;
    }

    public static function getError(){
        return self::$_errors;
    }
}