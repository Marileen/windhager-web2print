<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;
use Pimcore\Model\Asset;

class Webfrontend {


    public static function getImagesFromPanel(\Web2PrintBlackbit\Product $product, \Pimcore\Model\Object\ClassDefinition\Layout\Panel $panel){
        $addedIds = [];
        $images = [];
        foreach($panel->getChildren() as $element){


            $item = $product->{"get".$element->getName()}();
            if($item instanceof \Pimcore\Model\Object\Data\Hotspotimage){
                if($asset = $item->getImage()){
                    if(!$addedIds[$asset->getId()]){
                        $images[] = $asset;
                        $addedIds[$asset->getId()] = 1;
                    }
                }
            }elseif($item instanceof Asset\Image){
                if(!$addedIds[$asset->getId()]){
                    $images[] = $asset;
                    $addedIds[$asset->getId()] = 1;
                }
            }elseif($item instanceof Object\Fieldcollection){
                foreach($item->getItems() as $e){
                    if($e instanceof Object\Fieldcollection\Data\Images){
                        if($x = $e->getImage()){
                            if($asset = $x->getImage()){
                                if($asset instanceof Asset\Image){
                                    if(!$addedIds[$asset->getId()]){
                                        $images[] = $asset;
                                        $addedIds[$asset->getId()] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }elseif(is_array($item)){
                foreach($item as $entry){
                    if($entry['image'] instanceof Object\Data\BlockElement){
                        $image = $entry['image']->getData();

                        if($image instanceof Object\Data\Hotspotimage){
                            $asset = $image->getImage();
                            if($asset instanceof Asset\Image){
                                $images[] = $asset;
                                $addedIds[$asset->getId()] = 1;
                            }
                        }
                    }
                }
            }
        }
        return $images;
    }

    /**
     * @param \Pimcore\Model\Object\ClassDefinition\Data $element
     * @param \Web2PrintBlackbit\Product $prodcut
     */
    public static function getDisplayData($prodcut, $element,$view){

        $fieldNames = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix()['fieldTitles'];
        $getter = "get" . ucfirst($element->getName());
        $val = $prodcut->$getter();

        if($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Select){
            if($val != ''){
                $val = $view->t('class_select_value_' . $val);
            }
        }elseif($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Href){
            if($val instanceof \Pimcore\Model\Object\AbstractObject){
                if (is_callable(array($val, 'getTitle'))){
                    $val = $val->getTitle();
                }
            }
        }elseif($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Multiselect){
            $tmp = [];

            foreach((array)$val as $v){
                if($v != ''){
                    $tKey = 'class_select_value_' . $v.'.'.$element->getName();
                    $tmp[] = $view->t('class_select_value_' . $v.'.'.$element->getName());
                }
            }
            $val = implode(', ',$tmp);
        }elseif($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Multihref || $element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objects){
            $tmp = [];
            foreach((array)$val as $v){
                if($v instanceof \Web2PrintBlackbit\Product){
                    $detailUrl = $view->url(['id' => $v->getId(), 'name' => $v->getOSProductNumber() ?: 'article', 'docPath' => $view->document->getProperty('detailPage')], 'webfrontend-product-detail');
                    $tmp[] = '<a href="' . $detailUrl . '">' . $v->getArt_no() . '</a>';
                } elseif($v instanceof \Pimcore\Model\Object\ProductOperationManual){


                    $title = $v->getTitle();
                    if($element->getName() == 'sparepartsoperatinginstructions'){
                        $title .= ' (' . $v->getArtNo().')';
                    }
                    $v = '<a href="javascript:pimcore.helpers.openObject('.$v->getId().',\'object\');" class="open-pimcore" data-link-only-pimcore="1">' . $title .'</a>';
                    $tmp[] = $v;
                }else{
                    if (is_callable(array($v, 'getTitle'))){
                        $v = $v->getTitle();
                    }
                    $tmp[] = $v;
                }
            }

            $tmp = array_filter($tmp);
            $val = implode(', ',$tmp);
        }elseif($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Objectbricks) {

        }


        if($val instanceof \Pimcore\Date){
            $val = $val->get(\Pimcore\Date::DATE_MEDIUM);
        }

        if($element->getName() == 'salesDateFrom' || $element->getName() == 'salesDateTo' ){
            $val = $prodcut->$getter();
            foreach($element->getOptions() as $o){
                if($o['value'] == $val){
                    $val = $o['key'];
                    break;
                }
            }
        }
        $fieldName = $fieldNames[$element->getName()];

        if($prodcut instanceof \Pimcore\Model\Object\Objectbrick\Data\AbstractData){
            $fieldName = $element->getTitle();
        }
        return [
            'name' => $fieldName,
            'value' => $val
        ];

    }
}