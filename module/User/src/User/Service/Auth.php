<?php
namespace User\Service;

use Core\Service\AbstractService;
use Core\Traits\ServiceSession;
use Core\Traits\ServiceDoctrine;

class Auth extends AbstractService
{
    use ServiceSession;
    use ServiceDoctrine;
    use \User\Traits\ServiceUser;
    use \Core\Traits\ServiceOption;
    use \Core\Traits\ServiceBrowser;
    use \Sender\Traits\ServiceSender;


    protected $sender;
    protected $sessionDb;

    public function authorize(\User\Entity\User $user)
    {
        $this->getServiceSession()->setValue('user_id', $user->getId());
    }

    public function isLogin()
    {
        $user = $this->getLoginUser();
        return $user ? true : false;
    }

    public function getLoginUser()
    {
        $user = null;
        $userId = $this->getServiceSession()->getValue('user_id');
        if($userId){
            $user = $this->getServiceDoctrine()->getRepository('User', 'User')->findOneBy(['id' => $userId]);
        }

        if(!$this->sessionDb && $user) {
            $identification = $this->getServiceSession()->get()->getManager()->getId();
            $session = $this->getServiceDoctrine()->getRepository('User', 'Session')->findOneBy([
                'identification' => $identification,
                'user' => $user
            ]);
            if(!$session){

                $loginSessions = $this->getServiceDoctrine()->getRepository('User', 'Session')->findBy([
                    'user' => $user,
                    'is_login' => 1
                ]);

                $browser = $this->getServiceBrowser();
                $browser->setUserAgent($_SERVER['HTTP_USER_AGENT']);

                $session = new \User\Entity\Session();
                $session->setIdentification($identification);
                $session->setCreateAt(time());
                $session->setAolVersion($browser->getAolVersion());
                $session->setBrowser($browser->getBrowser());
                $session->setPlatform($browser->getPlatform());
                $session->setVersion($browser->getVersion());
                $session->setIp($browser->getIp());
                $session->setIsLogin(1);
                $session->setUser($user);

                if(
                    count($loginSessions) &&
                    $user->getEmail() &&
                    $user->getActiveNotification()
                ){
                    $this->getSender()->sendMessage([
                        'tmp_name' => 'email/user/new_device',
                        'recipients' => [[
                            'address' => $user->getEmail(),
                            'name' => 'Test'
                        ]],
                        'user' => $user,
                        'session' => $session
                    ]);
                }
            }

            $session->setLastActivity(time());
            $session->save();

            $this->sessionDb = $session;
        }

        return $user;
    }

    public function logout()
    {
        $loginUser = $this->getLoginUser();
        if($loginUser){
            $this->sessionDb->setIsLogin(0);
            $this->sessionDb->save();
        }
        $this->getServiceSession()->setValue('user_id', '');
    }

    public function getHybrid()
    {
        $serverUrlHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('serverUrl');
        $UrlHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Url');
        $baseUrl = $serverUrlHelper->getScheme() . '://' . $serverUrlHelper->getHost() . $UrlHelper('social/endpoint');

        $configs = array(
            "base_url" => $baseUrl,
            "providers" => [],
            "debug_mode" => false,
            "debug_file" => "",
        );

        $list = $this->getServiceLocator()->get('config')['user']['social_auth']['list'];
        foreach($list as $index=>&$provider){
            if(!$provider['enabled']) continue;
            $configs['providers'][$index] = ["enabled" => true, 'keys' => []];
            $providerKey = 'social_auth_' . $index . '_';
            foreach($provider['keys'] as $_key){
                $key = $providerKey . $_key;
                $configs['providers'][$index]['keys'][$_key] = $this->getServiceOption()->get('user', $key)->getValue();
            }
        }

        $auth = new \User\Hybrid\Auth($configs);
        return $auth;
    }

    public function socialAuthorize($data)
    {
        $social = $this->getServiceDoctrine()->getRepository('User', 'Social')->findOneBy([
            'provider' => $data['provider'],
            'identifier' => $data['identifier']
        ]);

        if(!$social){
            $user = $this->getServiceDoctrine()->getRepository('User', 'User')->findOneBy(['email' => $data['email']]);

            if(!$user){
                $user = $this->getServiceUser()->createUser([
                    'email' => $data['email'],
                    'password' => md5(time()),
                    'displayname' => $data['displayName'],
                    'last_name' => $data['lastName'],
                    'first_name' => $data['firstName'],
                    'birth_date' => date('Y-m-d', strtotime(str_replace('/', '-', $data['birthDay'] . '/' . $data['birthMonth'] . '/' . $data['birthYear'])))
                ]);
            }

            $social = new \User\Entity\Social();
            $social->setIdentifier($data['identifier']);
            $social->setProvider($data['provider']);
            $social->setCreateAt(time());
            $social->setUser($user);
            $social->save();
        }

        $this->authorize($social->getUser());
    }

    public function getSender()
    {
        if(!$this->sender){
            $this->sender = $this->getServiceSender()->get();
        }
        return $this->sender;
    }
}