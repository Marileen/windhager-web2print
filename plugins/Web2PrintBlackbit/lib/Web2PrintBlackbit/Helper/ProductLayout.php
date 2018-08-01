<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;

class ProductLayout {

    protected static $roles = [];
    protected static $customLayouts = [];
    protected static $fieldMatrix = [];

    public function __construct()
    {
        if(empty(self::$roles)){
            $list = new \Pimcore\Model\User\Role\Listing();
            $roles = $list->setCondition('`type` = "role"')->load();
            foreach($roles as $r){
                self::$roles[$r->getId()] = $r;
            }
        }

        if(empty(self::$customLayouts)){
            $list = new \Pimcore\Model\Object\ClassDefinition\CustomLayout\Listing();
            $list->setCondition('classId = ? ',[\Pimcore\Model\Object\ClassDefinition::getByName('Product')->getId()]);
            $config = include \Pimcore\Config::locateConfigFile("channels.php");
            $channelIdMapper = [];
            foreach($config['channels'] as $key => $config){
                $channelIdMapper[$config['customLayoutId']]  = $key;
            }

            foreach($list->load() as $classDefinition){
                if($channelIdMapper[$classDefinition->getId()]){
                    self::$customLayouts[$classDefinition->getId()] = $classDefinition;
                }
            }
        }
    }

    public static function getFieldMatrix(){
        if(empty(self::$fieldMatrix)){
            $self = new self();
            $metaData = $self->getMetaDataArray();
            $df = \Pimcore\Model\Object\ClassDefinition::getByName('Product')->getLayoutDefinitions();
            $childs = $df->getChildren();
            $metaData = $self->getMetaDataArray();
            $metaData['roleFields'] = [];
            $metaData['fieldTitles'] = [];

            $channelIdMapper = [];
            $config = include \Pimcore\Config::locateConfigFile("channels.php");
            foreach($config['channels'] as $key => $config){
                $channelIdMapper[$config['customLayoutId']]  = $key;
            }

            $metaData['channelIdMapper'] = $channelIdMapper;
            foreach($childs as $c){
                $self->modifyValue($c,$metaData);
                #$self->modifyValueForCustomLayout($c,$metaData);
            }

            foreach(self::$customLayouts as $layout){
                $df = $layout->getLayoutDefinitions();

                $m = $self->getMetaDataArray();
                if($df && $df->getChilds()[0]){

                    foreach($df->getChilds()[0]->getChilds() as $c){
                        $self->modifyValueForCustomLayout($c,$m);
                    }
                }
                $metaData['channelFields'][$channelIdMapper[$layout->getId()]] = $m['channel'];
            }

            unset($metaData['notEditable']);
            unset($metaData['currentRole']);
            unset($metaData['mandatory']);

            $channelIdMapper = [];
            $config = include \Pimcore\Config::locateConfigFile("channels.php");
            foreach($config['channels'] as $key => $config){
                $channelIdMapper[$config['customLayoutId']]  = $key;
            }


            foreach($metaData['fields'] as $key => $data){
                if($r = $metaData['fields'][$key]['role']){
                    $metaData['roleFields'][$data['role']['id']][] = $key;
                }
                /*if($c = $metaData['fields'][$key]['channels']){
                    foreach(array_keys($c) as $channelId){
                        $metaData['channelFields'][$channelIdMapper[$channelId]][] = $key;
                    }
                }*/
            }
            unset($metaData['fields']);

            $metaData['channelRoleIds'] = array_intersect(array_keys($metaData['roleFields']),array_keys($metaData['userRoles']));

            self::$fieldMatrix = $metaData;
        }
        return self::$fieldMatrix;
    }

    protected function getMetaDataArray(){
        $user = \Pimcore\Tool\Authentication::authenticateSession();

        $metaData = ['userRoles' => []];

        //!$user for cli scripts
        if(!$user || $user->isAdmin()){
            $class = \Pimcore\Model\Object\ClassDefinition::getByName('Product');
            foreach($class->getLayoutDefinitions()->getChilds()[0]->getChilds() as $panel){
                if($panel instanceof \Pimcore\Model\Object\ClassDefinition\Layout\ExtendedPanel){
                    if($r = \Pimcore\Model\User\Role::getById($panel->getRole())){
                        $roles[] = $r;
                    }
                }
            }
        }else{
            $roles = [];
            foreach($user->getRoles() as $id){
                $roles[] = \Pimcore\Model\User\Role::getById($id);
            }
        }
        foreach((array)$roles as $role){
            $metaData['userRoles'][$role->getId()] = $role->getName();
        }

        return $metaData;
    }

    public function modifyProductLayout(\Pimcore\Model\Object\Product $object,\Zend_EventManager_Event $event){


        $target = $event->getTarget();
        $returnValueContainer = $event->getParam('returnValueContainer');

        $layoutId = (int)$_GET['layoutId'];
        if($layoutId == -1){ //admin mode layout - do not modify
            return;
        }


        $data = $returnValueContainer->getData();


        $metaData = $this->getMetaDataArray();


        if($layoutId){
            $df = clone $object->getClass()->getLayoutDefinitions();
            $childs = $df->getChildren();
            foreach($childs as $c){
                $this->modifyValue($c,$metaData,$layoutId);
            }
        }
        if($layoutId){
            $childs = $data['layout']->getChildren();
            foreach($childs as $c){
                $this->modifyValueForCustomLayout($c,$metaData);
            }
        }else{
            $childs = $data['layout']->getChildren();
            foreach($childs as $c){
                $this->modifyValue($c,$metaData);
                $this->modifyValueForCustomLayout($c,$metaData,['disableRoles' => true]);
            }
        }
    }

