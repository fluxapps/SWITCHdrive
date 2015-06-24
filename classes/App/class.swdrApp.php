<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/Client/class.swdrClient.php');

/**
 * Class swdrApp
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrApp {

	const SSL_STANDARD = NULL;
	const SSL_V3 = 3;
	const RESP_TYPE_CODE = 'code';
	/**
	 * @var string
	 */

	protected $base_url = '';
	/**
	 * @var string
	 */
	protected $token_url = '';
	/**
	 * @var string
	 */
	protected $client_id = '';
	/**
	 * @var string
	 */
	protected $response_type = self::RESP_TYPE_CODE;
	/**
	 * @var string
	 */
	protected $redirect_uri = 'https://ilias.phzh.ch/next/od_oauth.php';
	/**
	 * @var string
	 */
	protected $client_secret = '';
	/**
	 * @var string
	 */
	protected $ressource_uri = '';
	/**
	 * @var string
	 */
	protected $ressource = '';
	/**
	 * @var swdrClient
	 */
	protected $swdr_client;
	/**
	 * @var int
	 */
	protected $il_own_cloud;
	/**
	 * @var
	 */
	protected $ssl_version = self::SSL_STANDARD;
	/**
	 * @var
	 */
	protected static $instance;


	/**
	 * @param exodBearerToken $exod_bearer_token
	 * @param                 $client_id
	 * @param                 $client_secret
	 */
	protected function __construct() {
        $swdrClient = new swdrClient($this);
        $this->setSwdrClient($swdrClient);
	}

    /**
     * @param exodBearerToken $exod_bearer_token
     * @param $client_id
     * @param $client_secret
     * @param exodTenant $exodTenant
     * @return swdrApp
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


	public function buildURLs(){

    }


	/**
	 * @return mixed|string
	 */
	public function getHttpPath() {
		$http_path = ILIAS_HTTP_PATH;
		if (substr($http_path, - 1, 1) != '/') {
			$http_path = $http_path . '/';
		}
		if (strpos($http_path, 'Customizing') > 0) {
			$http_path = strstr($http_path, 'Customizing', true);
		}

		return $http_path;
	}


//	/**
//	 * @throws Exception
//	 */
//	public function checkAndRefreshToken() {
//		if ($this->getExodBearerToken()->refresh($this->getExodAuth())) {
//			return true;
//		}
//
//		return false;
//	}

//
//	/**
//	 * @return bool
//	 */
//	public function isTokenValid() {
//		return $this->getExodBearerToken()->isValid();
//	}


//	/**
//	 * @return ilOneDrive
//	 */
//	public function getIlonedrive() {
//		return $this->il_one_drive;
//	}


	/**
	 * @param ilSWITCHdrive $il_own_cloud
	 */
	public function setIlSWITCHdrive(ilSWITCHdrive $il_own_cloud) {
		$this->il_own_cloud = $il_own_cloud;
	}

	/**
	 * @return swdrClient
	 */
	public function getSwdrClient() {
		return $this->swdr_client;
	}


	/**
	 * @param swdrClient $swdr_client
	 */
	public function setSwdrClient($swdr_client) {
		$this->swdr_client = $swdr_client;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->base_url;
	}


	/**
	 * @param string $base_url
	 */
	public function setBaseUrl($base_url) {
		$this->base_url = $base_url;
	}

	/**
	 * @return string
	 */
	public function getRessourceUri() {
		return $this->ressource_uri;
	}


	/**
	 * @param string $ressource_uri
	 */
	public function setRessourceUri($ressource_uri) {
		$this->ressource_uri = $ressource_uri;
	}


	/**
	 * @return string
	 */
	public function getRessource() {
		return $this->ressource;
	}


	/**
	 * @param string $ressource
	 */
	public function setRessource($ressource) {
		$this->ressource = $ressource;
	}
//
//	/**
//	 * @return mixed
//	 */
//	public function getSslVersion() {
//		return $this->ssl_version;
//	}
//
//	/**
//	 * @param mixed $ssl_version
//	 */
//	public function setSslVersion($ssl_version) {
//		$this->ssl_version = $ssl_version;
//	}
//

}


