<?php
namespace User\Controller\Admin;

use Admin\Controller\AbstractController as CoreController;
use Zend\View\Model\JsonModel;
use User\Traits\ServiceUser;

class AuthSocial extends CoreController
{
    use ServiceUser;

    public function indexAction()
    {

        $list = $this->getServiceLocator()->get('config')['user']['social_auth']['list'];
        foreach($list as $index=>&$provider){
            if(!$provider['enabled']) continue;
            $providerKey = 'social_auth_' . $index . '_';
            $_keys = [];
            foreach($provider['keys'] as $_key){
                $key = $providerKey . $_key;
                $_keys[$_key] = $this->getServiceOption()->get('user', $key)->getValue();
            }
            $provider['keys'] = $_keys;
        }
        return $this->render('admin/user/social-auth', ['listSocials' => $list]);
    }

    public function ajaxAction()
    {
        $postParams = $this->params()->fromPost();
        foreach($postParams['providers'] as $provider){
            $providerKey = 'social_auth_' . $provider['provider'] . '_';
            foreach($provider['keys'] as $index=>$key){
                $_key = $providerKey . $index;
                $this->getServiceOption()->get('user', $_key)->setValue($key)->save();
            }
        }
        return new JsonModel(['success' => true]);
    }
}