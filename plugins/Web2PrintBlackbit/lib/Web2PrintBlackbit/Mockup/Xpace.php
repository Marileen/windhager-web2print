<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 06.06.2017
 * Time: 11:58
 */

namespace Web2PrintBlackbit\Mockup;

class Xpace {


    public static function updateMockupData($object){
        $db = \Pimcore\Db::get();
        $ids = $db->fetchCol('SELECT o_id FROM object_' . $object->getClassId().' WHERE o_path LIKE "'.$object->getFullPath().'%" AND productType="article"');
        array_unshift($ids,$object->getId());

        if(count($ids) > 30){
            $db->query('UPDATE windhager_mockup_article_xpace set calculateInBackground=1 where oo_id IN('.implode(',',$ids).')');
        }else{
            foreach($ids as $i => $id){
                self::doUpdateMockupData($id);
            }
        }

    }

    public static function doUpdateMockupData($id){
        /**
         * @var $article \Web2PrintBlackbit\Product
         */
        $article = \Web2PrintBlackbit\Product::getById($id);
        $tmp = [];
        $tmp['id'] = $article->getId();
        $tmp['order-id'] = $article->getArt_no();
        $tmp['name'] = $article->getEsbTitle1();

        $tmp['can-hang'] = $article->getPlanogramPlaceableHanging() ? 'true' : 'false';
        $tmp['can-stack'] = $article->getPlanogramPlaceableStacked() ? 'true' : 'false';
        $tmp['can-heap'] = $article->getPlanogramPlaceablePoured() ? 'true' : 'false';
        $tmp['pack-unit'] = $article->getVke();
        $tmp['class'] = 'product';
        $tmp['ean']  = $article->getEan();
        $tmp['brand']  = '';


        $tmp['gap-x'] = $article->getHandleCutoutLeftRight() ? $article->getHandleCutoutLeftRight()*10 : 10;
        $tmp['gap-y'] = $article->getHandleCutoutTopBottom() ? $article->getHandleCutoutTopBottom()*10 : 10;
        $tmp['gap-z'] = $article->getHandleCutoutFrontBack() ? $article->getHandleCutoutFrontBack()*10 : 0;


        if($brand = $article->getBrand()){
            $tmp['brand'] = trim($brand->getTitle());
        };
        $tmp['category'] = $article->getUNTGR();

        $tmp['unit-width'] = $article->getWidthBME()*10;
        $tmp['unit-height'] = $article->getHeightBME()*10;
        $tmp['unit-depth'] = $article->getDepthBME()*10;

        $tmp['put-size-x'] = $article->getWidthBME()*10;
        $tmp['put-size-y'] = $article->getHeightBME()*10;
        $tmp['put-size-z'] = $article->getDepthBME()*10;

        $tmp['max-x'] = $article->getPlanogramMaxHorizontal();
        $tmp['max-y'] = $article->getPlanogramMaxVertical();
        $tmp['max-z'] = $article->getPlanogramMaxInFront();

        $tmp['min-x'] = $article->getPlanogramMinHorizontal();
        $tmp['min-y'] = $article->getPlanogramMinVertical();
        $tmp['min-z'] = $article->getPlanogramMinInFront();

        $tmp['default-x'] = $article->getPlanogramDefHorizontal();
        $tmp['default-y'] = $article->getPlanogramDefVertical();
        $tmp['default-z'] = $article->getPlanogramDefInFront();


        $images = $fragment = [];
        if($article->getPlanogramObjectType()){
            $fragment['kind'] = $article->getPlanogramObjectType();
        }
        $websiteConfig = \Pimcore\Config::getWebsiteConfig()->toArray();


        $images = [];

        $imageFields = [];
        //Bei den boxen werden alle Bilder, ausgenommen side=“all“ aufgeführt, wenn gepflegt. (imagePositionType siehe unten!)
        if($fragment['kind'] == 'box'){
            $imageFields = ['left','right','top','bottom','front','back','all'];
        //Bei den cylindern ist lediglich 1 Bild anzugeben, die anderen werden nicht aufgeführt: <image side=“all“ src=“…“>
        }elseif($fragment['kind'] == 'cylinder'){
            $imageFields = ['all'];
        }


        foreach($imageFields as $field){
            $getter = "getImage_planogramm_" . $field;
            $getterTransparent = "getImage_transparent_" . $field;

            $value = $article->$getter();
            if($value instanceof \Pimcore\Model\Object\Data\Hotspotimage){
                $entry = ['side' => $field];
                $entry['src'] = $websiteConfig['externalImageUrl'] . $value->getThumbnail('product-detail-big');

                if($transparent = $article->$getterTransparent()){
                    $entry['msk'] = $websiteConfig['externalImageUrl'] . $transparent->getThumbnail('product-detail-big');
                }

                $images[] = $entry;
            }
        }
        if($fragment['kind'] == 'box'){
            $fragment['sx'] = $article->getWidthBME()*10;
            $fragment['sy'] = $article->getHeightBME()*10;
            $fragment['sz'] = $article->getDepthBME()*10;
            $fragment['color'] = '';
        }

        if($fragment['kind'] == 'cylinder'){
            $fragment['radius'] = $article->getWidthBME()*10/2;
            $fragment['px'] = $article->getWidthBME()*10/2;
            $fragment['pz'] = $article->getWidthBME()*10/2;
            $fragment['rx'] = -90;
            $fragment['slices'] = 24;
            $fragment['length'] = $article->getHeightBME()*10;
            $fragment['color'] = '';

        }

        $tmp['euro-x'] = $tmp['euro-y'] = '';
        if(!$article->getEuroHoleMiddleLeft() && $article->getPlanogramPlaceableHanging()){
            if($article->getWidthBME()){
                $tmp['euro-x'] = ($article->getWidthBME()*10)/2;
            }
        }

        if(!$article->getEuroHoleMiddleTop() && $article->getPlanogramPlaceableHanging()){
            $tmp['euro-y'] = 15;
        }

        if($color = $article->getPlanogramNonImageColorCode()){
            $fragment['color'] = $color->getCssColor();
        }
        $tmp['fragment'] = $fragment;
        $tmp['images'] = $images;

        $storageData = ['data' => $tmp,'metaData' => ['path' => $article->getPath()]];

        if($_GET['test']){
            p_r($storageData); Exit;
        }

        $db = \Pimcore\Db::get();

        $db->insertOrUpdate('windhager_mockup_article_xpace',['oo_id' => $article->getId(),'data' => json_encode($storageData),'calculateInBackground' => 0]);
        return $storageData;
    }
}