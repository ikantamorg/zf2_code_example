<?php
namespace User\Controller\Auth;

use Core\Controller\Core as CoreController;
use User\Traits\ServiceUser;

class Social extends CoreController
{
    use ServiceUser;
    use \User\Traits\ServiceAuth;

    public function indexAction()
    {
        $auth = $this->getServiceAuth()->getHybrid();
        $adapter = $auth->authenticate($this->params()->fromRoute('provider'));

        $profile = $adapter->getUserProfile();
        $profile = (array)$profile;

        $profile['provider'] = $this->params()->fromRoute('provider');

        $this->getServiceAuth()->socialAuthorize($profile);
        print 'fdf';
        exit;
    }

    public function endpointAction(){
        \User\Hybrid\Endpoint::process();
    }
}