<?php
// api.php

/**
 * Get QuantiModo API host
 * @return string
 */
function qm_api_host(){
	if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'qm-wp.test'){
		return "local.quantimo.do";
	}
    return "app.quantimo.do";
}

/**
 * Get QuantiModo API origin
 * @return string
 */
function qm_api_origin(): string {
    $apiHostName = qm_api_host();
    return "https://" . $apiHostName;
}

/**
 * Get App Builder URL
 * @return string
 */
function get_app_builder_url(): string
{
    $appBuilderUrl = APP_BUILDER_URL;
    $qmClientId = qm_api_client_id();
    if($qmClientId){$appBuilderUrl .= "?client_id=" . $qmClientId;}
    return $appBuilderUrl;
}
