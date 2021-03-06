<?php
/**
 * Created by PhpStorm.
 * User: Lukas
 * Date: 10.10.2015
 * Time: 20:01
 */

namespace lkdevelopment;

use Firebase\JWT\JWT;

class BreadfishAPIClient
{
    /**
     * Aktuelle Cient Version
     */
    const CLIENT_VERSION = '0.0.2';
    /**
     * Berechtigung fuer Link zum Avatar
     */
    const SCOPE_AVATAR = 'avatar';
    /**
     * Berechtigung fuer  Registrationsdatum
     */
    const SCOPE_REGISTRATION = 'registration';
    /**
     * Berechtigung fuer Anzahl der Posts
     */
    const SCOPE_NUM_POSTS = 'num_posts';
    /**
     * Berechtigung fuer Anzahl der Bedankungen
     */
    const SCOPE_THANKS = 'thanks';
    /**
     * Berechtigung fuer  Banstatus und Bangrund
     */
    const SCOPE_BAN = 'ban';
    /**
     * Berechtigung fuer Anzahl Verwarnungen
     */
    const SCOPE_WARNINGS = 'warnings';
    /**
     * Berechtigung fuer Profilinformationen
     */
    const SCOPE_PROFILE_INFORMATION = 'profile_information';
    /**
     * Berechtigung fuer  E-Mail Adresse
     */
    const SCOPE_EMAIL = 'email';
    /**
     * Berechtigung fuer Private Nachrichten
     */
    const SCOPE_PRIVATE_MESSAGES = 'private_messages';
    /**
     * Berechtigung fuer  Details zu Verwarnungen
     */
    const SCOPE_WARNINGS_FULL = 'warnings_full';
    /**
     * @var string
     */
    protected $apiToken;
    /**
     * @var string
     */
    protected $apiPassword;
    /**
     * @var string
     */
    protected $redirectUrl;
    /**
     * @var array
     */
    protected $scope;
    /**
     * @var string
     */
    private $client_url = 'breadfish.de/oauth/index.php';

    /**
     * BreadfishAPIClient constructor.
     * @param string $apiToken
     * @param string $apiPassword
     */
    public function __construct($apiToken, $apiPassword)
    {
        $this->apiToken = $apiToken;
        $this->apiPassword = $apiPassword;
    }


    /**
     * @param array $scopes
     */
    public function setScope(array $scopes)
    {
        $this->scope = implode(",", $scopes);
    }

    /**
     * @param string $RedirectUrl
     */
    public function setRedirectUrl($RedirectUrl)
    {
        $this->redirectUrl = $RedirectUrl;
    }

    /**
     * @return string
     */
    public function getOAuthUrl()
    {
        $params = array("token" => $this->apiToken, "redirect" => $this->redirectUrl, "scope" => $this->scope);
        return $this->httpOrHttps() . $this->client_url . "?" . http_build_query($params);
    }

    /**
     * @return bool
     */
    public function redirectToBreadfish()
    {
        if (headers_sent() === false) {
            header('Location: ' . $this->getOAuthUrl());
            return true;
        } else {
            echo '<meta http-equiv="refresh" content="0; URL=' . $this->getOAuthUrl() . '">';
            return true;
        }
    }

    /**
     * @return object
     * @throws BreadfishAPIException
     */
    public function getResponseFromBreadfish()
    {
        if (isset($_GET['error'])) {
            throw new BreadfishAPIException($_GET['error']);
        } else {
            if (isset($_POST['response'])) {
                return $this->decodeResponse($_POST['response']);
            } else {
                throw new BreadfishAPIException('response empty');
            }
        }
    }

    /**
     * @param string $response
     * @return object
     */
    private function decodeResponse($response)
    {
        return JWT::decode($response, $this->apiPassword, array('HS256'));
    }

    /**
     * @return string
     */
    private function httpOrHttps()
    {
        if (isset($_SERVER['HTTPS'])) {
            return "https://";
        } else {
            return "http://";
        }
    }

}