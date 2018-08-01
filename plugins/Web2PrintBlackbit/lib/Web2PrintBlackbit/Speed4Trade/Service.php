<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 04.12.2017
 * Time: 14:44
 */


namespace Web2PrintBlackbit\Speed4Trade;

class Service
{

    const MANDATOR_ID = 32;

    /**
     * @var Client
     */
    protected $client;

    protected $validLocales = [];
    public function __construct()
    {
        $this->client = new Client();

        foreach(\Pimcore\Tool::getValidLanguages() as $locale){
            list($lang,$region) = explode('_',$locale);
            if(in_array($lang,['de','en'])){
                $this->validLocales[$lang] = $locale;
            }
        }
    }

    /**
     * Dokumetnation - https://partner.speed4trade.com/api/dokumentation/index.html <- updateItems()
     * @param array $ids
     * @param bool $debug
     */

    public function updateArticles($ids,$debug = false){
        \Pimcore\Model\Object\AbstractObject::setGetInheritedValues(true);
        $fallbackLanguagesValueBackup = \Pimcore\Model\Object\Localizedfield::doGetFallbackValues();

        \Pimcore\Model\Object\Localizedfield::setGetFallbackValues(true);

        $monitoringItem = \ProcessManager\Plugin::getMonitoringItem();
        $monitoringItem->setCurrentStep(3)->save();

        $monitoringItem->setCurrentWorkload(0)->setTotalWorkload(count($ids))->save();


        //20 products per soap request
        $chunks = array_chunk($ids,20);


        $currentWorkload = 0;
        foreach($chunks as $x => $ids){
            $domDoc = new DOMDocumentExtended();
            $domDoc->preserveWhiteSpace = false;
            $domDoc->formatOutput = true;

            $request = $domDoc->createElement('request');
            $request->setAttribute('version', '1.0.0');
            $request->setAttribute('method', 'updateItems');
            \Zend_Registry::set('forceFallbackValues',true);
            foreach ($ids as $i => $id) {
                $currentWorkload++;
                $monitoringItem->setMessage('Processing Item ' . $id)->save();

                /**
                 * @var \Web2PrintBlackbit\Product $o
                 */
                $o = \Web2PrintBlackbit\Product::getById($id);

                if($o->getOnlineSpeed4Trade() == 'yes'){
                    if($o->getProductType() == 'product'){
                        $item = $this->getProductXML($domDoc, $request, $o);
                        $request->appendChild($item);

                        foreach($o->getChildren() as $o){
                            if($o->getOnlineSpeed4Trade() == 'yes'){
                                $item = $this->getVariantXML($domDoc, $request, $o);
                                $request->appendChild($item);
                            }else{
                                $monitoringItem->getLogger()->info('Skipping article ' . $id.' - not defined for export');
                            }
                        }
                    }
                }else{
                    $monitoringItem->getLogger()->info('Skipping product ' . $id.' - not defined for export');
                }
            }
            \Zend_Registry::set('forceFallbackValues',false);

            $domDoc->appendChild($request);
            if($_GET['test']){
                header("Content-type: text/xml");
                die($domDoc->saveXML());
            }
            $monitoringItem->setMessage('Sending chunk to Speed4Trade: (Package '.$x.' from ' .count($chunks).')');
                $result = $this->client->call('updateItems',$domDoc->saveXML());


            $monitoringItem->setCurrentStep(4)->setCurrentWorkload($currentWorkload)->setMessage('Processing response')->setTotalWorkload(count($ids))->save();
            $i = 0;
           # if($_GET['id']){
              #  p_r($result->report);
                #exit;
         #   }

            foreach($result->report as $entry){
                $i++;
                $returnCode = (int)$entry->attributes()->return_code;
                $id = (string)$entry->item->internal_item_number;

                #var_dump($entry);
                if($returnCode == 0){
                    $speed4TradeId = (int)$entry->item->item_id;
                    $monitoringItem->getLogger()->debug('Processing result - ProductId :' .$id);

                    /**
                     * @var \Web2PrintBlackbit\Product $product
                     */

                    //proukt - intrnal item number = P + Pimcore Id
                    if($id[0] == 'P'){
                        $id = str_replace('P','',$id);
                        $product = \Web2PrintBlackbit\Product::getById($id);
                    }else{
                        //Artikel - internal item number = Artikelnummer
                        $product = \Web2PrintBlackbit\Product::getByArt_No($id,['unpublished' => true,'limit' => 1]);
                    }

                    if($product->getSpeed4TradeId() != $speed4TradeId){
                        $product->setSpeed4TradeId($speed4TradeId)->save();
                    }
                    \Pimcore\Db::get()->update('windhager_speed4_trade_process_item_list',['oo_id' => $product->getId(),'processed' => time()],'oo_id='.$product->getId());
                }else{
                    $monitoringItem->getLogger()->emergency('Product Update failed:' .$id .' Data: ' . print_r($entry,true),['relatedObject' => $product]);
                }

            }
           # die('done');
            $monitoringItem->setMessage('Processing Response finished. ')->save();
            \Pimcore::collectGarbage();

        }









        $monitoringItem->setWorloadCompleted()->save();

        \Pimcore\Model\Object\Localizedfield::setGetFallbackValues($fallbackLanguagesValueBackup);


        if($debug){
            $attr = (array)$result->report->attributes();
            if($attr['@attributes']['return_code'] != 0){
                p_r($result); exit;
            }
            header("Content-type: text/xml");
            die($domDoc->saveXml());
        }

    }


