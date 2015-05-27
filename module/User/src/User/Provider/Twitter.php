<?php
namespace User\Provider;

class Twitter extends \User\Provider\Model\OAuth1
{
    function initialize()
    {
        parent::initialize();

        $this->api->api_base_url      = "https://api.twitter.com/1.1/";
        $this->api->authorize_url     = "https://api.twitter.com/oauth/authenticate";
        $this->api->request_token_url = "https://api.twitter.com/oauth/request_token";
        $this->api->access_token_url  = "https://api.twitter.com/oauth/access_token";

        if ( isset( $this->config['api_version'] ) && $this->config['api_version'] ){
            $this->api->api_base_url  = "https://api.twitter.com/{$this->config['api_version']}/";
        }

        if ( isset( $this->config['authorize'] ) && $this->config['authorize'] ){
            $this->api->authorize_url = "https://api.twitter.com/oauth/authorize";
        }

        $this->api->curl_auth_header  = false;
    }

    function loginBegin()
    {
        if (isset($_REQUEST['reverse_auth']) && ($_REQUEST['reverse_auth'] == 'yes')){
            $stage1 = $this->api->signedRequest( $this->api->request_token_url, 'POST', array( 'x_auth_mode' => 'reverse_auth' ) );
            if ( $this->api->http_code != 200 ){
                throw new \Exception( "Authentication failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ), 5 );
            }
            $responseObj = array( 'x_reverse_auth_parameters' => $stage1, 'x_reverse_auth_target' => $this->config["keys"]["key"] );
            $response = json_encode($responseObj);
            header( "Content-Type: application/json", true, 200 ) ;
            echo $response;
            die();
        }
        $tokens = $this->api->requestToken( $this->endpoint );
        $this->request_tokens_raw = $tokens;

        if ( $this->api->http_code != 200 ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ), 5 );
        }

        if ( ! isset( $tokens["oauth_token"] ) ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an invalid oauth token.", 5 );
        }

        $this->token( "request_token"       , $tokens["oauth_token"] );
        $this->token( "request_token_secret", $tokens["oauth_token_secret"] );

        if ( ( isset( $this->config['force_login'] ) && $this->config['force_login'] ) || ( isset( $this->config[ 'force' ] ) && $this->config[ 'force' ] === true ) ){
            \User\Hybrid\Auth::redirect( $this->api->authorizeUrl( $tokens, array( 'force_login' => true ) ) );
        }

