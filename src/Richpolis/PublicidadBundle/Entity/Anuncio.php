<?php

namespace Richpolis\PublicidadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anuncios
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Richpolis\PublicidadBundle\Repository\AnunciosRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Anuncios
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenidoEs;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    public function __construct() {
        $this->isActive = true;
    }
    
    /*
     * Timestable
     */
    
    /**
     ** @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if(!$this->getCreatedAt())
        {
          $this->createdAt = new \DateTime();
        }
    }

}
