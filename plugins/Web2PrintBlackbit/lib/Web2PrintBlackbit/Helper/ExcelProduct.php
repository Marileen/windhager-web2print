<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.05.2017
 * Time: 13:21
 */
namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;
use Pimcore\Model\Document;

class ExcelProduct {

    /**
     * @var null | \Web2PrintBlackbit\Product
     */
    protected $product = null;
    protected $config = [];

    public function __construct()
    {
        Document::setHideUnpublished(true);
        Object\AbstractObject::setHideUnpublished(true);
        Object\AbstractObject::setGetInheritedValues(true);
        Object\Localizedfield::setGetFallbackValues(true);
        \Web2PrintBlackbit\Exporter\Product\Exports\ProductDatasheet::setPortalConfig();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Web2PrintBlackbit\Product $product
     * @return $this
     */
    public function setProduct(\Web2PrintBlackbit\Product $product)
    {
        $this->product = $product;
        return $this;
    }

    protected function t($key){
        if(!$this->translator){
            $this->translator = new \Pimcore\Translate\Website($this->getConfig()['locale']);
        }

        return $this->translator->translate($key);
    }


    protected function getTextFromHtml($html){
        if($html){
            return \Html2Text\Html2Text::convert($html);
        }
        return $html;
    }

    protected function getExcel(){

        $colorPrimary = 'AABA58';
        $abc = range('A','Z');


        $borderStyle = [];
        $borderStyle['borders']['top'] = [
            'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
            'color' => ['rgb' => $colorPrimary]
        ];


        /**
         * @var \Web2PrintBlackbit\Product $product
         */
        $product = $this->getProduct();
        $displayData = \Web2PrintBlackbit\Webservice\ProductDisplay::getDisplayContents(
            $product,
            $this->getConfig()["pricelist"],
            $this->getConfig()["year"],
            $this->getConfig()["mandant"],
            $this->getConfig()["region"],
            explode(",", $this->getConfig()["includeFields"]),
            true
        );

        $data = [
            'category' =>  'Verkaufshilfen und Konzepte',
        ];

        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()
            ->setShowGridlines(true);

        $margin = 0.5;
        $objPHPExcel->getActiveSheet()
            ->getPageMargins()->setTop($margin)->setBottom($margin)->setLeft($margin)->setRight(0);
      /*  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToPage(true);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);*/

        foreach(range('A','Z') as $char) {
            $sheet->getColumnDimension($char)->setWidth(10);
        }
        $sheet->getColumnDimension('K')->setWidth(6);
        $sheet->getColumnDimension('N')->setWidth(3);
        #$sheet->getRowDimension('2')->setRowHeight(40);


        foreach($abc as  $char){
            $sheet->getStyle($char.'1')->applyFromArray($borderStyle);
            if($char == 'J'){
                break;
            }
        }


        if($mainImage = $product->getMainImage()){
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($mainImage->getThumbnail('export-excel-main-image',false)->getFileSystemPath(false));
            $objDrawing->setCoordinates('A2');
            $objDrawing->setWorksheet($sheet);
        }

        $sheet->mergeCells("F2:I2");
        $sheet->setCellValue('F2',strtoupper('PLATZHALTER CATEGORY'));
        $sheet->getStyle('F2')->applyFromArray([
            'font'  => [
                'bold'  => true,
                'color' => ['rgb' => $colorPrimary],
                'size'  => 13,
                'name'      => 'Arial',
            ]
        ]);


     #   $sheet->mergeCells("D4:I10");
        $sheet->mergeCells("F4:J4");
        $sheet->setCellValue('F4',$product->getTitle())->getStyle('F4')->applyFromArray([
            'font'  => [
                'bold'  => true,
                'size'  => 17,
                'name'      => 'Arial',
            ]
        ])->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
        $rowsNeeded = ceil(strlen($product->getTitle())/35);
        $sheet->getRowDimension(4)->setRowHeight($rowsNeeded*30);

        $sheet->mergeCells("F5:J5");

        $text = $this->getTextFromHtml($product->getText());
        $rowsNeeded2 = count(explode("\n",$text));
        $sheet->getCell('F5')->setValueExplicit($text,\PHPExcel_Cell_DataType::TYPE_STRING)->getStyle()->getAlignment()->setWrapText(true)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
        $sheet->getRowDimension(5)->setRowHeight($rowsNeeded2*15);

        $totalRows = ($rowsNeeded*2)+$rowsNeeded;

        $images = [];
        foreach(['crop','packing','material'] as $type){
            $image = $product->getAllImages($type, $product, true);
            if(is_array($image)){
                foreach($image as $img){
                    $images[] = $img->getThumbnail('export-excel-small',false)->getFileSystemPath(false);
                }
            }else{
                if($image){
                    $images[] = $image->getThumbnail('export-excel-small',false)->getFileSystemPath(false);
                }
            }

        }

        $currentRow = $totalRows-3;
        if($currentRow < 6){
            $currentRow = 6;
        }

        $images = array_chunk($images,5);
        foreach($images as $x => $rowImages){
            $currentRow++;

            for($i = 0; $i < count($rowImages); $i++){
                $objDrawing = new \PHPExcel_Worksheet_Drawing();
                $objDrawing->setPath($rowImages[$i]);
                if($i == 0){
                    $charIndex = 'A'.$currentRow;
                }elseif($i == 1){
                    $charIndex = 'C'.$currentRow;
                }elseif($i == 2){
                    $charIndex = 'E'.$currentRow;
                }elseif($i == 3){
                    $charIndex = 'G'.$currentRow;
                }elseif($i == 4){
                    $charIndex = 'I'.$currentRow;
                }elseif($i == 5){
                    $charIndex = 'K'.$currentRow;
                }else{
                    throw new \Exception('Not supported');
                }
                $objDrawing->setCoordinates($charIndex);
                $objDrawing->setWorksheet($sheet);

            }
            $sheet->getRowDimension($currentRow)->setRowHeight(65);
        }



        $articles = $displayData['articles'];

        $currentRow += 2;
        $rowHeight = 25;
        $sheet->getRowDimension($currentRow)->setRowHeight($rowHeight);
        if($articles){
            $head = array_keys($articles[0]);
            $i = 0;
            foreach ($head as $th) {
                $val = $this->t("datasheet." . $th);
                $colIndex = $abc[$i].$currentRow;
                $sheet->setCellValue($colIndex,$val);
                $sheet->getStyle($colIndex)->applyFromArray([
                    'font'  => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 10,
                        'name'      => 'Arial',
                    ],
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => $colorPrimary)
                    ),
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                ]);

                $i++;
                if($th == 'title'){
                    $i++;
                    $sheet->mergeCells($colIndex.':'.$abc[$i].$currentRow);
                }
            }
        }

        $currentRow++;

        $tmpDir = \PIMCORE_WEBSITE_VAR.'/barcode/';
        \Pimcore\File::mkdir($tmpDir);

        foreach ($articles as $x => $articleRow) {
            $i=0;
            foreach ($articleRow as $param => $value) {
                $colIndex = $abc[$i].$currentRow;

                if($param == 'ean'){
                    if($value){
                        $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
                        $code = $generator->getBarcode($value, $generator::TYPE_EAN_13,1,20);
                        $file = $tmpDir . $value .'.jpeg';
                        file_put_contents($file,$code);
/*
                        $asset = new \Pimcore\Model\Asset\Image();
                        $asset->setMimetype('image/jpeg');
                        $asset->setFilename('asdfasdf.jpeg');
                        $asset->setData($code);

                        var_dump($asset->getThumbnail('export-excel-small',false)->getFileSystemPath(false)); Exit;*/
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setName($value);
                        $objDrawing->setCoordinates($colIndex);
                        $objDrawing->setPath($file);
                        $objDrawing->setWidth(70);
                        $objDrawing->setWorksheet($sheet);
                    }
                }else{
                    $sheet->setCellValue($colIndex,$value);
                }

                $style = [
                    'font'  => [
                        'size'  => 10,
                        'name'      => 'Arial',
                    ],
                    'fill' => [
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => ['rgb' => 'f7f7f7']

                    ],
                    'alignment' => [
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ]
                ];
            /*    if(!$articles[$x+1]){
                    $style['borders']['bottom'] = [
                            'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '808080']
                    ];
                }*/
                $sheet->getStyle($colIndex)->applyFromArray($style);
                $i++;
                if($param == 'title'){
                    $i++;
                    $sheet->mergeCells($colIndex.':'.$abc[$i].$currentRow);
                    $style['alignment']['horizontal'] = \PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                    $sheet->getStyle($colIndex)->applyFromArray($style);
                }
            }

            $sheet->getRowDimension($currentRow)->setRowHeight($rowHeight);
            $currentRow++;
        }

        //need to put the border on the next row on top because it doesn't work with the merged cells
        for($a = 0; $a < $i;$a++){
            $colIndex = $abc[$a].$currentRow;
            $colIndexHead = $abc[$a].'1';
            $sheet->getStyle($colIndex)->applyFromArray($borderStyle);
        }





        $logo = $this->getConfig()['logo'];
        if($logo instanceof \Pimcore\Model\Asset\Image){
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $objDrawing->setPath($logo->getThumbnail('export-excel-logo',false)->getFileSystemPath(false));
            $objDrawing->setCoordinates('L1');
            #$objDrawing->setHeight(75); // logo height
            $objDrawing->setWorksheet($sheet);
        }

        $template = \Web2PrintBlackbit\Helper\PdfTemplateSelector::getPDFTemplateDatasheet($product);



        $sideImage = $template->getBanner();
        if($sideImage instanceof \Pimcore\Model\Asset\Image){
            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setPath($sideImage->getThumbnail('export-excel-sidebar',false)->getFileSystemPath(false));
            $objDrawing->setCoordinates('L4');
            $objDrawing->setWorksheet($sheet);
        }

        return $objPHPExcel;
    }

    public function sendAsDownload(){

        $objPHPExcel = $this->getExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $targetFile = \PIMCORE_DOCUMENT_ROOT.'/test5.xlsx';
        @unlink($targetFile);
        # $objWriter->save($targetFile);

        //http://proinsect.windhager.dev.elements.pm/test2.xlsx

        $objWriter->save($targetFile);
          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment;filename="hallo.xlsx"');
          header('Cache-Control: max-age=0');
          header('Cache-Control: max-age=1');
        $objWriter->save('php://output');
        exit;
    }


}