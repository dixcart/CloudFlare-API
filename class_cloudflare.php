<?php
/**
 * CloudFlare Client API
 * 
 * 
 * @author AzzA <azza@broadcasthe.net>
 * @copyright omgwtfhax inc. 2011
 * @version 1.0
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
	 public function purge_cache($mode, $domain){
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
	 * Set Cache Level - This function sets the Caching Level to Aggressive or Basic. (agg|basic)
	 */
	public function set_cache_lvl($mode, $domain){
		$data['a'] = "cache_lvl";
		$data['z'] = $domain;
		$data['v'] = ($mode == 'agg') ? 'agg' : 'basic';
		return $this->http_post($data);
	}
	
	/**
	 * Set Security Level - This function sets the Basic Security Level to HIGH / MEDIUM / LOW / ESSENTIALLY OFF.
	 * (high|med|low|eoff)
	 */
	public function set_security_lvl($mode, $domain){
		$data['a'] = "sec_lvl";
		$data['z'] = $domain;
		$data['v'] = $mode;
		return $this->http_post($data);
	}
	
	/**
	 * Pull recent IPs hitting your site
	 * Returns a list of IP addresses which hit your site classified by type.
	 * $ZoneID = ID of the zone you would like to check. 
	 * $Hours = Number of hours to go back. Default is 24, max is 48.
	 * $Class = Restrict the result set to a given class. Currently r|s|t, for regular, crawler, threat resp.
	 * $Geo = Optional token. Add to add longitude and latitude information to the response. 0,0 means no data.
	 */
	 public function get_zone_ips($zoneid, $hours, $class, $geo = '0,0'){
		$data['a'] = 'zone_ips';
		$data['zid'] = $zoneid;
		$data['hours'] = $hours;
		$data['class'] = $class;
		$data['geo'] = $geo;
		return $this->http_post($data);
	 }
	 
	 /**
	  * Create a new DNS record - Creates a new DNS record for your site. This can be either a CNAME or A record.
	  * $zone = zone
	  * $type = A|CNAME
	  * $content = The value of the cname or IP address (the destination).
      * $name = The name of the record you wish to create.
      * $mode = 0 or 1. 0 means CloudFlare is off (grey cloud) for the new zone, while 1 means a happy orange cloud.
	  */
	  public function add_dns_record($zone, $type, $content, $name, $mode){
		$data['a'] = 'rec_set';
		$data['type'] = ($type == 'A') ? 'A' : 'CNAME';
		$data['content'] = $content;
		$data['name'] = $name; 
		$data['service_mode'] = ($mode == true) ? 1 : 0;
		return $this->http_post($data);
	  }
	  
	  /**
	   * Update an existing DNS record - Update a DNS record for your site. This needs to be an A record.
	   * $ip = The value of the IP address (the destination).
       * $hosts = The name of the record you wish to create.
	   */
	  public function update_dns_record($ip, $hosts){
		$data['a'] = "DIUP";
		$data['ip'] = $ip;
		$data['hosts'] = $hosts;
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


