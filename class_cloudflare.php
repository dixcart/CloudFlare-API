<?php
/**
 * CloudFlare Client API
 * 
 * 
 * @author AzzA <azza@broadcasthe.net>
 * @copyright omgwtfhax inc. 2010
 */
class cloudflare_api {
	
	/**
	 * The URL of the API
	 */
	const URL = 'https://www.cloudflare.com/api_json.html';
	
	/**
	 * Timeout for the API requests in seconds
	 */
	const TIMEOUT = 5;
	
	/**
	 * Stores the api key
	 *
	 * @var string
	 */
	private $token_key;
	
	/**
	 * Stores the email login
	 * 
	 * @var string
	 */
	private $email;
	
	/**
	 * Make a new instance of the API client
	 * 
	 * @param string $email your login email address
	 * @param string $token_key your api key
	 */
	public function __construct ($email, $token_key) {
		$this->api_key = $api_key;
		$this->secret = $secret;
		$this->default_icon_url = $default_icon_url;
	}
	
	/**
	 * HTTP POST a specific task with the supplied data
	 *
	 * @param string $task
	 * @param array $data
	 * @return array
	 */
	private function http_post () {
	}
}


