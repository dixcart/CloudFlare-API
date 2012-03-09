#CloudFlare API PHP Binding

A basic PHP binding for the [CloudFlare](http://www.cloudflare.com) Client and Host APIs.  Depending on the number of parameters passed, you can use either the host functions or the client functions.

Returns a PHP object in all cases.

##Client API

###Usage

    $cf = new cloudflare_api("me@example.com", "799df833d7a42adf3b8e2fd113c7260b955b8e95ac42c");
    $response = $cf->stats("example.com", 20);
    
##Host API

###Usage

    $cf = new cloudflare_api("8afbe6dea02407989af4dd4c97bb6e25");
    $response = $cf->user_create("newuser@example.com", "newpassword", "", "someuniqueid");