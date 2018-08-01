<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */
namespace Web2PrintBlackbit\Events;

use Pimcore\Model\Object;


class Asset {

    public static function setTodos(\Zend_EventManager_Event $event){
        $asset = $event->getTarget();

        $orig = \Pimcore\Model\Asset::getById($asset->getId(),true);

        if($orig->getMetaData('lastModified') != $asset->getMetaData('lastModified')){
            $db = \Pimcore\Db::get();

            $check = $db->fetchAll('select * from object_relations_'.\Web2PrintBlackbit\Product::classId().' where fieldname="articlePassAssets" and dest_id=? AND src_id IN(select oo_id from object_' . \Web2PrintBlackbit\Product::classId().' where esbType="R")',[$asset->getId()]);
            if($check){
                foreach(['PUR','PMT'] as $role){
                    $todoItem = new \Web2PrintBlackbit\TodoItem();
                    $todoItem->setRole($role);
                    $todoItem->setTargetItem($asset);
                    $todoItem->setItemType('asset_rohwaren');
                    $todoItem->setItemSubType('changed');
                    $todoItem->setMetaData(json_encode($check));
                    $todoItem->save();
                }
            }
        }

    }
}