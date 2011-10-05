<?php
/**
 * CloudFlare Client API
 * 
 * 
 * @author AzzA <azza@broadcasthe.net>
 * @copyright omgwtfhax inc. 2011
 */
class cloudflare_api {
	
	//The URL of the API
	const URL = 'https://www.cloudflare.com/api_json.html';
	
	//Timeout for the API requests in seconds
	const TIMEOUT = 5;
	
	//Stores the api key
	private $token_key;
	
	//Stores the email login
	private $email;
	
	//Data to post
	private $data = array;
	
	/**
	 * Make a new instance of the API client
	 */
	public function __construct ($email, $token_key) {
		$this->email = $email;
		$this->token_key = $token_key;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function setToken($token_key){
		$this->token_key = $token_key;
	}
	
	/**
	 * Developer Mode - This function allows you to toggle Development Mode on or off for a particular domain. 
	 * When Development Mode is on the cache is bypassed. Development mode remains on for 3 hours or 
	 * until when it is toggled back off.
	 */
	public function devmode($mode, $domain){
		$data['a'] = "devmode";
		$data['z'] = $domain;
		$data['v'] = ($mode == true) ? 1 : 0;
		return $this->http_post($data);
	}
	
	/**
	 * Purge Cache - This function will purge CloudFlare of any cached files. It may take up to 48 hours for
	 * the cache to rebuild and optimum performance to be achieved so this function should be used sparingly.
	 */
	 public function purge_cache(){
		$data['a'] = "fpurge_ts";
		$data['z'] = $domain;
		$data['v'] = ($mode == true) ? 1 : 0;
		return $this->http_post($data);
	 }
	
	/**
	 * You can add an IP address to your whitelist.
	 */
	public function whitelist_ip($ip){
		$data['a'] = "wl";
		$data['key'] = $ip;
		return $this->http_post($data);
	}
	
	/**
	 * You can add an IP address to your blacklist.
	 */
	public function blacklist_ip($ip){
		$data['a'] = "ban";
		$data['key'] = $ip;
		return $this->http_post($data);
	}
	
	/**
	 * HTTP POST a specific task with the supplied data
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


