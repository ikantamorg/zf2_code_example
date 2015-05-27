<?php
namespace Storage\Controller\Admin;

use Admin\Controller\OptionsController;
use Core\Traits\ServiceOption;
use Zend\View\Model\JsonModel;

class Options extends OptionsController
{
    use ServiceOption;

    public function indexAction()
    {
        $storageCollection = $this->getServiceDoctrine()->getRepository('Storage', 'Storage')->findAll();
        $formOptions = new \Storage\Form\Admin\Options();
        $formOptions->setData([
            'default_storage_id' => $this->getServiceOption()->get('storage', 'default_storage_id')->getValue()
        ]);
        return $this->render('admin/storage/options', [
            'form' => $formOptions,
            'storageCollection' => $storageCollection
        ]);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $formOptions = new \Storage\Form\Admin\Options();
        $formOptions->setData($post);
        if($formOptions->isValid()){
            $formData = $formOptions->getData();
            $this->getServiceOption()->get('storage', 'default_storage_id')->setValue($formData['default_storage_id'])->save();
            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $formOptions->getMessages();
        }
        return new JsonModel($data);
    }

}
