<?php

namespace PN\Bundle\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SeoRepository extends EntityRepository
{


    public function findBySlugAndBaseRouteAndNotId($slug, $seoBaseRouteId, $seoId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('SeoBundle:Seo')->find($r['id']);
        }
        return $result;
    }

    public function findOneBySlugAndBaseRoute($slug, $seoBaseRouteId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND deleted=:deleted Limit 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("deleted", FALSE);

        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result = $this->getEntityManager()->getRepository('SeoBundle:Seo')->find($r['id']);
        }
        return $result;
    }


    public function findByFocusKeywordAndNotId($focusKeyword, $seoId)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM seo WHERE focus_keyword = :focusKeyword AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("focusKeyword", $focusKeyword);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", FALSE);

        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('SeoBundle:Seo')->find($r['id']);
        }
        return $result;
    }

}
