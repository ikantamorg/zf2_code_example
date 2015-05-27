<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Core\Entity\AbstractEntity;

/**
 * @ORM\Table(name="user_avatars")
 * @ORM\Entity(repositoryClass="User\Repository\Avatar")
 * @ORM\HasLifecycleCallbacks()
 */
class Avatar extends AbstractEntity{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Storage\Entity\File", cascade={"remove"})
     * @ORM\JoinColumn(name="big_file_id", referencedColumnName="id")
     **/
    protected $bigFile;

    /**
     * @ORM\OneToOne(targetEntity="Storage\Entity\File", cascade={"remove"})
     * @ORM\JoinColumn(name="small_file_id", referencedColumnName="id")
     **/
    protected $smallFile;

    /**
     * @ORM\OneToOne(targetEntity="Storage\Entity\File", cascade={"remove"})
     * @ORM\JoinColumn(name="middle_file_id", referencedColumnName="id")
     **/
    protected $middleFile;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $is_tmp;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $create_at;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set is_tmp
     *
     * @param integer $isTmp
     * @return Avatar
     */
    public function setIsTmp($isTmp)
    {
        $this->is_tmp = $isTmp;

        return $this;
    }

    /**
     * Get is_tmp
     *
     * @return integer 
     */
    public function getIsTmp()
    {
        return $this->is_tmp;
    }

    /**
     * Set create_at
     *
     * @param integer $createAt
     * @return Avatar
     */
    public function setCreateAt($createAt)
    {
        $this->create_at = $createAt;

        return $this;
    }

    /**
     * Get create_at
     *
     * @return integer 
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * Set bigFile
     *
     * @param \Storage\Entity\File $bigFile
     * @return Avatar
     */
    public function setBigFile(\Storage\Entity\File $bigFile = null)
    {
        $this->bigFile = $bigFile;

        return $this;
    }

    /**
     * Get bigFile
     *
     * @return \Storage\Entity\File 
     */
    public function getBigFile()
    {
        return $this->bigFile;
    }

    /**
     * Set smallFile
     *
     * @param \Storage\Entity\File $smallFile
     * @return Avatar
     */
    public function setSmallFile(\Storage\Entity\File $smallFile = null)
    {
        $this->smallFile = $smallFile;

        return $this;
    }

    /**
     * Get smallFile
     *
     * @return \Storage\Entity\File 
     */
    public function getSmallFile()
    {
        return $this->smallFile;
    }

    /**
     * Set middleFile
     *
     * @param \Storage\Entity\File $middleFile
     * @return Avatar
     */
    public function setMiddleFile(\Storage\Entity\File $middleFile = null)
    {
        $this->middleFile = $middleFile;

        return $this;
    }

    /**
     * Get middleFile
     *
     * @return \Storage\Entity\File 
     */
    public function getMiddleFile()
    {
        return $this->middleFile;
    }
}
