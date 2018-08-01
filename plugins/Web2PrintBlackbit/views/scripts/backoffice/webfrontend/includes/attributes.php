<?
$key = $this->key;
$i = $this->i;
if($this->elements){
    $data = $this->elements;
}else{
    $data = $this->tabArray[$key];

}
/**
 * @var \Pimcore\Model\Object\ClassDefinition\Data $element
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;

$key = str_replace(' ','',$key);
if($data){?>

    <?php
    $trs = '';
    foreach($data->getChilds() as $element){
        $trs .= $this->template('backoffice/webfrontend/includes/tr.php',['product' => $product,'element' => $element],true,'placeholder_'.$key);

    }


    $additionalTables = [];
    if($key == 'ProduktDetails'){
        foreach($product->getAttributes()->getBrickGetters() as $getter){
            $brick = $product->getAttributes()->$getter();
            if(!$brick){
                continue;
            }
            /**
             * @var \Pimcore\Model\Object\Objectbrick\Definition $def
             */
            $def = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($brick->getType());

            $fields = $def->getLayoutDefinitions()->getChildren()[0]->getChildren();

            $trs2 = '';
            foreach($fields as $element){
                if($element instanceof \Pimcore\Model\Object\ClassDefinition\Data\Block){
                    foreach($brick->getHolz() as $entry){
                        $country = $entry['originCountry']->getData();
                        $woodType = $entry['woodType']->getData();

                        if($woodType instanceof \Pimcore\Model\Object\AttributeWoodType){
                            $woodType = $woodType->getName();
                        }
                        $country = \Zend_Locale::getTranslation($country, 'territory');

                        if($woodType || $country){
                            if(!$trs2){
                                $trs2 .= '<tr><th>'.$this->t('backoffice_label_wood').'</th><th>'.$this->t('backoffice_label_country').'</th>';
                            }
                            $trs2 .= '<tr><td>'.$woodType.'</td><td>'.$country.'</td>';
                        }
                    }
                }else{
                    $trs2 .= $this->template('backoffice/webfrontend/includes/tr.php',['product' => $brick,'element' => $element],true,'placeholder_'.$key);
                }
            }

            if($trs2){
                $additionalTables[$brick->getType()] = '<table class="table table-striped"><tbody>' . $trs2.'</tbody></table>';
            }
        }

            $materials = $product->getMaterials_articlepass();
            if($materials){
                foreach($materials as $entry){
                    $name = $entry['name']->getData();
                    $value = $entry['description']->getData();
                    if($value){
                        $additionalTables['materials'][] = '<tr><td>'.$name.'</td><td>'.$value.'</td></tr>';
                    }
                }
            }

            if($additionalTables['materials']){
                $additionalTables['materials'] = '<table class="table table-striped"><tbody>' . implode('',$additionalTables['materials']).'</tbody></table>';
            }
            $ingredients = $product->getInhaltsstoffe();
            if($ingredients){
                foreach($ingredients as $entry){
                    $data = $entry['Ingredients']->getData();
                    if($data){
                        $name = $data->getTitle();
                        $value = $entry['percent']->getData();
                        if($value){
                            $additionalTables['ingredients'][] = '<tr><td>'.$name.'</td><td>'.$value.'%</td></tr>';
                        }
                    }
                }
            }
        if($additionalTables['ingredients']){
            $additionalTables['ingredients'] = '<table class="table table-striped"><tbody>' . implode('',$additionalTables['ingredients']).'</tbody></table>';
        }

    }
    if($trs || $additionalTables){
    ?>


    <div class="panel panel-default">
        <div class="panel-heading detail-expand" role="tab" id="heading<?=$key?>">
            <h4 class="panel-title">
                <a class="<?if($i > 0 ){?> collapsed <?}?>" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>" aria-controls="collapse<?=$key?>">
                    <?=$this->t('backoffice_label_'.$key)?>
                </a>
            </h4>
        </div>
        <div id="collapse<?=$key?>" class="panel-collapse collapse <?if($i == 0 ){?>  in <?}?>" role="tabpanel" aria-labelledby="heading<?=$key?>">
        <div class="panel-body">

            <? if($this->headline && $trs){?>
                <h4><?=$this->t('backoffice_label_'.$this->headline)?></h4>
            <?}?>

            <? if($trs){?>
                <table class="table table-striped">
                    <tbody>
                        <?=$trs?>
                    </tbody>
                </table>
            <?}?>

            <? if($additionalTables){?>

                <div class="row">
                    <?foreach($additionalTables as $k => $table){ ?>
                        <div class="col-xs-6">
                            <h4><?=$this->t('backoffice_label_'.$k)?></h4>
                            <?=$table?>
                        </div>
                    <? } ?>

                </div>

                <?
            }
            ?>


        </div>
    </div>
    </div>

<? }
} ?>