    protected function applyProductAndVariantValues(DOMDocumentExtended $domDoc, $rootElement, \Web2PrintBlackbit\Product $object, \DOMElement $item){
        $item->setAttribute('significant_key', 'internal_item_number');

        $internalItemNumber = null;
        if($object->getProductType() == 'article'){
            $internalItemNumber = $object->getArt_no();
        }elseif($object->getProductType() == 'product'){
            $internalItemNumber = 'P'.$object->getId();
        }

        $item->appendChild($domDoc->createElement('internal_item_number', $internalItemNumber));
        $item->appendChild($domDoc->createElement('debug_speed4trade_id', $object->getSpeed4TradeId()));

        $item->appendChild($domDoc->createElement('mandator_id', self::MANDATOR_ID));
        $item->appendChild($domDoc->createElement('vendor_item_number', $object->getArt_no()));
        $item->appendChild($domDoc->createElement('tax_type_id', 3));
        $item->appendChild($domDoc->createElement('identifier', $object->getTitle_ecom_6()));


        //
        $item->appendChild($domDoc->createElement('length', $object->getItem_length()));
        $item->appendChild($domDoc->createElement('width', $object->getItem_width()));
        $item->appendChild($domDoc->createElement('height', $object->getItem_height()));
        $item->appendChild($domDoc->createElement('distance_unit_id', 110));
        $item->appendChild($domDoc->createElement('weight', $object->getWeightTotalBME()));
        $item->appendChild($domDoc->createElement('weight_unit_id', 405));

        $item->appendChild($domDoc->createElement('package_length', $object->getDepthBME()));
        $item->appendChild($domDoc->createElement('package_width', $object->getWidthBME()));
        $item->appendChild($domDoc->createElement('package_height', $object->getHeightBME()));
        $item->appendChild($domDoc->createElement('package_distance_unit_id', 110));


        $item->appendChild($domDoc->createElement('package_weight', $object->getWeightTotalBME()));
        $item->appendChild($domDoc->createElement('package_weight_unit_id', 405));
        $item->appendChild($domDoc->createElement('packaging_unit', $object->getUnitBME()));

        $isData = $domDoc->createElement('specific_item_data');

        $highlightsAmazon = $object->getHighlights_ecom_2();
        $titleAmazon = $object->getTitle_ecom_2();
        if($highlightsAmazon || $titleAmazon){
            $amazon = $domDoc->createElement('amazon_item_data');

            $scd = $domDoc->createElement('subchannel_specific_data');
            $sc = $domDoc->createElement('subchannel_specific');

            if($titleAmazon){
                $scd->appendChild($domDoc->createElement('title',$titleAmazon));
            }

            $rows = array_filter(explode_and_trim("\n",$highlightsAmazon));
            sort($rows);
            if($rows){
                $i = 1;

                for($i = 1; $i <= 5; $i++){
                    if($s = $rows[$i-1]){
                        $scd->appendChild($domDoc->createElement('bulletpoint'.$i,$s));
                    }
                }
            }

            $amazon->appendChild($scd);

            $isData->appendChild($amazon);
        }

        if($s = $object->getTitle_rakuten()){
            $rd = $domDoc->createElement('rakuten_item_data');

            $account_specific_data = $domDoc->createElement('account_specific_data');
            $account_specific = $domDoc->createElement('account_specific');
            $account_specific->appendChild($domDoc->createElement('account_id','3372482'));
            $account_specific->appendChild($domDoc->createElement('identifier',$s));
            $account_specific_data->appendChild($account_specific);
            $rd->appendChild($account_specific_data);
            $isData->appendChild($rd);
        }


        if($s = $object->getTitle_ecom_3()){
            $rd = $domDoc->createElement('ebay_item_data');
            $account_specific_data = $domDoc->createElement('account_specific_data');
            $account_specific = $domDoc->createElement('account_specific');
            $account_specific->appendChild($domDoc->createElement('account_id','123'));
            $account_specific->appendChild($domDoc->createElement('title',$s));
            $account_specific_data->appendChild($account_specific);
            $rd->appendChild($account_specific_data);
            $isData->appendChild($rd);


        }



        $item->appendChild($isData);

        $textModules = $domDoc->createElement('text_modules');
        $textModules->setAttribute('delete',true);

        $textModulesMapping = [];

        $entries = \ConfigManager\Configuration::getByAccessKey('speed4trade-textmapping')->getEntries();
        foreach($entries as $entry){
            $textModulesMapping[$entry->getValue()] = ['field' => $entry->getKey(), 'index' => $entry->getIndex()];
        }

        foreach($textModulesMapping as $id => $e){
            $field = $e['field'];
            $index = $e['index'];
            $getter  = "get" . ucfirst($field);

            $textModule = $domDoc->createElement('text_module');
            $textModule->appendChild($domDoc->createElement('text_module_type', $id));

            foreach($this->validLocales as $lang => $locale) {
                $value = $object->$getter($locale);

                if ($index){
                    $value = explode_and_trim("\n",$value);
                    $value = $value[$index-1];
                }
                if($value){
                    $translation = $domDoc->createElement('translation');
                    $translation->appendChild($domDoc->createElement('language_code_iso',$lang));
                    $translation->appendChild($domDoc->createElement('text',$value));
                    $textModule->appendChild($translation);
                }
            }
            $textModules->appendChild($textModule);
        }
        $item->appendChild($textModules);


        $imageModules =  $domDoc->createElement('image_modules');
        $imageModules->setAttribute('delete',true);
        $imageModulesMapping = [];

        $entries = \ConfigManager\Configuration::getByAccessKey('speed4trade-imagemapping')->getEntries();
        foreach($entries as $entry){
            $imageModulesMapping[$entry->getValue()] = ['field' => $entry->getKey(),'index' => $entry->getIndex()];
        }

        $websiteConfig = \Pimcore\Config::getWebsiteConfig()->toArray();
        $addImage = function ($url,$id) use ($imageModules,$domDoc){
            $module = $domDoc->createElement('image_module');
            $module->appendChild($domDoc->createElement('image_module_type_id',$id));
            $module->appendChild($domDoc->createElement('image_url',$url));
            $module->appendChild($domDoc->createElement('is_local',false));
            $imageModules->appendChild($module);
        };

        foreach($imageModulesMapping as $id => $e){
            $field = $e['field'];
            $index = $e['index'];
            $getter  = "get" . ucfirst($field);

            $value = $object->$getter();
            if($value){
                if(is_array($value)){

                    if($index){
                        $value = [$value[$index-1]];
                    }
                    foreach($value as $v){
                        if($v['image']->data){
                            try {
                                $url = $websiteConfig['externalImageUrl'].$v['image']->data->getThumbnail('product-detail-big',false);
                            }catch(\Exception $e){
                                \ProcessManager\Plugin::getMonitoringItem()->getLogger()->emergency('Cant get thumbnail. ' . $getter.' ' . $object->getId());
                            }
                        }else{
                            $url = '';
                        }
                        $addImage($url,$id);
                        break;
                    }

                }else{

                    if($value instanceof \Pimcore\Model\Object\Data\Hotspotimage){
                        $url = $websiteConfig['externalImageUrl'].$value->getThumbnail('product-detail-big',false);
                    }else{
                        $url = '';
                    }
                    $addImage($url,$id);
                }
            }else{
                $addImage('',$id);
            }
        }

        $item->appendChild($imageModules);



/*
        $itemAttributes = $domDoc->createElement('item_attributes');
        $itemAttributes->setAttribute('clear','true');


        $attribVAlue = $domDoc->createElement('item_attribute_value');
        $attribVAlue->setAttribute('significant_key','attribute_value_id');

        $attribVAlue->appendChild($domDoc->createElement('attribute_value_id','2397543'));

        $itemAttributeValus = $domDoc->createElement('item_attribute_values');
        $itemAttributes->appendChild($attribVAlue);

        $item->appendChild($itemAttributes);
*/



        $category = $object;
        while($category->getProductType() != 'virtual'){
            $category = $category->getParent();
        }
        $item->appendChild($domDoc->createElement('external_category_id', $category->getId()));
    }

