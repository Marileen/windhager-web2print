<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */
namespace Web2PrintBlackbit\Events;

use Pimcore\Model\Object;


class Product {

    private static $parentCheckDisabled = false;

    public static function preSendData(\Zend_EventManager_Event $event){

        $object = $event->getParam('object');
        if($object instanceof Object\Product){
            $helper = new \Web2PrintBlackbit\Helper\ProductLayout();
            $helper->modifyProductLayout($object,$event);
        }
    }

    public static function deleteChannelStates(\Zend_EventManager_Event $event)
    {
        $object = $event->getTarget();
        if ($object instanceof Object\Product && $object->getProductType() == 'article') {
            \Pimcore\Db::get()->delete('product_missing_fields','o_id=' . $object->getId());
        }
    }

    public static function setChannelStates(\Zend_EventManager_Event $event){
        $object = $event->getTarget();
        if($object instanceof Object\Product && $object->getProductType() == 'article'){

            //temporary enable inheritance because otherwise it is disabled in cli but not in the admin
            $originalInheritance = Object\AbstractObject::getGetInheritedValues();
            Object\AbstractObject::setGetInheritedValues(true);

            $originalFallbackLanguages = Object\Localizedfield::doGetFallbackValues();
            Object\Localizedfield::setGetFallbackValues(true);

            $db = \Pimcore\Db::get();
            $db->delete('product_missing_fields','o_id=' . $object->getId());
            $channelConfig= include \Pimcore\Config::locateConfigFile("channels.php");


            $fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();
            $lf = array_keys(current($object->getLocalizedfields()->getItems()));

            $categoryFields = ['mainCategory','mainCategory_ecom_6','mainCategory_ecom_7','mainCategory_gardiso','mainCategory_is24'];

            $hasCategory = false;
            foreach($categoryFields as $field){
                $getter = "get" .ucfirst($field);
                if($object->$getter()){
                    $hasCategory = true;
                    break;
                }
            }

            foreach((array)$fieldMatrix['channelFields'] as $channel => $entry){
                $missing = [];

                foreach((array)$entry['mandatory'] as $field){

                    if(in_array($field,$categoryFields) && $hasCategory){
                        continue;
                    }

                    $getter = "get" .ucfirst($field);

                    $e = null;

                    if(in_array($field,$lf)){
                        $missingLocale = '';
                        foreach(\Pimcore\Tool::getValidLanguages() as $language){
                            if(!$object->$getter($language)){
                                $missingLocale .= ','.$language;
                            }
                        }
                        if($missingLocale){
                            $missing[] = ['fieldName' => $field,'channel' => $channel,'locale' => $missingLocale,'o_id' => $object->getId()];
                        }
                    }else{
                        if(!$object->$getter()){
                            $missing[] = ['fieldName' => $field,'channel' => $channel,'locale' => '','o_id' => $object->getId()];
                        }
                    }

                }

                foreach($missing as $e){
                    $db->insert('product_missing_fields',$e);
                }

                $readyState = empty($missing);

                $channelState = $object->{"getChannelState" . ucfirst($channel)}();
                if($channelState == 'ignore'){
                    $readyState = false;
                }elseif($channelState == 'ready'){
                    $readyState = true;
                }

                #echo $channel."\n";
                #var_dump($missing);
                $object->{"setChannelReady" . ucfirst($channel)}($readyState);
            }
            Object\AbstractObject::setGetInheritedValues($originalInheritance);
            Object\Localizedfield::setGetFallbackValues($originalFallbackLanguages);
        }
    }

    public static function preAddProduct(\Zend_EventManager_Event $event){
        if(self::getParentCheckDisabled()) {
            return;
        }

        $object = $event->getTarget();
        if($object instanceof Object\Product){
            $object->setProductType('product');
            if($_GET['_dc']){ //only in pimcore backend
                $paths = explode('/',$object->getParent()->getFullPath());
                if($paths[1] != 'products' || count($paths) != 5){
                    throw new \Pimcore\Model\Element\ValidationException('The product has to be created in the /prodocts/xxx/xxx/xxx folder (3rd level).');
                }
            }
        }
    }

    public static function updateMockup(\Zend_EventManager_Event $event){
        $object = $event->getTarget();
        if($object instanceof Object\Product){
            \Web2PrintBlackbit\Mockup\Xpace::updateMockupData($object);
        }
    }

