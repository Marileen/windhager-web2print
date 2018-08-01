<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 30.01.2017
 * Time: 10:19
 */
namespace Web2PrintBlackbit\Controller\Assistant;

use \Pimcore\Model\Object;

class Webfrontend {

    protected $sessionNamespace = 'webfrontend';

    public function getUser(){

        $session = $this->getSession();
        if($session->userId){
            if($session->userType == 'regular'){
                return Object\WebfrontendUser::getById($session->userId);
            }else{
                return \Pimcore\Model\User::getById($session->userId);
            }
        }
        return null;
    }

    public function doLoginPimcoreUser(){
        $user = \Pimcore\Tool\Authentication::authenticateSession();

        if($user){
            $this->getSession()->userId = $user->getId();
            $this->getSession()->userType = 'pimcore';
            \Zend_Session::writeClose();
            return $this->getUser();
        }
        return false;
    }

    public function doLogin($email,$password){
        $list = new Object\WebfrontendUser\Listing();
        $list->setCondition('email = ? AND password = ?',[$email,md5($password)]);
        list($user) = $list->load();
        if($user){
            $this->getSession()->userId = $user->getId();
            $this->getSession()->userType = 'regular';
            \Zend_Session::writeClose();
            return $this->getUser();
        }else{
            return false;
        }
    }


    public function getSession(){
        return new \Zend_Session_Namespace($this->sessionNamespace);
    }

    public function doLogout(){
        \Zend_Session::namespaceUnset($this->sessionNamespace);
    }

    public function getProductTree($forceCache = false){
        $tree = [];


        $root = \Pimcore\Model\Object\Folder::getByPath('/products');


        if(!$forceCache){
            $tree = \Pimcore\Cache::load('webfronted_tree');
        }

        if(!$tree){
            $table = 'object_' . \Pimcore\Model\Object\Product::classId();
            foreach($root->getChildren() as $c){
                $check = \Pimcore\Db::get()->fetchOne('select count(*) from ' . $table . ' where productType="article" and o_path like "'.$c->getFullPath().'%"');

                if($check){
                    $name = $c->getKey();
                    $tree[] = [
                        'path' => $c->getFullPath(),
                        'name' => $name,
                        'level' => 1
                    ];
                    foreach(\Pimcore\Db::get()->fetchAll('select * from ' . $table . ' where o_parentId = ?' , [$c->getId()]) as $c2){
                        $fullPath = $c2['o_path'].$c2['o_key'];
                        $check = \Pimcore\Db::get()->fetchOne('select count(*) from ' . $table . ' where productType="article" and o_path like "'.$fullPath.'/%"');
                        if($check){
                            $name2 = $name .' &raquo; ' . $c2['o_key'];
                            $tree[] = [
                                'path' => $fullPath,
                                'name' => $name2,
                                'level' => 2
                            ];
                            foreach(\Pimcore\Db::get()->fetchAll('select * from ' . $table . ' where o_parentId = ?' , [$c2['oo_id']]) as $c3){
                                $fullPath = $c3['o_path'].$c3['o_key'];
                                $check = \Pimcore\Db::get()->fetchOne('select count(*) from ' . $table . ' where productType="article" and o_path like "'.$fullPath.'/%"');
                                if($check){
                                    $name3 = $name2 .' &raquo; ' . $c3['o_key'];
                                    $tree[] = [
                                        'path' => $fullPath,
                                        'name' => $name3,
                                        'level' => 3
                                    ];
                                }
                            }
                        }

                    }
                }
            }
            \Pimcore\Cache::save($tree,'webfronted_tree',['backoffice'],3600,0,$forceCache);
        }

        return $tree;
    }

    public function getTentants(){

    }
}