    /**
     * @param \Web2PrintBlackbit\Speed4Trade\DOMDocumentExtended $domDoc
     * @param array $data
     */
    protected function createAttribute($domDoc,$data){
        $attribute =  $domDoc->createElement('attribute');
        $property =  $domDoc->createElement('property');
        $value = $domDoc->createElement('value');

        $property->appendChild($domDoc->createElement('identifier',$data['identifier']));
        $value->appendChild($domDoc->createElement('identifier',$data['value']['identifier']));
        if($translations = $data['value']['translations']){
            foreach($this->validLocales as $lang => $locale){

                $t = $domDoc->createElement('translation');
                $t->appendChild($domDoc->createElement('language_code_iso',$lang));
                $t->appendChild($domDoc->createElement('text',$translations[$lang]));
                $value->appendChild($t);

            }
        }

        $attribute->appendChild($property);
        $attribute->appendChild($value);
        return $attribute;
    }

    protected function getVariantXML(DOMDocumentExtended $domDoc, $rootElement, \Web2PrintBlackbit\Product $object)
    {
        $item = $domDoc->createElement('item');

        $this->applyProductAndVariantValues($domDoc,$rootElement,$object,$item);

        $item->appendChild($domDoc->createElement('ean_code', $object->getEan()));

        $variant = $domDoc->createElement('variant');
        $variant->appendChild($domDoc->createElement('variant_standard','false'));
        $variant->appendChild($domDoc->createElement('external_variant_parent_id',$object->getParent()->getId()));


        $attributes = $domDoc->createElement('attributes');


        $colors = $object->getEcom_color();

        /**
         * @var \Web2PrintBlackbit\AttributeColor $color
         */
        if($color = array_shift($colors)){
            $data = ['identifier' => 'Hauptfarbe'];
            $data['value'] = ['identifier' => $color->getTitle('de_DE')];
            foreach($this->validLocales as $lang => $locale){
                $data['value']['translations'][$lang] = $color->getTitle($locale);
            }
            $attribute = $this->createAttribute($domDoc,$data);
            $attributes->appendChild($attribute);
        }


        $itemAttributes = $domDoc->createElement('item_attributes');
        $itemAttributes->setAttribute('clear',"true");

        $itemAttribute = $domDoc->createElement('item_attribute');
        $itemAttribute->setAttribute('significant_key','attribute_id');
        $itemAttribute->setAttribute('delete','false');

        if($colors){
            $itemAttribute->appendChild($domDoc->createElement('attribute_id',9382593));
            $itemAttribute->appendChild($domDoc->createElement('attribute_identifier','Zusatzfarbe'));
            $itemAttribute->appendChild($domDoc->createElement('attribute_data_type','MULTILANGUAGE_STRING'));
            $values = $domDoc->createElement('item_attribute_values');

            foreach($colors as $color) {
                $val = $domDoc->createElement('item_attribute_value');
                $val->setAttribute('significant_key','attribute_value_id');

                $val->appendChild($domDoc->createElement('attribute_value_id',$color->getId()));
                $val->appendChild($domDoc->createElement('attribute_value_identifier',$color->getTitle('de_DE')));

                $trans = $domDoc->createElement('translations');
                foreach($this->validLocales as $lang => $locale){
                    $t = $domDoc->createElement('translation');
                    $t->appendChild($domDoc->createElement('language_code_iso',$lang));
                    $t->appendChild($domDoc->createElement('text',$color->getTitle($locale)));
                    $trans->appendChild($t);
                }
                $val->appendChild($trans);
                $values->appendChild($val);
            }

            $itemAttribute->appendChild($values);
            $itemAttributes->appendChild($itemAttribute);

            $item->appendChild($itemAttributes);
        }








        $mapping = [
            'item_length' => 'Länge',
            'item_width' => 'Breite',
            'item_height' => 'Höhe',
        ];

        foreach($mapping as $field => $target){
            if($value = $object->{"get".ucfirst($field)}()){
                $data = ['identifier' => $target];

                if($v = $object->getSize_unit()){
                    $value .= ' ' . $v->getTitle();
                    $value = trim($value);
                }
                $data['value'] = ['identifier' => $value];

                $attribute = $this->createAttribute($domDoc,$data);
                $attributes->appendChild($attribute);
            }
        }

        if($object->getPmtMarketingAttributes()){
            $brick = $object->getPmtMarketingAttributes()->getPMTAttributeLot();
            if($brick && $v = $brick->getLot()){
                $data = ['identifier' => 'Stückzahl'];
                $data['value'] = ['identifier' => $v];
                $attributes->appendChild($this->createAttribute($domDoc,$data));
            }
          /*  $grammage = $object->getPmtMarketingAttributes()->getPMTAttributeGrammage();
            if($grammage){
                if($v = $grammage->getGrammage()){
                    $data = ['identifier' => 'Grammatur'];
                    $data['value'] = ['identifier' => $v.' g/m²'];
                    $attributes->appendChild($this->createAttribute($domDoc,$data));
                }
            }

            $brick = $object->getPmtMarketingAttributes()->getPMTAttributeDiameter();
            if($brick && $v = $brick->getDiameter()){
                if($unit = $brick->getDiameter_unit()){
                    $v .= ' ' . $unit->getTitle();
                    $v = trim($v);
                }
                $data = ['identifier' => 'Durchmesser'];
                $data['value'] = ['identifier' => $v];
                $attributes->appendChild($this->createAttribute($domDoc,$data));
            }



            $brick = $object->getPmtMarketingAttributes()->getPMTAttributeThickness();
            if($brick && $v = $brick->getThickness()){
                $data = ['identifier' => 'Dicke'];

                if($unit = $brick->getThickness_unit()){
                    $v .= ' ' . $unit->getTitle();
                    $v = trim($v);
                }
                $data['value'] = ['identifier' => $v];
                $attributes->appendChild($this->createAttribute($domDoc,$data));
            }

            $brick = $object->getPmtMarketingAttributes()->getPMTAttributeMesh();
            if($brick){
                $v = array_filter([$brick->getMeshHeight(),$brick->getMeshWidth()]);
                if($v){
                    $v = implode(' x ', $v).' mm';
                    $data = ['identifier' => 'Maschenweite'];
                    $data['value'] = ['identifier' => $v];
                    $attributes->appendChild($this->createAttribute($domDoc,$data));
                }
            }*/
        }




        $variant->appendChild($attributes);

        $item->appendChild($variant);

        return $item;
    }

