<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 12.05.2017
 * Time: 15:12
 */

namespace Web2PrintBlackbit\OutputDataConfigToolkit;

use Elements\OutputDataConfigToolkit;

class Service extends OutputDataConfigToolkit\Service {

    public static function buildOutputDataConfigForCatalog($outputDataConfig, $context = null){
        $config = parent::buildOutputDataConfig($outputDataConfig, $context);
        array_unshift($config,new \Web2PrintBlackbit\OutputDataConfigToolkit\ConfigElement\Value\Ean((object)['attribute' => 'ean','label' => 'ean']));
        array_push($config,new \Elements\OutputDataConfigToolkit\ConfigElement\Value\Numeric((object)['attribute' => 'vke','label' => 've']));
        //contentsVPE
        return $config;
    }
}