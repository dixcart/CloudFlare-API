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
    private $URL = array('USER' => 'https://www.cloudflare.com/api_json.html', 'HOST' => 'https://api.cloudflare.com/host-gw.html');
    
    //Timeout for the API requests in seconds
    const TIMEOUT = 5;
    
    //Stores the api key
    private $token_key;
    private $host_key;
    
    //Stores the email login
    private $email;
    
    //If you want to output an array instead of an object
    private $out_array = false;
    
    //Data to post
    private $data = array();
    
    /**
     * Make a new instance of the API client
     */
    public function __construct() {
        $parameters = func_get_args();
        switch (func_num_args()) {
            case 1:
                //a host API
                $this->host_key  = $parameters[0];
                break;
            case 2:
                //a user request
                $this->email     = $parameters[0];
                $this->token_key = $parameters[1];
                break;
        }
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function setToken($token_key) {
        $this->token_key = $token_key;
    }
    
    //Set to output an associative array instead of an object
    public function useArray($value = true) {
        $this->out_array = $value;
    }
    
    /**
     * Stats about the zone
     * 
     * @param string $domain - zone name (example.com)
     * @param int $interval - 10 = 1 year, 20 = 30 day, 30 = 7 day, 40 = 24 hours, 100 = < 24 hours, 110 = <12 hours, 120 = <6 hours
     * @return array - associative array 
     */
    public function stats($domain, $interval = 20) {
        $data['a']        = "stats";
        $data['z']        = $domain;
        $data['interval'] = $interval;
        return $this->http_post($data);
    }
    
    /**
     * This function allows you to toggle Development Mode on or off for a particular domain. When Development
     * Mode is on the cache is bypassed. Development mode remains on for 3 hours or until when it is toggled back
     * off.
     * 
     * @param bool $mode - true for on, false for off
     * @param string $domain - zone to applt to (e.g. example.com)
     * @return array - associative array
     */
    public function devmode($mode, $domain) {
        $data['a'] = "devmode";
        $data['z'] = $domain;
        $data['v'] = ($mode == true) ? 1 : 0;
        return $this->http_post($data);
    }
    
    /**
     * This function will purge CloudFlare of any cached files. It may take up to 48 hours for the cache to rebuild
     * and optimum performance to be achieved so this function should be used sparingly.
     * 
     * @param bool $mode - whether to actually purge the cache //TODO: is this necessary?
     * @param type $domain - zone (example.com)
     * @return array - associative array
     */
    public function purge_cache($mode, $domain) {
        $data['a'] = "fpurge_ts";
        $data['z'] = $domain;
        $data['v'] = ($mode == true) ? 1 : 0;
        return $this->http_post($data);
    }
    
    /**
     * TODO: Docs say this should be a GET and returns "OK" not JSON.
     * Whitelist an IP on your whole account
     *
     * @param string $ip - the IP address you want to whitelist
     * @return array - associative array 
     */
    public function whitelist_ip($ip) {
        $data['a']   = "wl";
        $data['key'] = $ip;
        return $this->http_post($data);
    }
    
    /**
     * TODO: Docs say this should be a GET and returns "OK" not JSON.
     * Ban an IP on your whole account
     *
     * @param string $ip - the IP address you want to whitelist
     * @return array - associative array 
     */
    public function blacklist_ip($ip) {
        $data['a']   = "ban";
        $data['key'] = $ip;
        return $this->http_post($data);
    }
    
    /**
     * This function sets the Caching Level to Aggressive or Basic.
     * 
     * @param string $mode - "agg" or "basic"
     * @param string $domain - zone (example.com)
     * @return array - associative array 
     */
    public function set_cache_lvl($mode, $domain) {
        $data['a'] = "cache_lvl";
        $data['z'] = $domain;
        $data['v'] = ($mode == 'agg') ? 'agg' : 'basic';
        return $this->http_post($data);
    }
    
    /**
     * This function sets the Basic Security Level to HIGH / MEDIUM / LOW / ESSENTIALLY OFF.
     * 
     * @param string $mode - one of (high|med|low|eoff)
     * @param string $domain - zone (example.com)
     * @return array - associative array
     */
    public function set_security_lvl($mode, $domain) {
        $data['a'] = "sec_lvl";
        $data['z'] = $domain;
        $data['v'] = $mode;
        return $this->http_post($data);
    }
    
    /**
     * Returns a list of IP addresses which hit your site classified by type.
     * 
     * @param int $zoneid - ID of the zone you would like to check. 
     * @param int $hours - Number of hours to go back. Default is 24, max is 48.
     * @param char $class - Restrict the result set to a given class. Currently r|s|t, for regular, crawler, threat resp.
     * @param type $geo - Optional token. Add to add longitude and latitude information to the response. 0,0 means no data.
     * @return array - associative array
     */
    public function get_zone_ips($zoneid, $hours, $class, $geo = '0,0') {
        $data['a']     = 'zone_ips';
        $data['zid']   = $zoneid;
        $data['hours'] = $hours;
        $data['class'] = $class;
        $data['geo']   = $geo;
        return $this->http_post($data);
    }
    
    /**
     * Creates a new DNS record for your site. This can be either a CNAME or A record.
     * 
     * @param string $zone - zone (example.com)
     * @param string $type - one of (A|CNAME)
     * @param string $content - The value of the cname or IP address (the destination).
     * @param string $name - The name of the record you wish to create.
     * @param bool $mode - false means CloudFlare is off (grey cloud) for the new zone, while true means a happy orange cloud.
     * @return array - associative array
     */
    public function add_dns_record($zone, $type, $content, $name, $mode) {
        $data['a']            = 'rec_set';
        $data['type']         = ($type == 'A') ? 'A' : 'CNAME';
        $data['content']      = $content;
        $data['name']         = $name;
        $data['service_mode'] = ($mode == true) ? 1 : 0;
        return $this->http_post($data);
    }
    
    /**
     * Update an existing DNS record - Update a DNS record for your site. This needs to be an A record.
     * $ip = The value of the IP address (the destination).
     * $hosts = The name of the record you wish to create.
     */
    public function update_dns_record($ip, $hosts) {
        $data['a']     = "DIUP";
        $data['ip']    = $ip;
        $data['hosts'] = $hosts;
        return $this->http_post($data);
    }
    
    /**
     * Toggle IPv6 support for your site - Toggles ipv6 support for a site.
     */
    public function toggle_ipv6($zone, $mode) {
        $data['a'] = 'ipv46';
        $data['z'] = $zone;
        $data['v'] = ($mode == true) ? 1 : 0;
        return $this->http_post($data);
    }
    
    /**
     * Update the snapshot of your site for CloudFlare's challenge page
     * Tells CloudFlare to take a new image of your site.
     * Note that this call is rate limited to once per zone per day. Also the new image may take up to 1 hour to appear.
     */
    public function update_image($zoneid) {
        $data['a']   = 'zone_grab';
        $data['zid'] = $zoneid;
        return $this->http_post($data);
    }
    
    public function zone_check($zones) {
        if (is_array($zones))
            $zones = implode(",", $zones);
        $data['a']     = 'zone_check';
        $data['zones'] = $zones;
        return $this->http_post($data);
    }
    
    public function del_dns($zone, $name) {
        $data['a']    = 'rec_del';
        $data['zone'] = $zone;
        $data['name'] = $name;
        return $this->http_post($data);
    }
    
    public function update_dns($host, $ip) {
        $data['a']     = 'DIUP';
        $data['ip']    = $ip;
        $data['hosts'] = $host;
        return $this->http_post($data);
    }
    
    public function threat_score($ip) {
        $data['a']  = 'ip_lkup';
        $data['ip'] = $ip;
        return $this->http_post($data);
    }
    
    // HOST SECTION
    
    public function user_create($email, $password, $username = '', $id = '') {
        $data['act']                 = 'user_create';
        $data['cloudflare_email']    = $email;
        $data['cloudflare_pass']     = $password;
        $data['cloudflare_username'] = $username;
        $data['unique_id']           = $id;
        return $this->http_post($data, 'HOST');
    }
    
    public function zone_set($key, $zone, $resolve_to, $subdomains) {
        if (is_array($subdomains))
            $sudomains = implode(",", $subdomains);
        $data['act']        = 'zone_set';
        $data['user_key']   = $key;
        $data['zone_name']  = $zone;
        $data['resolve_to'] = $resolve_to;
        $data['subdomains'] = $subdomains;
        return $this->http_post($data, 'HOST');
    }
    
    public function user_lookup($email, $isID = false) {
        $data['act'] = 'user_lookup';
        if ($isID) {
            $data['unique_id'] = $email;
        } else {
            $data['cloudflare_email'] = $email;
        }
        return $this->http_post($data, 'HOST');
    }
    
    public function user_auth($email, $pass, $id = '') {
        $data['act']              = 'user_auth';
        $data['cloudflare_email'] = $email;
        $data['cloudflare_pass']  = $pass;
        $data['unique_id']        = $id;
        return $this->http_post($data, 'HOST');
    }
    
    public function zone_lookup($zone, $user_key) {
        $data['act']       = 'zone_lookup';
        $data['user_key']  = $user_key;
        $data['zone_name'] = $zone;
        return $this->http_post($data, 'HOST');
    }
    
    public function zone_delete($zone, $user_key) {
        $data['act']       = 'zone_delete';
        $data['user_key']  = $user_key;
        $data['zone_name'] = $zone;
        return $this->http_post($data, 'HOST');
    }
    
    /**
     * HTTP POST a specific task with the supplied data
     */
    private function http_post($data, $type = 'USER') {
        switch ($type) {
            case 'USER':
                $data['u']   = $this->email;
                $data['tkn'] = $this->token_key;
                break;
            case 'HOST':
                $data['host_key'] = $this->host_key;
                break;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_URL, $this->URL[$type]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $http_result = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http_code != 200) {
            return array(
                "error" => $error
            );
        } else {
            return json_decode($http_result, $this->out_array);
        }
    }
}