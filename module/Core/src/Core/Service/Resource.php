<?php
namespace Core\Service;

use Core\Traits\ServiceDoctrine;

class Resource extends AbstractService
{
    use ServiceDoctrine;

    public function get($object)
    {
        $class = get_class($object);
        $resource = $this->getServiceDoctrine()->getRepository('Core', 'Resource')->findOneBy(['class' => $class]);

        if(!$resource){
            $resource = new \Core\Entity\Resource();
            $resource->setClass($class);
            $resource->save();
        }
        return $resource;
    }

    public function getObject($_resource)
    {
        $resource = $this->get($_resource);
        $object = $this->getServiceDoctrine()->getRepository('Core', 'ResourceObject')->findOneBy([
            'resource' => $resource,
            'object_id' => $_resource->getId()
        ]);
        if(!$object){
            $object = new \Core\Entity\ResourceObject();
            $object->setObjectId($_resource->getId());
            $object->setResource($resource);
            $object->save();
        }
        return $object;
    }

    public function getResourceTagByTag($tag)
    {
        $resourceTag = $this->getServiceDoctrine()->getRepository('Core', 'ResourceTag')->findOneBy(['tag' => $tag]);
        if(!$resourceTag){
            $resourceTag = new \Core\Entity\ResourceTag();
            $resourceTag->setTag($tag);
            $resourceTag->save();
        }
        return $resourceTag;
    }

    public function viewPlus($resource)
    {
        $object = $this->getObject($resource);

        $view = new \Core\Entity\ResourceView();
        $view->setCreatedAt(time());
        $view->setObject($object);
        $view->save();
        return $view;
    }

    public function viewsCount($resource)
    {
        $object = $this->getObject($resource);
        return $object->getViewsCount();
    }
}