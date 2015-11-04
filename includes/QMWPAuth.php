<?php

class QMWPAuth
{
    const API_HOST = 'https://app.quantimo.do:443';
    //const API_HOST = 'https://quantimodo-quantimodo-v1.p.mashape.com';

    public $httpUtil;
    public $clientEnabled;
    public $clientId;
    public $clientSecret;
    public $redirectUri;
    public $scope;
    public $urlAuth;
    public $urlToken;
    public $urlUser;

    function __construct()
    {
        $this->httpUtil = get_option('qmwp_http_util');
        $this->clientEnabled = get_option('qmwp_quantimodo_api_enabled');
        $this->clientId = get_option('qmwp_quantimodo_api_id');
        $this->clientSecret = get_option('qmwp_quantimodo_api_secret');
        $this->redirectUri = rtrim(site_url(), '/') . '/';
        $this->scope = 'writemeasurements readmeasurements';

        $this->urlAuth = QMWPAuth::API_HOST . '/api/oauth2/authorize?';
        $this->urlToken = QMWPAuth::API_HOST . '/api/oauth2/token?';
        $this->urlUser = QMWPAuth::API_HOST . '/api/user/me?';

    }

    /**
     * Redirects to authentication URL
     * Asks API server for oauth code
     *
     * @param QMWP $qmwp
     */
    public function get_oauth_code($qmwp)
    {
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => $this->scope,
            'state' => uniqid('', true),
            'redirect_uri' => $this->redirectUri,
        );
        $_SESSION['QMWP']['STATE'] = $params['state'];
        $url = $this->urlAuth . http_build_query($params);
        header("Location: $url");
        exit;
    }

    /**
     * @param QMWP $qmwp
     * @param $code
     * @return bool
     */
    public function get_oauth_token($qmwp, $code)
    {
        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        );
        $url_params = http_build_query($params);
        switch (strtolower($this->httpUtil)) {
            case 'curl':
                $url = $this->urlToken . $url_params;
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, (get_option('qmwp_http_util_verify_ssl') == 1 ? 1 : 0));
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, (get_option('qmwp_http_util_verify_ssl') == 1 ? 2 : 0));

                $result = curl_exec($curl);
                break;
            case 'stream-context':
                $url = rtrim($this->urlToken, "?");
                $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $url_params,
                    )
                );
                $context = $context = stream_context_create($opts);
                $result = @file_get_contents($url, false, $context);
                if ($result === false) {
                    $qmwp->qmwp_end_login("Sorry, we couldn't log you in. Could not retrieve access token via stream context. Please notify the admin or try again later.", true);
                }
                break;
        }

        if (!$this->populate_session_vars($result)) {
            $message = "Sorry, we couldn't log you in. Malformed access token result detected. Please notify the admin or try again later.";
            $serverErrorMessage = $this->get_error_message_from_response($result);
            if ($serverErrorMessage) {
                $message .= $serverErrorMessage;
            }
            $qmwp->qmwp_end_login($message, true);
        }
    }

    /**
     * @param QMWP $qmwp
     * @param string $refresh_token
     * @return bool
     */
    public function refresh_oauth_token($qmwp, $refresh_token)
    {
        $params = array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refresh_token,
        );
        $url_params = http_build_query($params);

        switch (strtolower($this->httpUtil)) {
            case 'curl':
                $url = $this->urlToken . $url_params;
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, (get_option('qmwp_http_util_verify_ssl') == 1 ? 1 : 0));
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, (get_option('qmwp_http_util_verify_ssl') == 1 ? 2 : 0));

                $result = curl_exec($curl);
                break;
            case 'stream-context':
                $url = rtrim($this->urlToken, "?");
                $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $url_params,
                    )
                );
                $context = $context = stream_context_create($opts);
                $result = @file_get_contents($url, false, $context);
                if ($result === false) {
                    $qmwp->qmwp_end_login("Sorry, we couldn't log you in. Could not retrieve access token via stream context. Please notify the admin or try again later.", true);
                }
                break;
        }

        if (!$this->populate_session_vars($result)) {
            $message = "Sorry, we couldn't log you in. Malformed access token result detected. Please notify the admin or try again later.";
            $serverErrorMessage = $this->get_error_message_from_response($result);
            if ($serverErrorMessage) {
                $message .= $serverErrorMessage;
            }
            $qmwp->qmwp_end_login($message, true);
        }
    }

    /**
     * @param QMWP $qmwp
     * @return array
     */
    public function get_oauth_identity($qmwp)
    {
        // here we exchange the access token for the user info...
        // set the access token param:
        $params = array(
            'access_token' => $_SESSION['QMWP']['ACCESS_TOKEN'], // PROVIDER SPECIFIC: the access_token is passed to QuantiModo via POST param
        );
        $url_params = http_build_query($params);
        // perform the http request:
        switch (strtolower($this->httpUtil)) {
            case 'curl':
                $url = $this->urlUser . $url_params; // TODO: we probably want to send this using a curl_setopt...
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($curl);
                $result_obj = json_decode($result, true);
                break;
            case 'stream-context':
                $url = rtrim($this->urlUser, "?");
                $opts = array('http' =>
                    array(
                        'method' => 'GET',
                        // PROVIDER NORMALIZATION: Reddit/Github requires User-Agent here...
                        'header' => "Authorization: Bearer " . $_SESSION['QMWP']['ACCESS_TOKEN'] . "\r\n" . "x-li-format: json\r\n", // PROVIDER SPECIFIC: i think only LinkedIn uses x-li-format...
                    )
                );
                $context = $context = stream_context_create($opts);
                $result = @file_get_contents($url, false, $context);
                if ($result === false) {
                    $qmwp->qmwp_end_login("Sorry, we couldn't log you in. Could not retrieve user identity via stream context. Please notify the admin or try again later.", true);
                }
                $result_obj = json_decode($result, true);
                break;
        }
        // parse and return the user's oauth identity:
        $oauth_identity = array();
        $oauth_identity['provider'] = $_SESSION['QMWP']['PROVIDER'];
        $oauth_identity['id'] = $result_obj['id']; // PROVIDER SPECIFIC: QuantiModo returns the user's OAuth identity as id
        $oauth_identity['email'] = $result_obj['email'];
        $oauth_identity['displayName'] = $result_obj['displayName'];
        $oauth_identity['loginName'] =   $result_obj['loginName'];
        if (!$oauth_identity['id']) {
            $qmwp->qmwp_end_login("Sorry, we couldn't log you in. User identity was not found. Please notify the admin or try again later.", true);
        }
        return $oauth_identity;
    }

    /**
     * Pareses repsonse values and populates session variables with them
     * Returns false when argument does not contain needed values
     *
     * @param $values
     * @return bool
     */
    private function populate_session_vars($values)
    {
        // parse the result:
        $result_obj = json_decode($values, true); // PROVIDER SPECIFIC: QuantiModo encodes the access token result as json by default
        $access_token = isset($result_obj['access_token']) ? $result_obj['access_token'] : null; // PROVIDER SPECIFIC: this is how QuantiModo returns the access token KEEP THIS PROTECTED!
        $expires_in = isset($result_obj['access_token']) ? $result_obj['expires_in'] : 0; // PROVIDER SPECIFIC: this is how QuantiModo returns the access token's expiration
        $expires_at = time() + $expires_in;
        $refresh_token = isset($result_obj['refresh_token']) ? $result_obj['refresh_token'] : null;
        // handle the result:
        if (!$access_token || !$expires_in) {
            return false;
        } else {
            $_SESSION['QMWP']['ACCESS_TOKEN'] = $access_token;
            $_SESSION['QMWP']['REFRESH_TOKEN'] = $refresh_token;
            $_SESSION['QMWP']['EXPIRES_IN'] = $expires_in;
            $_SESSION['QMWP']['EXPIRES_AT'] = $expires_at;
            return true;
        }

    }

    /**
     * This method will parse response and will return error description
     * If field with description will be not found - it will return null
     * @param $response
     * @return string|null
     */
    private function get_error_message_from_response($response)
    {
        $parsedResponse = json_decode($response, true);
        return isset($parsedResponse['error_description']) ? " Error description: '" . $parsedResponse['error_description'] . "'" : null;
    }

}