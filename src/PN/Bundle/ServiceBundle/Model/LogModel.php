<?php

namespace PN\Bundle\ServiceBundle\Model;

class LogModel {

    private $container;
    private $oldEntity, $newEntity;
    private $excludeMethods = array(
        "getId",
        "getLocate",
        "getTranslation",
        "getAttr",
        "getLocale",
        "getObject",
        "getSeo",
        "getPost",
        "getLog",
        "getStras",
    );

    public function __construct($newEntity, $oldEntity = NULL) {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $this->container = $kernel->getContainer();
        $this->oldEntity = $oldEntity;
        $this->newEntity = $newEntity;
    }

    public function createLogNod() {
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
        $str = $username . ' added a new entry on ' . date('D d/m/Y h:i A');
        $this->addToEntity($str);
    }

    public function updateLogNod() {
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();

        $oldMethods = get_class_methods($this->oldEntity);

        $getterMethods = array();
        $changedMethods = array();

        foreach ($oldMethods as $method_name) {
            if (strpos($method_name, 'get') !== false AND ! in_array($method_name, $this->excludeMethods)) {
                array_push($getterMethods, $method_name);
            }
        }

        foreach ($getterMethods as $method) {
            $item = $this->oldEntity->$method();
            if ((!is_array($item) ) && ( (!is_object($item) && settype($item, 'string') !== false ) || ( is_object($item) && method_exists($item, '__toString') ) )) {
                if ($this->oldEntity->$method() != $this->newEntity->$method()) {
                    array_push($changedMethods, $method);
                }
            }
        }


        if (method_exists($this->newEntity, 'getPost') AND $this->newEntity->getPost() != NULL) {
            if (is_array($this->newEntity->getPost()->getContent())) {
                $oldContent = $this->oldEntity->getPost()->getContent();
                foreach ($this->newEntity->getPost()->getContent() as $postKey => $postValue) {
                    if (isset($oldContent[$postKey])) {
                        if ($postValue != $oldContent[$postKey]) {
                            array_push($changedMethods, $postKey);
                        }
                    } else {
                        array_push($changedMethods, $postKey);
                    }
                }
            }
        }

        if (count($changedMethods) > 0) {
            $returnString = "";
            for ($i = 0; $i < count($changedMethods); $i++) {
                $method = str_replace('get', '', $changedMethods[$i]);
                $method = preg_split('/(?<=[a-z])(?=[A-Z])/x', $method);
                $method = join($method, " ");

                if (count($changedMethods) == $i + 1 and count($changedMethods) != 1) {
                    $returnString .= ' and ' . ucfirst($method);
                } elseif ($i != 0) {
                    $returnString .= ', ' . ucfirst($method);
                } else {
                    $returnString .= ucfirst($method);
                }
            }
            $str = $username . ' has changed the ' . $returnString . ' on ' . date('D d/m/Y h:i A');
            $this->addToEntity($str);
        }

        /* $transGetterMethods = array();
          $languages = \AppKernel::$subLang;
          foreach ($languages as $language) {
          if (is_object($this->oldEntity->getTranslation($language))) {
          $transMethods = get_class_methods($this->oldEntity->getTranslation($language));

          foreach ($transMethods as $transMethod) {
          if (strpos($transMethod, 'get') !== false AND ! in_array($transMethod, $this->excludeMethods)) {
          array_push($transGetterMethods, $transMethod);
          }
          }

          foreach ($transGetterMethods as $method) {
          if (is_string($this->oldEntity->getTranslation($language)->$method())) {
          echo $this->oldEntity->getTranslation($language)->$method() . ' != ' . $this->newEntity->getTranslation($language)->$method();
          if ($this->oldEntity->getTranslation($language)->$method() != $this->newEntity->getTranslation($language)->$method()) {
          echo $method . ' changed from ' . $this->oldEntity->getTranslation($language)->$method() . " to " . $this->newEntity->getTranslation($language)->$method();
          }
          }
          }
          }
          echo var_dump($transGetterMethods);
          } */
    }

    private function addToEntity($str) {
        if ($this->newEntity->getLog() == NULL) {
            $log = new \PN\Bundle\CMSBundle\Entity\Log();
            $logArray = array($str);
            $log->setLog($logArray);
            $this->newEntity->setLog($log);
        } else {
            $logArray = $this->newEntity->getLog()->getLog();
            array_push($logArray, $str);
            $this->newEntity->getLog()->setLog($logArray);
        }
    }

}
