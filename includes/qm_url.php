<?php

function qm_url($params = []) {
    $params = array_merge(array(
        'qmAccessToken' => get_qm_access_token(),
        'clientId' => qm_api_client_id(),
    ), $params);
    $url = ionic_base_url() . "?" . http_build_query($params, '', '&');
    return $url;
}

function ionic_base_url() {
	return qm_api_origin() . '/app/public/#';
}

function ionic_state_url($state, $params = []) {
	$params = array_merge(array(
		'qmAccessToken' => get_qm_access_token(),
		'clientId' => qm_api_client_id(),
	), $params);
	$url = ionic_base_url() . '/app/' . $state . "?" . http_build_query($params, '', '&');
	return $url;
}

function intro_url(){
	return ionic_state_url('intro');
}
