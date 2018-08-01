<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;

class Catalog {

    public static $chapterCount = null;
    protected static $totalChapters = null;

    protected static $icons = [];
    public static function getIconList(){
        if(!self::$icons){
            $folder = \Pimcore\Model\Asset::getByPath('/pim-icons/icons-svg');
            if($folder){
                $children = $folder->getChildren();
                /**
                 * @var \Pimcore\Model\Asset\Image $asset
                 */
                foreach($children as $asset){

                    if($asset instanceof \Pimcore\Model\Asset\Image){
                        $key = strtolower(substr($asset->getFilename(),0,strrpos($asset->getFilename(),'.')));

                        if(strpos($asset->getFilename(),'.svg')){
                            $src = $asset->getFullPath();
                        }else{
                            $src = (string)$asset->getThumbnail('catalog-icon',false);
                        }
                        self::$icons[$key] = [
                            'src' => $src,
                            'assetId' => $asset->getId()
                        ];
                    }
                }
            }
        }
        return self::$icons;
    }

    public static function inDateRage($date,$startDate,$endDate,$type = 'new'){
        if(!$date){
            return false;
        }

        if($type == 'catalog' && !$startDate && !$endDate){
            return true;
        }

        if(!$startDate && !$endDate){
            return false;
        }

        if(is_string($date)){
            $date = new \Carbon\Carbon($date);
        }

        $date = $date->setTime(12,0,0)->getTimestamp();

        if($startDate instanceof \Pimcore\Date){
            $startDate = $startDate->getTimestamp();
        }else{
            $startDate = 0;
        }
        if($endDate instanceof \Pimcore\Date){
            $endDate = $endDate->setHour(23)->setMinute(59)->setSecond(59)->getTimestamp();
        }else{
            $endDate = mktime(0,0,0,1,1,3000);
        }

        if($startDate < $date && $endDate > $date){
            return true;
        }else{
            return false;
        }
    }

    public static function getChapterCount($document){
        if(is_null(self::$totalChapters)){
            while ($document->getParent() instanceof \Pimcore\Model\Document\PrintAbstract){
                $document = $document->getParent();
            }
            self::$totalChapters = count($document->getChildren());
        }
        return self::$totalChapters;
    }
}