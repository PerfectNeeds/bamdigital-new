<?php

namespace PN\Bundle\ServiceBundle\Entity;

trait DateTimeTrait {

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="creator", type="string", length=30)
     */
    protected $creator;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $modified;

    /**
     * @ORM\Column(name="modified_by", type="string", length=30)
     */
    protected $modifiedBy;

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return DeliveryNote
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set creator
     *
     * @param string $creator
     *
     * @return DeliveryNote
     */
    public function setCreator($creator) {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return string
     */
    public function getCreator() {
        return $this->creator;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return DeliveryNote
     */
    public function setModified($modified) {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified() {
        return $this->modified;
    }

    /**
     * Set modifiedBy
     *
     * @param string $modifiedBy
     *
     * @return DeliveryNote
     */
    public function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return string
     */
    public function getModifiedBy() {
        return $this->modifiedBy;
    }

}
