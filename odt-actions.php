<?php 

$action = $_REQUEST['odt_action'];

switch($action){
	case 'odt-search-building-name':
		if(isset($_REQUEST['keywords'])){
			$keywords = $_REQUEST['keywords'];
    		$response = odt_api::searchBuildingName( $keywords );

    		if($response){
    			echo json_encode( array('status'=>ODT_API_SUCCESS,'data'=>$response) );
    		}else{
    			$error = odt_api::getError();
    			echo json_encode( array('status'=>ODT_API_ERROR,'error'=>$error) );
    		}
		}

		break;

	case 'odt-get-scripts':
		if(isset($_REQUEST['odt_language']) && isset($_REQUEST['odt_building_ref_id']) && isset($_REQUEST['odt_width']) && isset($_REQUEST['odt_height'])){
			$odt_language = $_REQUEST['odt_language'];
			$building_ref_id = $_REQUEST['odt_building_ref_id'];
			$width = $_REQUEST['odt_width'];
			$height = $_REQUEST['odt_height'];
    		$response = odt_api::getScripts( $building_ref_id, $width, $height );

    		if($response){
    			$response = (object) $response; // always parse to object to make sure it's an object.
    			odt_save_scripts($response->data_id, $response->script_en . '<br>', $response->script_zh . '<br>');

    			echo json_encode( array('status'=>ODT_API_SUCCESS,'data'=> odt_get_shortcode($response->data_id, $odt_language) ) );
    		}else{
    			$error = odt_api::getError();
    			echo json_encode( array('status'=>ODT_API_ERROR,'error'=>$error) );
    		}
		}

		break;
}