<?php

namespace Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Traits\ServiceTraits;
use Core\Traits\ServiceDoctrine;

class AbstractEntity {

    use ServiceTraits;
    use ServiceDoctrine;


    public function save()
    {
        $em = $this->getServiceDoctrine()->getEm();
        $em->persist($this);
        //if($this->getId()){
        //    $em->clear(); // Detaches all objects from Doctrine!
        //$em->flush(); // Executes all deletions.
        //}
        $em->flush();

        return $this;
    }

    public function delete()
    {
        $em = $this->getServiceDoctrine()->getEm();
        $em->remove($this);
        $em->flush();
    }

    function timeAgo($ptime)
    {
        $etime = time() - $ptime;

        if ($etime < 1)
        {
            return '0 seconds';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
            30 * 24 * 60 * 60  =>  'month',
            24 * 60 * 60  =>  'day',
            60 * 60  =>  'hour',
            60  =>  'minute',
            1  =>  'second'
        );
        $a_plural = array( 'year'   => 'years',
            'month'  => 'months',
            'day'    => 'days',
            'hour'   => 'hours',
            'minute' => 'minutes',
            'second' => 'seconds'
        );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }
}