    protected function modifyValueForCustomLayout($c,&$metaData,$options = []){

        if(in_array($c->getName(),['System','Import'])){
            return;
        }
        if($c instanceof Object\ClassDefinition\Layout\ExtendedPanel){
            $metaData['mandatory'] = $c->getMandatory();
        }

        if($c instanceof Object\ClassDefinition\Layout\Panel){
            # $metaData['mandatory'] = $c->getMandatory();
        }

        if($c instanceof Object\ClassDefinition\Data){

            if($c->getName() != 'localizedfields'){
                $fieldData = $metaData['fields'][$c->getName()];
                if(!is_null($fieldData['noteditable'])){
                    $c->setNoteditable($fieldData['noteditable']);
                }
                #$c->setMandatory($metaData['mandatory']);

                $tooltip = '';

                if($fieldData['role']['name'] && $options['disableRoles'] != true){
                    $tooltip = '<b>Role:</b> ' . $fieldData['role']['name'];
                }


                $channels = [];
                /**
                 * @var \Pimcore\Model\Object\ClassDefinition\CustomLayout $layout
                 */
                foreach(self::$customLayouts as $layout){
                    if($element = $layout->getFieldDefinition($c->getName())){
                        $channels[$layout->getId()] = $layout->getName();
                    }
                }


                $metaData['channel'][$metaData['mandatory'] ? 'mandatory' : 'optional'][] = $c->getName();
                $metaData['fields'][$c->getName()]['channels'] = $channels;

                if($channels){
                    sort($channels);
                    if($tooltip){
                        $tooltip .= '<br/>';
                    }
                    $tooltip .= '<b>Channels:<br/></b>' . implode('<br/>',$channels);
                }
                $c->setTooltip($tooltip);
            }

        }

        if($c instanceof \Pimcore\Model\Object\ClassDefinition\Data\Block){

        }else{
            if(method_exists($c,'getChildren')){
                if($childs = $c->getChildren()){
                    foreach($childs as $c2){
                        $this->modifyValueForCustomLayout($c2,$metaData,$options);
                    }
                }
            }
        }

    }

    public function getEditableFields(){

        $df = \Pimcore\Model\Object\ClassDefinition::getByName('Product')->getLayoutDefinitions();
        $childs = $df->getChildren();
        $metaData = $this->getMetaDataArray();
        foreach($childs as $c){
            $this->modifyValue($c,$metaData);
        }

        $fields = [];
        foreach($metaData['fields'] as $key => $data){
            if($data['noteditable'] == 0){
                $fields[] = $key;
            }
        }
        return $fields;
    }


    protected function collectValueData($c,&$metaData,$level = 0){
        if($c instanceof Object\ClassDefinition\Data) {
            $metaData['fieldTitles'][$c->getName()] = $c->getTitle();
            $metaData['fieldTypes'][$c->getFieldtype()][] = $c->getName();
        }

        if(method_exists($c,'getChildren')){
            if($childs = $c->getChildren()){
                $level++;
                foreach($childs as $c2){
                    self::collectValueData($c2,$metaData,$level);
                }
            }
        }else{
            $level = 0;
        }
    }

    protected function modifyValue($c,&$metaData,$level = 0){

        if($c instanceof Object\ClassDefinition\Layout\ExtendedPanel){
            $level = 0;
            $roleId = $c->getRole();
            if($roleId){
                if(in_array($roleId,array_keys($metaData['userRoles']))){
                    # $metaData['mandatory'] = true;
                    $metaData['notEditable'] = false;
                }else{
                    #  $metaData['mandatory'] = false;
                    $metaData['notEditable'] = true;
                }
                if(self::$roles[$roleId]){
                    $metaData['currentRole'] = ['id' => self::$roles[$roleId]->getId(),'name' => self::$roles[$roleId]->getName()];
                }else{
                    $metaData['currentRole'] = [];
                }

            }
        }

        if(in_array($c->getName(),['System','Import'])){
            self::collectValueData($c,$metaData,$level);
            return;
        }

        if($c instanceof Object\ClassDefinition\Data){
            $metaData['fieldTitles'][$c->getName()] = $c->getTitle();
            $metaData['fieldTypes'][$c->getFieldtype()][] = $c->getName();

            $c->setMandatory($metaData['mandatory']);
            if(!$c->getNoteditable()){
                $c->setNoteditable($metaData['notEditable']);
            }
            if($c->getName() != 'localizedfields'){
                $metaData['fields'][$c->getName()]['noteditable'] = (int)$c->getNoteditable();

                if($metaData['currentRole']){
                    $metaData['fields'][$c->getName()]['role'] = $metaData['currentRole'];
                }
            }
        }
        if(method_exists($c,'getChildren')){
            if($childs = $c->getChildren()){
                $level++;
                foreach($childs as $c2){
                    self::modifyValue($c2,$metaData,$level);
                }
            }
        }else{
            $level = 0;
        }
    }


}