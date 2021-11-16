<?php

namespace PN\Bundle\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ShippingAddressRepository extends EntityRepository {

    public function findAll() {
        return $this->getEntityManager()->getRepository('UserBundle:ShippingAddress')->findBy(array('deleted' => FALSE));
    }

}
