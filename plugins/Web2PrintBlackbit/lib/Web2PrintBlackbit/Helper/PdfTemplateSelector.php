<?php
/**
 * Created by PhpStorm.
 * User: mmoser
 * Date: 2017-03-27
 * Time: 15:59
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object\AbstractObject;
use Pimcore\Model\Object\PdfTemplate;
use Pimcore\Model\Object;
use Pimcore\View;
use Web2PrintBlackbit\Product;

class PdfTemplateSelector {

    public static function embedTemplateForLDL(Product $product, View $view) {
       self::embedTemplateHelper($product, $view, 'pdfTemplateLDL');
    }
    public static function embedTemplateForDatasheet(Product $product, View $view) {
        self::embedTemplateHelper($product, $view, 'pdfTemplateDatasheet');
    }


    /**
     * @param Product $product
     * @return PdfTemplate
     */
    public static function getPDFTemplateLDL (Product $product) {
        $template = self::getPDfTemplateHelper($product, 'pdfTemplateLDL');

        return $template;
    }

    /**
     * @param Product $product
     * @return PdfTemplate
     */
    public static function getPDFTemplateDatasheet (Product $product) {
        return self::getPDfTemplateHelper($product, 'pdfTemplateDatasheet');
    }

    /**
     * @param Product $product
     * @string $type
     * @return PdfTemplate
     */
    private static function getPDfTemplateHelper(Product $product, $type) {
        $backup = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(true);

        $getter = 'get' . ucfirst($type);

        $pdfTemplate = $product->$getter();

        if($type == 'pdfTemplateLDL'){
            //iv.	Anhand des Markenobjekts muss automatisch das passende LDL ausgewählt werden, ist keine Marke ausgewählt wird automatisch das Windhager/Venilia LDL herangezogen. Eingabe bei PUR dient nur zum overrulen
            if(!$pdfTemplate){
                if($brand = $product->getBrand()){
                    $list = new Object\PdfTemplate\Listing();
                    $pdfTemplate = $list->setCondition('brand__id=?',$brand->getId())->setLimit(1)->load()[0];
                }
            }
            if(!$pdfTemplate){
                $pdfTemplate = \Pimcore\Config::getWebsiteConfig()->toArray()['defaultLdlPDFTemplate'];
            }
        }elseif($type == 'pdfTemplateDatasheet'){
            if(!$pdfTemplate){

                if($product->getDisplaySet() && $product->getProductType() == 'product'){
                    $key = 'defaultDataSheetTemplateDisplaySet';
                }else{
                    $key = 'defaultDataSheetTemplate';
                }
                //defaultDataSheetTemplateDisplaySet
                $pdfTemplate = \Pimcore\Config::getWebsiteConfig()->toArray()[$key];
            }

        }


        AbstractObject::setGetInheritedValues($backup);

        return $pdfTemplate;
    }

    public static function embedTemplateHelper(Product $product, View $view, $type) {
        $backup = AbstractObject::getGetInheritedValues();
        AbstractObject::setGetInheritedValues(true);
        if($pdfTemplate = self::getPDfTemplateHelper($product, $type)) {

            if($template = $pdfTemplate->getCustomHtmlTemplate()) {

                $parent = realpath(PIMCORE_DOCUMENT_ROOT . '/../custom-pdf-templates/');
                $path = realpath($parent . '/scripts/' . $template);

                if(file_exists($path)) {
                    $view->addBasePath($parent);
                    $view->template($template);
                } else {
                    print "template $parent/scripts/$template not found";
                }

            } elseif($template = $pdfTemplate->getTemplate()) {
                 $view->template("export/print/product/" . $template . '.php');
            }

        } else {
            print "no template type defined for {$type}";
        }

        AbstractObject::setGetInheritedValues($backup);
    }
}