<?php
namespace Storage\Type;

class Image
{
    protected  $entity = null;


    public function __construct(\Storage\Entity\File $file)
    {
        $this->entity = $file;
    }

    public function getImagine()
    {
        return new \Imagine\Gd\Imagine();
    }

    /* SET PUBLIC FUNCTION */
    public function resize($w, $h, $isSave = true)
    {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->entity->getMap());
        $image->resize(new \Imagine\Image\Box($w, $h));
        $image->save($this->entity->getMap());
        if($isSave)
            $this->entity->save();
        return $this;
    }

    public function cropAndResize($w, $h, $isSave = true)
    {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->entity->getMap());
        $size = $image->getSize();

        $h_i = $size->getHeight();
        $w_i = $size->getWidth();

        if(($h_i / $w_i) * $w >= $h){
            $_w = $w;
            $_h = ($h_i/$w_i)*$w;
        } else {
            $_h = $h;
            $_w = ($w_i/$h_i)*$h;
        }

        $this->resize($_w, $_h);
        $this->crop(($_w - $w) / 2, ($_h - $h) / 2, $w, $h, false);

        if($isSave)
            $this->entity->save();

        return $this;
    }

    public function outResize($w, $h, $isSave = true)
    {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->entity->getMap());
        $size = $image->getSize();

        $h_i = $size->getHeight();
        $w_i = $size->getWidth();

        if(($h_i / $w_i) * $w >= $h){
            $_w = $w;
            $_h = ($h_i/$w_i)*$w;
        } else {
            $_h = $h;
            $_w = ($w_i/$h_i)*$h;
        }
        $this->resize($_w, $_h, false);
        if($isSave)
            $this->entity->save();
        return $this;
    }

    public function crop($x, $y, $w, $h, $isSave = true)
    {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->entity->getMap());
        $image->crop(new \Imagine\Image\Point($x, $y), new \Imagine\Image\Box($w, $h));
        $image->save($this->entity->getMap());
        if($isSave)
            $this->entity->save();
        return $this;
    }

    public function iCrop($x, $y, $w, $h, $_w, $isSave = true)
    {
        $imagine = $this->getImagine();
        $image = $imagine->open($this->entity->getMap());
        $size = $image->getSize();

        $opt = 1 / ($size->getWidth() / $_w);
        $this->crop($x * $opt, $y * $opt, $w * $opt, $h * $opt, false);
        if($isSave)
            $this->entity->save();
        return $this;
    }
}