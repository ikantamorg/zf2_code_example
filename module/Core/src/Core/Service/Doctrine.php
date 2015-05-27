<?php
namespace Core\Service;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as Hydrator;
use Zend\Form\Annotation\AnnotationBuilder;

class Doctrine extends AbstractService{

    /**
     * Get Doctrine Entity Manger
     *
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * Get Doctrine Entity by id
     *
     * @param string $repository
     * @param int $id 
     * 
     * @return object
     */
    public function getEntity($module, $model, $id = null)
    {
        return $this->getEm()->find($module . "\\Entity\\" . $model, intval($id));
    }

    /**
     * Get Doctrine repository
     *
     * @param string $repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($module, $model)
    {
        return $this->getEm()->getRepository($module . '\Entity\\' . $model);
    }

    /**
     * Shortcut to create a form from annotations
     * Attach Doctrine hydrator
     *
     * @param object $entity
     * @param bool $bind - if entity should be binded
     *
     * @return use Zend\Form\Form;
     */
    public function createForm($entity, $bind = true) {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($entity);
        $form->setHydrator(new Hydrator($this->getEm(), get_class($entity)));
        if ($bind === true) {
            $form->bind($entity);
        }
        return $form;
    }
}