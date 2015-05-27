<?php
namespace Seo\Controller\Admin;

use Admin\Controller\OptionsController;
use Core\Traits\ServiceOption;
use Storage\Traits\ServiceStorage;
use Zend\View\Model\JsonModel;

class Options extends OptionsController
{
    use ServiceOption;
    use ServiceStorage;


    public function indexAction()
    {
        $shareImage = $this->getServiceDoctrine()->getEntity(
            'Storage',
            'File',
            $this->getServiceOption()->get('seo', 'share_image_file_id')->getValue()
        );

        $formOptions = new \Seo\Form\Admin\Options();
        $formOptions->setData([
            'title' => $this->getServiceOption()->get('seo', 'title')->getValue(),
            'description' => $this->getServiceOption()->get('seo', 'description')->getValue(),
            'title_separator' => $this->getServiceOption()->get('seo', 'title_separator')->getValue(),
            'keywords' => $this->getServiceOption()->get('seo', 'keywords')->getValue(),
            'share_image_file_id' => $this->getServiceOption()->get('seo', 'share_image_file_id')->getValue()
        ]);

        $piwikForm = new \Seo\Form\Admin\Piwik();
        $piwikForm->setData([
            'piwik_server_url' => $this->getServiceOption()->get('seo', 'piwik_server_url')->getValue(),
            'piwik_access_token' => $this->getServiceOption()->get('seo', 'piwik_access_token')->getValue(),
            'piwik_site_id' => $this->getServiceOption()->get('seo', 'piwik_site_id')->getValue(),
        ]);

        return $this->render('admin/seo/options', [
            'form' => $formOptions,
            'piwikForm' => $piwikForm,
            'shareImage' => $shareImage
        ]);
    }

    public function ajaxAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $formOptions = new \Seo\Form\Admin\Options();
        $formOptions->setData($post);
        if($formOptions->isValid()){
            $formData = $formOptions->getData();
            $this->getServiceOption()->get('seo', 'title')->setValue($formData['title'])->save();
            $this->getServiceOption()->get('seo', 'description')->setValue($formData['description'])->save();
            $this->getServiceOption()->get('seo', 'title_separator')->setValue($formData['title_separator'])->save();
            $this->getServiceOption()->get('seo', 'keywords')->setValue($formData['keywords'])->save();
            $this->getServiceOption()->get('seo', 'share_image_file_id')->setValue($formData['share_image_file_id'])->save();
            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $formOptions->getMessages();
        }
        return new JsonModel($data);
    }

    public function uploadImageAction()
    {
        $data = ['success' => false];
        $files = $this->getRequest()->getFiles();

        $file = $this->getServiceStorage()->getDefault()->upload($files->file['tmp_name'], $files->file['name']);

        $fileImage = new \Storage\Type\Image($file);
        $fileImage->outResize(1600, 1200);

        $data['href'] = $file->getHref();
        $data['image_id'] = $file->getId();
        $data['success'] = true;

        return new JsonModel($data);
    }

    public function piwikAction()
    {
        $data = ['success' => false];
        $post = $this->params()->fromPost();
        $formOptions = new \Seo\Form\Admin\Piwik();
        $formOptions->setData($post);
        if($formOptions->isValid()){
            $formData = $formOptions->getData();
            $this->getServiceOption()->get('seo', 'piwik_server_url')->setValue($formData['piwik_server_url'])->save();
            $this->getServiceOption()->get('seo', 'piwik_access_token')->setValue($formData['piwik_access_token'])->save();
            $this->getServiceOption()->get('seo', 'piwik_site_id')->setValue($formData['piwik_site_id'])->save();
            $data['success_alert'] = 'Changes saved successfully';
            $data['success'] = true;
        } else {
            $data['errors'] = $formOptions->getMessages();
        }
        return new JsonModel($data);
    }
}
