<?php
// api.php

/**
 * Get QuantiModo API host
 * @return string
 */
function qm_api_host(){
    return "local.quantimo.do";
    // return "app.quantimo.do"; // This line is unreachable. You might want to add some logic to switch between hosts.
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