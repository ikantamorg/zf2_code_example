<?php
namespace User\Provider;

class Foursquare extends \User\Provider\Model\OAuth2
{
    private static $apiVersion = array( "v" => "20120610" );
    private static $defPhotoSize = "100x100";

    function initialize()
    {
        parent::initialize();

        $this->api->api_base_url  = "https://api.foursquare.com/v2/";
        $this->api->authorize_url = "https://foursquare.com/oauth2/authenticate";
        $this->api->token_url     = "https://foursquare.com/oauth2/access_token";

        $this->api->sign_token_name = "oauth_token";
    }

    function getUserProfile()
    {
        $data = $this->api->api( "users/self", "GET", \User\Provider\Foursquare::$apiVersion );

        if ( ! isset( $data->response->user->id ) ){
            throw new \Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
        }

        $data = $data->response->user;

        $this->user->profile->identifier    = $data->id;
        $this->user->profile->firstName     = $data->firstName;
        $this->user->profile->lastName      = $data->lastName;
        $this->user->profile->displayName   = $this->buildDisplayName( $this->user->profile->firstName, $this->user->profile->lastName );
        $this->user->profile->photoURL      = $this->buildPhotoURL( $data->photo->prefix, $data->photo->suffix );
        $this->user->profile->profileURL    = "https://www.foursquare.com/user/" . $data->id;
        $this->user->profile->gender        = $data->gender;
        $this->user->profile->city          = $data->homeCity;
        $this->user->profile->email         = $data->contact->email;
        $this->user->profile->emailVerified = $data->contact->email;

        return $this->user->profile;
    }

    function getUserContacts()
    {
        $this->refreshToken();
        $contacts = array();
        try {
            $response = $this->api->api( "users/self/friends", "GET", \User\Provider\Foursquare::$apiVersion );
        }
        catch( \User\Third\LinkedIn\LinkedInException $e ){
            throw new \Exception( "User contacts request failed! {$this->providerId} returned an error: $e" );
        }

        if( isset( $response ) && $response->meta->code == 200 ) {
            foreach( $response->response->friends->items as $contact ) {
                $uc = new \User\Hybrid\UserContact();
                $uc->identifier		= $contact->id;
                $uc->photoURL			= $this->buildPhotoURL( $contact->photo->prefix, $contact->photo->suffix );
                $uc->displayName	= $this->buildDisplayName( (isset($contact->firstName)?($contact->firstName):("")), (isset($contact->lastName)?($contact->lastName):("")) );
                $uc->email				= (isset($contact->contact->email)?($contact->contact->email):(""));
                $contacts[] = $uc;
            }
        }
        return $contacts;
    }

    private function buildDisplayName( $firstName, $lastName ) {
        return trim( $firstName . " " . $lastName );
    }

    private function buildPhotoURL( $prefix, $suffix ) {
        if ( isset( $prefix ) && isset( $suffix ) ) {
            return $prefix . ((isset($this->config["params"]["photo_size"]))?($this->config["params"]["photo_size"]):(\User\Provider\Foursquare::$defPhotoSize)) . $suffix;
        }
        return ("");
    }
}