    protected function getProductXml(DOMDocumentExtended $domDoc, $rootElement, \Web2PrintBlackbit\Product $object){
        $item = $domDoc->createElement('item');
        $this->applyProductAndVariantValues($domDoc,$rootElement,$object,$item);



        /**
         * @var \Web2PrintBlackbit\Product $child
         */
      /*  foreach($object->getChildren() as $child){
            $variant = $domDoc->createElement('variant');
            $variant->appendChild($domDoc->createElement('variant_standard','false'));
            $variant->appendChild($domDoc->createElement('vendor_item_number',$child->getArt_no()));
            $variant->appendChild($domDoc->createElement('ean_code',$child->getEan()));
            $variant->appendChild($domDoc->createElement('identifier',$child->getTitle_ecom_6()));

            $item->appendChild($variant);
        }*/



        return $item;
    }

    public function updateCategories($ids = [])
    {

        $monitoringItem = \ProcessManager\Plugin::getMonitoringItem();
        $monitoringItem->setTotalSteps(2)->save();
        $domDoc = new DOMDocumentExtended();
        $domDoc->preserveWhiteSpace = false;
        $domDoc->formatOutput = true;


        $request = $domDoc->createElement('request');
        $request->setAttribute('version', '1.0.0');
        $request->setAttribute('method', 'updateCategories');

        $monitoringItem->setCurrentWorkload(0)->setTotalWorkload(count($ids))->save();
        $monitoringItem->setMessage('Processing categories')->save();
        foreach ($ids as $i => $id) {
            $monitoringItem->getLogger()->debug('Processing Item ' . $id);

            $o = \Web2PrintBlackbit\Product::getById($id);
            $this->getCategoryXML($domDoc, $request, $o);

            if($i % 100 == 0){
                \Pimcore::collectGarbage();
                $monitoringItem->setCurrentWorkload($i)->save();
            }
        }
        $monitoringItem->setWorloadCompleted();

        $domDoc->appendChild($request);

        $xml = $domDoc->saveXML();
        $result = $this->client->call('updateCategories',$xml);

        $monitoringItem->setCurrentStep(2)->setCurrentWorkload(0)->setMessage('Processing response')->setTotalWorkload(count($ids))->save();
        $i = 0;
        foreach($result->report as $entry){
            $i++;
            $returnCode = (int)$entry->attributes()->return_code;
            $id = (int)$entry->category->external_id;

            if($returnCode == 0){
                $speed4TradeId = (int)$entry->category->category_id;
                $monitoringItem->getLogger()->debug('Processing result - CategoryID :' .$id);

                /**
                 * @var \Web2PrintBlackbit\Product $product
                 */
                $product = \Web2PrintBlackbit\Product::getById($id);
                if($product->getSpeed4TradeId() != $speed4TradeId){
                    $product->setSpeed4TradeId($speed4TradeId)->save();
                }
            }else{
                $monitoringItem->getLogger()->emergency('Category Update failed:' .$id .' Data: ' . print_r($entry,true));
            }
            if($i % 100 == 0){
                $monitoringItem->setCurrentWorkload($i);
                $monitoringItem->setMessage('Processing Response. Current count:' . $i)->save();
                \Pimcore::collectGarbage();
            }
        }
        $monitoringItem->setWorloadCompleted()->save();
        return $result;

    }

    protected function getCategoryXML($domDoc, $rootElement, \Web2PrintBlackbit\Product $object)
    {
        if ($object->getProductType() == 'virtual') {


            $category = $domDoc->createElement('category');
            $category->setAttribute('significant_key', 'external_id');

            $category->appendChild($domDoc->createElement('mandator_id', self::MANDATOR_ID));
            $category->appendChild($domDoc->createElement('identifier', $object->getKey()));

            $category->appendChild($domDoc->createElement('external_id', $object->getId()));

            if ($object->getParent() instanceof \Web2PrintBlackbit\Product) {
                $category->appendChild($domDoc->createElement('external_parent_id', $object->getParent()->getId()));
            }

            $rootElement->appendChild($category);
            return $domDoc;
        }
    }

}