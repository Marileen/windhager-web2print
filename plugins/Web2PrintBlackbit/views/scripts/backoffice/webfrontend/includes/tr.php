<?
/**
 * @var \Pimcore\Model\Object\ClassDefinition\Data $element
 * @var \Web2PrintBlackbit\Product $product
 */
$product = $this->product;
$element = $this->element;

$dispalyData = \Web2PrintBlackbit\Helper\Webfrontend::getDisplayData($product,$element,$this);

if($dispalyData['value'] instanceof \Pimcore\Model\Object\Objectbrick){
    $brick = $dispalyData['value'];

    if($brick instanceof \Pimcore\Model\Object\Product\PmtMarketingAttributes){
        foreach($brick->getBrickGetters() as $getter){
            $brick = $product->getPmtMarketingAttributes()->$getter();
            if(!$brick){
                continue;
            }

            /**
             * @var \Pimcore\Model\Object\Objectbrick\Definition $def
             */
            $def = \Pimcore\Model\Object\Objectbrick\Definition::getByKey($brick->getType());

            $fields = $def->getLayoutDefinitions()->getChildren()[0]->getChildren();
            $name = $fields[0]->getTitle();
            $getter = 'get' . ucfirst($fields[0]->getName());
            if(count($fields) == 2){
                $getter2 = 'get' . ucfirst($fields[0]->getName()).'_unit';
                $value = $brick->$getter();
                if(method_exists($brick,$getter2)){
                    $unit = $brick->$getter2();
                    if($unit instanceof \Web2PrintBlackbit\AttributeUnit){
                        $value .= ' ' . $unit->getTitle();
                    }
                }
            } else {
                $value = $brick->$getter();
            }

            if($value){?>
                <tr>
                    <td><?=$name?></td>
                    <td>
                        <?=$value?>
                    </td>
                </tr>
            <?}
        }
    }
    ?>
<?}elseif(!is_null($dispalyData['value']) && $dispalyData['value'] != ''){ ?>
    <tr>
        <td><?=$dispalyData['name']?></td>
        <td>
            <? if($dispalyData['value'] instanceof \Pimcore\Model\Object\Data\Video){
                $tag = new \Pimcore\Model\Document\Tag\Video();
                $tag->setName('montagevideo_' . rand());
                $tag->setOptions(['width' => 350,'height' => 280]);
                $tag->setId($dispalyData['value']->getData()->getId());
                echo $tag;
            }else{
               echo $dispalyData['value'];
            }
            ?>
        </td>
    </tr>
<? } ?>