<?php

namespace PN\Bundle\MediaBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;
use Symfony\Component\HttpFoundation\Session\Session;

class VarsExtension extends Twig_Extension {

    private $container;
    private $em;
    private $conn;

    public function __construct(\Doctrine\ORM\EntityManager $em, ContainerInterface $container) {
        $this->em = $em;
        $this->conn = $em->getConnection();
        $this->container = $container;
    }


    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('fileSizeConvert', array($this, 'fileSizeConvert')),
        );
    }

    public static function fileSizeConvert($bytes)
    {

        $result="";

        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result,2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

}