    public static function checkParent(\Zend_EventManager_Event $event){
        $object = $event->getTarget();


        if($object instanceof Object\Product){
            if($object->getProductType() == 'virtual' ){
                $parent = $object->getParent();
                if($parent instanceof Object\Product && $parent->getProductType() != 'virtual'){
                    throw new \Exception('Parent of virtual product must be a virtual product. ObjectId: ' . $object->getId().' ParentId: ' . $parent->getId());
                }
            }
        }
        if(self::getParentCheckDisabled()) {
            return;
        }

        if($object instanceof Object\Product){

            if($originalObject = \Web2PrintBlackbit\Product::getById($object->getId())) {
                if($originalObject->getParentId() == $object->getParentId()) {
                    return;
                }
            }

            $getParent = function($object){
                while ($parent = $object->getParent()){
                    if($parent instanceof \Web2PrintBlackbit\Product){
                        if($parent->getProductType() == 'virtual'){
                            return $parent;
                        }else{
                            $object = $parent;
                        }
                    }else{
                        return false;
                    }
                }
            };


            if($object->getProductType() == 'article' && $object->getParent()->getProductType() != 'product'){
                throw new \Pimcore\Model\Element\ValidationException('The parent has to be an product which has the ProductType set to "product". ID:' . $object->getId());
            }

            if($object->getProductType() == 'product' && $object->getParent()->getProductType() != 'virtual'){
                throw new \Pimcore\Model\Element\ValidationException('The parent has to be an product which has the ProductType set to "virtual".ID:' . $object->getId());
            }

            if(in_array($object->getProductType(),['product','article'])){


                $newParent = $getParent($object);
                \Zend_Registry::set('object_'.$object->getId(),null); //reset registry - otherwise we would get the changed object
                $oldParent = $getParent(Object\Product::getById($object->getId()));

                //check for $oldParent -> wenn das Produkt in /products/Unspezifiziert liegt, gib es keinen Parent.
                if($oldParent && ($newParent->getId() != $oldParent->getId())){
                    throw new \Pimcore\Model\Element\ValidationException('The virtual product cant be changed');
                }
            }

        }
    }


    public static function setSpeed4TradeExportItems(\Zend_EventManager_Event $event){
        $object = $event->getTarget();
        if($object instanceof Object\Product){
            $useObject = $object;
            if($object->getProductType() == 'article'){
                $parent = $object->getParent();
                if($parent instanceof \Web2PrintBlackbit\Product){
                    $useObject = $parent;
                }
            }

            if($useObject->getPBKey() != 'R'){
                $query = 'INSERT INTO windhager_speed4_trade_process_item_list (SELECT oo_id,null as processed FROM object_' . $useObject::classId().' WHERE concat(o_path,o_key) LIKE "' . $useObject->getFullPath().'%" AND productType="product" AND concat(o_path,o_key) NOT LIKE "/products/Unspezifiziert%") ON DUPLICATE KEY UPDATE oo_id = object_1.oo_id,processed=null';
                \Pimcore\Db::get()->query($query);
            }
        }
    }

    /**
     * @return bool
     */
    public static function getParentCheckDisabled()
    {
        return self::$parentCheckDisabled;
    }

    /**
     * @param bool $parentCheckDisabled
     */
    public static function setParentCheckDisabled($parentCheckDisabled)
    {
        self::$parentCheckDisabled = $parentCheckDisabled;
    }

    public static function deleteTodos(\Zend_EventManager_Event $event)
    {
        $object = $event->getTarget();
        if ($object instanceof Object\Product) {
            $list = new \Pimcore\Model\Object\TodoItem\Listing();
            $list->setCondition('targetItem__id = ?',[$object->getId()]);
            foreach($list->load() as $item){
                $item->delete();
            }
        }
    }

    public static function setTodos(\Zend_EventManager_Event $event){
        $object = $event->getTarget();
        /**
         * @var \Web2PrintBlackbit\Product $orig
         */
        if ($object instanceof Object\Product && $object->getProductType() == 'article' && $object->getEsbType() == 'F') {
            $orig = \Pimcore\Model\Object\AbstractObject::getById($object->getId(),true);

            $origParentType = $currentParentType = '';

            if($orig->getParent() instanceof \Web2PrintBlackbit\Product){
                $origParentType = $orig->getParent()->getProductType();
            }

            if($object->getParent() instanceof \Web2PrintBlackbit\Product){
                $currentParentType = $object->getParent()->getProductType();
            }


            if($origParentType != 'product' && $currentParentType == 'product'){
                foreach(['ECM','SAL DIY','SAL ONL'] as $role){
                    $todoItem = new \Web2PrintBlackbit\TodoItem();
                    $todoItem->setRole($role);
                    $todoItem->setTargetItem($object);
                    $todoItem->setItemType('new_article');
                    $todoItem->setItemSubType($object->getEsbType());
                    $todoItem->save();
                }
            }

            //lifecycle changed
            for($i = 1; $i <= 5; $i++){
                $getter = "getDateLifecycle".$i;
                $origValue = $orig->$getter();
                $currentValue = $object->$getter();

                if($orig->$getter() != $object->$getter()){
                    foreach(['MKT','ECM','SAL DIY','SAL ONL'] as $role){
                        $todoItem = new \Web2PrintBlackbit\TodoItem();
                        $todoItem->setRole($role);
                        $todoItem->setTargetItem($object);
                        $todoItem->setItemType('lifecycle');
                        $todoItem->setItemSubType($i);
                        $todoItem->save();
                    }
                }
            }







        }
    }

}