        \User\Hybrid\Auth::redirect( $this->api->authorizeUrl( $tokens ) );
    }

    function loginFinish()
    {
        if(isset($_REQUEST['oauth_token_secret'])){
            $tokens = $_REQUEST;
            $this->access_tokens_raw = $tokens;

            if ( ! isset( $tokens["oauth_token"] ) ){
                throw new \Exception( "Authentication failed! {$this->providerId} returned an invalid access token.", 5 );
            }

            $this->deleteToken( "request_token"        );
            $this->deleteToken( "request_token_secret" );
            $this->token( "access_token"        , $tokens['oauth_token'] );
            $this->token( "access_token_secret" , $tokens['oauth_token_secret'] );

            $this->setUserConnected();
            return;
        }
        parent::loginFinish();
    }

    function getUserProfile()
    {
        $response = $this->api->get( 'account/verify_credentials.json' );

        if ( $this->api->http_code != 200 ){
            throw new \Exception( "User profile request failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ), 6 );
        }

        if ( ! is_object( $response ) || ! isset( $response->id ) ){
            throw new \Exception( "User profile request failed! {$this->providerId} api returned an invalid response.", 6 );
        }

        $this->user->profile->identifier  = (property_exists($response,'id'))?$response->id:"";
        $this->user->profile->displayName = (property_exists($response,'screen_name'))?$response->screen_name:"";
        $this->user->profile->description = (property_exists($response,'description'))?$response->description:"";
        $this->user->profile->firstName   = (property_exists($response,'name'))?$response->name:"";
        $this->user->profile->photoURL    = (property_exists($response,'profile_image_url'))?(str_replace('_normal', '', $response->profile_image_url)):"";
        $this->user->profile->profileURL  = (property_exists($response,'screen_name'))?("http://twitter.com/".$response->screen_name):"";
        $this->user->profile->webSiteURL  = (property_exists($response,'url'))?$response->url:"";
        $this->user->profile->region      = (property_exists($response,'location'))?$response->location:"";

        return $this->user->profile;
    }

    function getUserContacts()
    {
        $parameters = array( 'cursor' => '-1' );
        $response  = $this->api->get( 'friends/ids.json', $parameters );

        if ( $this->api->http_code != 200 ){
            throw new \Exception( "User contacts request failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ) );
        }

        if( ! $response || ! count( $response->ids ) ){
            return ARRAY();
        }

        $contactsids = array_chunk ( $response->ids, 75 );

        $contacts    = ARRAY();

        foreach( $contactsids as $chunk ){
            $parameters = array( 'user_id' => implode( ",", $chunk ) );
            $response   = $this->api->get( 'users/lookup.json', $parameters );

            if ( $this->api->http_code != 200 ){
                throw new \Exception( "User contacts request failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ) );
            }

            if( $response && count( $response ) ){
                foreach( $response as $item ){
                    $uc = new \User\Hybrid\UserContact();

                    $uc->identifier   = (property_exists($item,'id'))?$item->id:"";
                    $uc->displayName  = (property_exists($item,'name'))?$item->name:"";
                    $uc->profileURL   = (property_exists($item,'screen_name'))?("http://twitter.com/".$item->screen_name):"";
                    $uc->photoURL     = (property_exists($item,'profile_image_url'))?$item->profile_image_url:"";
                    $uc->description  = (property_exists($item,'description'))?$item->description:"";

                    $contacts[] = $uc;
                }
            }
        }

        return $contacts;
    }

    function setUserStatus( $status )
    {
        if( is_array( $status ) && isset( $status[ 'message' ] ) && isset( $status[ 'picture' ] ) ){
            $response = $this->api->post( 'statuses/update_with_media.json', array( 'status' => $status[ 'message' ], 'media[]' => file_get_contents( $status[ 'picture' ] ) ), null, null, true );
        }else{
            $response = $this->api->post( 'statuses/update.json', array( 'status' => $status ) );
        }

        if ( $this->api->http_code != 200 ){
            throw new \Exception( "Update user status failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ) );
        }

        return $response;
    }

    function getUserStatus( $tweetid )
    {
        $info = $this->api->get( 'statuses/show.json?id=' . $tweetid . '&include_entities=true' );

        if ( $this->api->http_code != 200 || !isset( $info->id ) ){
            throw new \Exception( "Cannot retrieve user status! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ) );
        }

        return $info;
    }

    function getUserActivity( $stream )
    {
        if( $stream == "me" ){
            $response  = $this->api->get( 'statuses/user_timeline.json' );
        } else {
            $response  = $this->api->get( 'statuses/home_timeline.json' );
        }

        if ( $this->api->http_code != 200 ){
            throw new \Exception( "User activity stream request failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ) );
        }

        if( ! $response ){
            return ARRAY();
        }

        $activities = ARRAY();

        foreach( $response as $item ){
            $ua = new \User\Hybrid\UserActivity();

            $ua->id                 = (property_exists($item,'id'))?$item->id:"";
            $ua->date               = (property_exists($item,'created_at'))?strtotime($item->created_at):"";
            $ua->text               = (property_exists($item,'text'))?$item->text:"";

            $ua->user->identifier   = (property_exists($item->user,'id'))?$item->user->id:"";
            $ua->user->displayName  = (property_exists($item->user,'name'))?$item->user->name:"";
            $ua->user->profileURL   = (property_exists($item->user,'screen_name'))?("http://twitter.com/".$item->user->screen_name):"";
            $ua->user->photoURL     = (property_exists($item->user,'profile_image_url'))?$item->user->profile_image_url:"";

            $activities[] = $ua;
        }

        return $activities;
    }
}
