<?php

namespace PN\Bundle\SeoBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Extension;

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
            new \Twig_SimpleFilter('backlinks', array($this, 'backlinks')),
        );
    }

    public function backlinks($str) {
        if(strlen($str) == 0){
            return $str;
        }
        
        $backLinks = $this->em->getRepository('SeoBundle:BackLink')->findAllByJSON();

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($doc);
        $text_nodes = $xpath->evaluate('//text()');
        $searchArr = array();
        $replaceArr = array();
        foreach ($backLinks as $backLink) {
            $searchArr[] = $backLink['word'];
            $replaceArr[] = '<a href="' . $backLink['link'] . '" target="_blank" rel="dofollow">' . $backLink['word'] . '</a>';
        }

        foreach ($text_nodes as $text_node) {
            $text_node->nodeValue = str_replace($searchArr, $replaceArr, $text_node->nodeValue);
        }
        return html_entity_decode($doc->saveHTML());
    }

}
