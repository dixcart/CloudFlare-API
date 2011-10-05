<?php
/**
 * CloudFlare Client API
 * 
 * 
 * @author AzzA <azza@broadcasthe.net>
 * @copyright omgwtfhax inc. 2011
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
		$this->email = $email;
		$this->token_key = $token_key;
		if(!$this->email){ die("Must provide a valid email"); }
		if(!$this->token_key){ die("Must Provide a valid api key"); }
	}
	
	/**
	 * You can add an IP address to your whitelist.
	 * 
	 * @param string $ip the ip address
	 */
	public function whitelist_ip($ip){
		$data = array();
		$data['a'] = "wl";
		$data['key'] = $ip;
		return $this->http_post($data);
	}
	
	/**
	 * You can add an IP address to your blacklist.
	 * 
	 * @param string $ip the ip address
	 */
	public function blacklist_ip($ip){
		$data = array();
		$data['a'] = "ban";
		$data['key'] = $ip;
		return $this->http_post($data);
	}
	
	/**
	 * HTTP POST a specific task with the supplied data
	 *
	 * @param string $task
	 * @param array $data
	 * @return array
	 */
	private function http_post ($data) {
		 $data['u'] = $this->email;
		 $data['tkn'] = $this->token_key;
	     $ch = curl_init();
	     curl_setopt($ch, CURLOPT_VERBOSE, 0);
	     curl_setopt($ch, CURLOPT_FORBID_REUSE, true); 
	     curl_setopt($ch, CURLOPT_URL, self::URL);
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	     curl_setopt($ch, CURLOPT_POST, 1);
	     curl_setopt($ch, CURLOPT_POSTFIELDS, $data ); 
	     curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
	     $http_result = curl_exec($ch);
	     $error = curl_error($ch);
	     $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
	     curl_close($ch);
	   
	     if ($http_code != 200) {
		     return array("error"=>$error);
	     } else {
		     return json_decode($http_result);
	     }
	}
}


