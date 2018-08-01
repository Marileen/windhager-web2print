<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */

namespace Web2PrintBlackbit\Events;

use Pimcore\Model\Object;


class Note
{

    public static function setTodos(\Zend_EventManager_Event $event)
    {
        $note = $event->getTarget();

        if ($note instanceof \Pimcore\Model\Element\Note) {
            if ($note->getCtype() == 'object') {
                if ($user = \Pimcore\Tool\Authentication::authenticateSession()) {

                    $addNote = false;

                    if($user->isAdmin()){
                        $addNote = true;
                    }else{
                        foreach($user->getRoles() as $roleId){
                            $role = \Pimcore\Model\User\Role::getById($roleId);
                            if($role && $role->getName() == 'cus'){
                                $addNote = true;
                            }
                        }
                    }

                    if($addNote){
                        $o = \Pimcore\Model\Object\AbstractObject::getById($note->getCid());
                        if ($o instanceof \Web2PrintBlackbit\Product) {
                            if ($o->getProductType() == 'article' && in_array($note->getType(), ['warning', 'notice'])) {
                                $todoItem = new \Web2PrintBlackbit\TodoItem();
                                $todoItem->setRole('PMT');
                                $todoItem->setTargetItem($o);
                                $todoItem->setItemType('note');
                                $todoItem->setItemSubType($note->getType());

                                $user = $user->getName();
                                $todoItem->setMetaData($note->getId());
                                $todoItem->save();
                            }
                        }
                    }

                }
            }

        }


    }
}