<?php

function get_qm_access_token() {
    $wpUserId = get_current_user_id();
    if (!$wpUserId) {
        qm_error("No user ID found");
        return null;
    }
    $accessToken = get_user_meta($wpUserId, 'qm_access_token', true);
    if($accessToken){
        //qm_error("Returning access token from user meta");
        return $accessToken;
    }
    // If options are empty, then exit
    if( empty( $options = qm_settings() ) ){
        qm_error("No QuantiModo settings found");
        return null;
    }
    $qmClientId = qm_api_client_id();
    $qmClientSecret = $options['quantimodo_client_secret'] ?? null;
    $apiHostName = qm_api_hostname();
    $wpUser = wp_get_current_user();
    $userData = clone $wpUser->data;
    $userData->username = $userData->user_login = $qmClientId . '-' . $userData->ID;
    $userData->client_id = $qmClientId;
    $userData->client_secret = $qmClientSecret;
    $userData->client_user_id = $wpUserId;
    unset($userData->ID);

    $accessToken = get_user_meta($wpUserId, 'qm_access_token', true);
    if (!$accessToken) {
        $args = [
            'timeout' => 10,
            'body' => [
                'clientUserId' => $wpUserId,
                'clientUser' => $userData,
                'client_id' => $qmClientId,
                'client_secret' => $qmClientSecret
            ]
        ];
        $url = $apiHostName . '/api/v1/user';
        if (!$qmClientSecret) {
            qm_error('Cannot get quantimodo user because no_quantimodo_client_secret! ' . quantimodo_get_client_secret_instructions());
            return null;
        }
        $response = wp_remote_post($url, $args);
        if ($response instanceof WP_Error) {
            qm_error($response->get_error_message());
            return null;
        }
        $body = json_decode($response['body'], false);
        if (isset($body->user)) {
            $qmUser = $body->user;
            if (isset($qmUser->accessToken)) {
                $accessToken = $qmUser->accessToken;
                add_user_meta($wpUserId, 'qm_access_token', $accessToken);
            }
        }
    }
    return $accessToken;
}
