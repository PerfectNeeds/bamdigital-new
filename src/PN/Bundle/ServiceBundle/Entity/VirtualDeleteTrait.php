<?php

namespace PN\Bundle\ServiceBundle\Entity;

trait VirtualDeleteTrait {

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted = null;

    /**
     * @ORM\Column(name="deleted_by", type="string", length=30, nullable=true)
     */
    protected $deletedBy = NULL;

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Agent
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set deletedBy
     *
     * @param string $deletedBy
     * @return Agent
     */
    public function setDeletedBy($deletedBy) {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    /**
     * Get deletedBy
     *
     * @return string 
     */
    public function getDeletedBy() {
        return $this->deletedBy;
    }

}
