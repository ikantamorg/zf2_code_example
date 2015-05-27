<?php
namespace User\Provider;

class Latch extends \User\Provider\Model\OpenID
{
	var $openidIdentifier = "http://auth.latch-app.com/OpenIdServer/user.jsp";
	
	/**
	* finish login step 
	*/
	function loginFinish()
	{
		parent::loginFinish();

		$this->user->profile->identifier  = $this->user->profile->email;
		$this->user->profile->emailVerified = $this->user->profile->email;

		// restore the user profile
		\User\Hybrid\Auth::storage()->set( "hauth_session.{$this->providerId}.user", $this->user );
	}	
}
