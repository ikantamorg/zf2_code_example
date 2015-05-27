<?php

namespace Core\Controller;

class Widget extends Core
{
    public function indexAction()
    {

        $module = static::getFromWidget($this->params()->fromRoute('widget_module'));
        $widgetName = static::getFromWidget($this->params()->fromRoute('widget_name'));
        $action = $this->params()->fromRoute('widget_action');

        return $this->forward()->dispatch('Widget\\' . $module . '\\' . $widgetName, ['action' => $action]);
    }

    public static function getFromWidget($line)
    {
        $method  = str_replace(array('.', '-', '_'), ' ', $line);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        return $method;
    }
}
