<?php
namespace Core\Service;

use Core\Traits\ServiceDoctrine;

class Options extends AbstractService
{
    use ServiceDoctrine;

    public function get($moduleName, $name)
    {
        $option = $this->getServiceDoctrine()->getRepository('Core', 'Option')->findOneBy(['module' => $moduleName, 'name' => $name]);
        if(!$option){
            $option = new \Core\Entity\Option();
            $option->setModule($moduleName);
            $option->setName($name);
            $option->setValue('');
            $option->save();
        }
        return $option;
    }
}