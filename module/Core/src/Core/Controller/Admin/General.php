<?php
namespace Core\Controller\Admin;

use Admin\Controller\OptionsController;
use Core\Traits\ServiceOption;
use Storage\Traits\ServiceStorage;
use Zend\View\Model\JsonModel;

class General extends OptionsController
{
    use ServiceOption;
    use ServiceStorage;
    use \Core\Traits\ServiceGit;

    public function indexAction()
    {
        $form = new \Core\Form\Admin\General();
        $form->setData([
            'default_timezone' => $this->getServiceOption()->get('core', 'default_timezone')->getValue()
        ]);

        $formGithub = new \Core\Form\Admin\Github();
        $formGithub->setData([
            'github_executable_path' => $this->getServiceOption()->get('core', 'github_executable_path')->getValue(),
            'github_username' => $this->getServiceOption()->get('core', 'github_username')->getValue(),
            'github_password' => $this->getServiceOption()->get('core', 'github_password')->getValue(),
            'github_remote' => $this->getServiceOption()->get('core', 'github_remote')->getValue(),
            'github_branch' => $this->getServiceOption()->get('core', 'github_branch')->getValue()
        ]);

        return $this->render('admin/core/general', [
            'form' => $form,
            'formGithub' => $formGithub
        ]);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $form = new \Core\Form\Admin\General();
        $form->setData($post);
        if($form->isValid()){
            $formData = $form->getData();
            $this->getServiceOption()->get('core', 'default_timezone')->setValue($formData['default_timezone'])->save();
            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $form->getMessages();
        }
        return new JsonModel($data);
    }

    public function githubAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $form = new \Core\Form\Admin\Github();
        $form->setData($post);
        if($form->isValid()){
            $formData = $form->getData();
            $this->getServiceOption()->get('core', 'github_executable_path')->setValue($formData['github_executable_path'])->save();
            $this->getServiceOption()->get('core', 'github_username')->setValue($formData['github_username'])->save();
            $this->getServiceOption()->get('core', 'github_password')->setValue($formData['github_password'])->save();
            $this->getServiceOption()->get('core', 'github_remote')->setValue($formData['github_remote'])->save();
            $this->getServiceOption()->get('core', 'github_branch')->setValue($formData['github_branch'])->save();

            $this->getServiceGit()->updateAuthFile();

            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $form->getMessages();
        }
        return new JsonModel($data);
    }

    public function githubUpdateAction()
    {
        $return = $this->getServiceGit()->pull();

        $data = [
            'text' => $return,
            'success' => true
        ];

        return new JsonModel($data);
    }

    public function githubStatusAction()
    {
        $data = [
            'text' => $this->getServiceGit()->status(),
            'success' => true
        ];

        return new JsonModel($data);
    }
}