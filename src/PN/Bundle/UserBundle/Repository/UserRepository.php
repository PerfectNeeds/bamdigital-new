<?php

namespace PN\Bundle\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PN\Utils\SQL;
use PN\Utils\Validate;

class UserRepository extends EntityRepository {

    public function findByApiKey($apiKey) {
        return $this->findOneBy(array('apiKey' => $apiKey));
    }

    /**
     * @param string $role
     *
     * @return array
     */
    public function findByRole($role, $deleted = FALSE) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.deleted = :deleted')
            ->setParameter('roles', '%"' . $role . '"%')
            ->setParameter('deleted', $deleted);

        return $qb->getQuery()->getResult();
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {


        $sortSQL = [
            0 => 'u.fullname',
            1 => 'u.email',
            2 => 'u.last_login',
            3 => 'u.state',
            4 => 'u.roles',
            5 => 'u.created',
        ];
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';

        $searchFiltered = new \stdClass();
        foreach ($search as $key => $value) {
            if (Validate::not_null($value) AND ! is_array($value)) {
                $searchFiltered->{$key} = substr($connection->quote($value), 1, -1);
            }
            else{
                $searchFiltered->{$key} = $value;
            }
        }

        if (isset($searchFiltered->deleted) AND in_array($searchFiltered->deleted, array(0, 1))) {
            $where = ($where) ? " AND " : " WHERE ";
            if ($searchFiltered->deleted == 1) {
                $clause .= $where . " u.deleted IS NOT NULL ";
            } else {
                $clause .= $where . " u.deleted IS NULL ";
            }
        }
        if (isset( $searchFiltered->role) AND Validate::not_null( $searchFiltered->role)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::searchSCG( $searchFiltered->role, 'u.roles', $where);
        }

        if (isset( $searchFiltered->string) AND  $searchFiltered->string) {

            if (SQL::validateSS( $searchFiltered->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG( $searchFiltered->string, 'u.id', $where);
                $clause .= SQL::searchSCG( $searchFiltered->string, 'u.fullname', ' OR ');
                $clause .= SQL::searchSCG( $searchFiltered->string, 'u.email', ' OR ');
                $clause.= " ) ";
            }
        }

        if ($count) {
            $sqlInner = "SELECT count(u.id) as `count` FROM usr u ";
            $sqlInner .= $clause;

            $statement = $connection->prepare($sqlInner);
            $statement->execute();
            return $queryResult = $statement->fetchColumn();
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT u.id FROM usr u ";
        $sql .= $clause;

        if (isset($searchFiltered->ordr) AND Validate::not_null($searchFiltered->ordr)) {
            $dir = $searchFiltered->ordr['dir'];
            $columnNumber = $searchFiltered->ordr['column'];
            if (isset($columnNumber) AND array_key_exists($columnNumber, $sortSQL)) {
                $sql .= " ORDER BY " . $sortSQL[$columnNumber] . " $dir";
            }
        } else {
            $sql .= ' ORDER BY u.id DESC';
        }


        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();

        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('UserBundle:User')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
