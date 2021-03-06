<?php

namespace PN\Bundle\CMSBundle\Repository;
use PN\Utils\SQL;
use PN\Utils\Validate;

/**
 * FaqRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FaqRepository extends \Doctrine\ORM\EntityRepository
{

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {

        $sortSQL = [
            0 => 'f.question',
            1 => 'f.answer',
            2 => 'f.created',
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


        if (isset( $searchFiltered->string) AND  $searchFiltered->string) {

            if (SQL::validateSS( $searchFiltered->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG( $searchFiltered->string, 'f.id', $where);
                $clause .= SQL::searchSCG( $searchFiltered->string, 'f.question', ' OR ');
                $clause .= SQL::searchSCG( $searchFiltered->string, 'f.answer', ' OR ');
                $clause.= " ) ";
            }
        }

        if ($count) {
            $sqlInner = "SELECT count(f.id) as `count` FROM faq f ";

            $statement = $connection->prepare($sqlInner);
            $statement->execute();
            return $queryResult = $statement->fetchColumn();
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT f.id FROM faq f ";
        $sql .= $clause;

        if (isset($searchFiltered->ordr) AND Validate::not_null($searchFiltered->ordr)) {
            $dir = $searchFiltered->ordr['dir'];
            $columnNumber = $searchFiltered->ordr['column'];
            if (isset($columnNumber) AND array_key_exists($columnNumber, $sortSQL)) {
                $sql .= " ORDER BY " . $sortSQL[$columnNumber] . " $dir";
            }
        } else {
            $sql .= ' ORDER BY f.id DESC';
        }


        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();

        foreach ($filterResult as $key => $r) {
            $result[] = $this->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }
}
