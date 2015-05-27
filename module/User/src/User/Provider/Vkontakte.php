<?php
namespace User\Provider;

class Vkontakte extends \User\Provider\Model\OAuth2
{
    public $scope = "email";

    function initialize()
    {
        parent::initialize();

        $this->api->authorize_url  = "http://api.vk.com/oauth/authorize";
        $this->api->token_url      = "https://api.vk.com/oauth/token";
    }

    function loginFinish()
    {
        $error = (array_key_exists('error',$_REQUEST))?$_REQUEST['error']:"";

        if ( $error ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an error: $error", 5 );
        }

        $code = (array_key_exists('code',$_REQUEST))?$_REQUEST['code']:"";

        try{
            $response = $this->api->authenticate( $code );
        } catch( \Exception $e ) {
            throw new \Exception( "User profile request failed! {$this->providerId} returned an error: $e", 6 );
        }

        if ( !property_exists($response,'user_id') || ! $this->api->access_token ){
            throw new \Exception( "Authentication failed! {$this->providerId} returned an invalid access token.", 5 );
        }

        $this->token( "access_token" , $this->api->access_token  );
        $this->token( "refresh_token", $this->api->refresh_token );
        $this->token( "expires_in"   , $this->api->access_token_expires_in );
        $this->token( "expires_at"   , $this->api->access_token_expires_at );

        \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.user_id", $response->user_id );
        \User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.user_email", $response->email );

        $this->setUserConnected();
    }

    function getUserProfile()
    {
        $this->refreshToken();

        $params['uid'] = \User\Hybrid\Auth::storage()->get( "hauth_session.{$this->providerId}.user_id" );
        $params['fields'] = 'first_name,last_name,nickname,screen_name,sex,bdate,timezone,photo_rec,photo_big,home_town';
        $response = $this->api->api( "https://api.vk.com/method/getProfiles" , 'GET', $params);

        if (!isset( $response->response[0] ) || !isset( $response->response[0]->uid ) || isset( $response->error ) ){
            throw new \Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
        }

        $response = $response->response[0];
        $this->user->profile->identifier    = (property_exists($response,'uid'))?$response->uid:"";
        $this->user->profile->firstName     = (property_exists($response,'first_name'))?$response->first_name:"";
        $this->user->profile->lastName      = (property_exists($response,'last_name'))?$response->last_name:"";
        $this->user->profile->displayName   = (property_exists($response,'screen_name'))?$response->screen_name:"";
        $this->user->profile->photoURL      = (property_exists($response,'photo_big'))?$response->photo_big:"";
        $this->user->profile->profileURL    = (property_exists($response,'screen_name'))?"http://vk.com/" . $response->screen_name:"";
        $this->user->profile->email         = \User\Hybrid\Auth::storage()->get( "hauth_session.{$this->providerId}.user_email" );
        $this->user->profile->home_town     = (property_exists($response,'home_town'))?$response->home_town:"";

        if(property_exists($response,'sex')){
            switch ($response->sex)
            {
                case 1: $this->user->profile->gender = 'female'; break;
                case 2: $this->user->profile->gender = 'male'; break;
                default: $this->user->profile->gender = ''; break;
            }
        }

        if( property_exists($response,'bdate') ){
            $birthday = explode('.', $response->bdate);

            switch (count($birthday)) {
                case 3:
                    $this->user->profile->birthDay   = (int) $birthday[0];
                    $this->user->profile->birthMonth = (int) $birthday[1];
                    $this->user->profile->birthYear  = (int) $birthday[2];
                    break;

                case 2:
                    $this->user->profile->birthDay   = (int) $birthday[0];
                    $this->user->profile->birthMonth = (int) $birthday[1];
                    break;
            }
        }

        return $this->user->profile;
    }

    function getUserContacts()
    {
        $params=array(
            'fields' => 'nickname, domain, sex, bdate, city, country, timezone, photo_200_orig'
        );

        $response = $this->api->api('https://api.vk.com/method/friends.get','GET',$params);

        if(!$response || !count($response->response)){
            return array();
        }

        $contacts = array();
        foreach( $response->response as $item ){
            $uc = new \User\Hybrid\UserContact();
            $uc->identifier  = $item->uid;
            $uc->displayName = $item->first_name.' '.$item->last_name;
            $uc->profileURL  = 'http://vk.com/'.$item->domain;
            $uc->photoURL    = $item->photo_200_orig;
            $contacts[] = $uc;
        }

        return $contacts;
    }
}
