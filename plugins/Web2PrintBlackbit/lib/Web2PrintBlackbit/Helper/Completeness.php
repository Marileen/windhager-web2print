<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 03.01.2017
 * Time: 15:44
 */

namespace Web2PrintBlackbit\Helper;

use Pimcore\Model\Object;

class Completeness {


    public static function  renderLayoutText($channel, $object, $params) {
        $data = [];
        $fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();

        foreach($object->getMissingFields() as $e){
            if($e['channel'] == $channel){
                if(!$e['locale']){
                    $data[] = $fieldMatrix['fieldTitles'][$e['fieldName']];
                }else{
                    $data[] = $fieldMatrix['fieldTitles'][$e['fieldName']] .' (' . $e['locale'].')';
                }
            }
        }
        return '<div style="padding:10px;">'. (implode('<br/>',$data) ?: 'All fields ready').'</div>';
    }

    public static function calculateMissingFieldInfo($product){
        $fieldMatrix = \Web2PrintBlackbit\Helper\ProductLayout::getFieldMatrix();
        $missingFields = $product->getMissingFields();

        $getMissingFields = function($sourceFields,$format) use ($missingFields,$fieldMatrix) {
            $missing = [];
            if($sourceFields){
                foreach($sourceFields as $roleField){
                    foreach($missingFields as $e){
                        if($e['fieldName'] == $roleField){
                            $niceName = $fieldMatrix['fieldTitles'][$e['fieldName']];
                            if($format == 'html'){
                                if($l = $e['locale']){
                                    $missing[$e['fieldName']][] = $e['locale'];
                                }else{
                                    $missing[$e['fieldName']][] = '';
                                }
                            }else{
                                $missing[] = $e['fieldName'];
                            }
                        }
                    }
                }
            }
            foreach($missing as $key => $v){
                $v = array_filter($v);
                $niceName = $fieldMatrix['fieldTitles'][$key];
                $missing[$key] = $niceName;
                if($v){
                    $missing[$key] .= ' <span class="glyphicon glyphicon-info-sign" title="'.implode(', ',$v).'"></span>';
                    #$missing[$key] .= ' (' . implode(', ',$v).')';
                }
            }
            sort($missing);
            $missing = array_unique($missing);
            return $missing;
        };

        $result = [
            'roleMissing' => [],
            'missing' => []
        ];
        foreach($fieldMatrix['channelRoleIds'] as $roleId){
            $roleFields = $fieldMatrix['roleFields'][$roleId];
            $tmp = $getMissingFields($roleFields,'html');
            if($tmp){
                $result['roleMissing'][$roleId] = $tmp;
            }
        }
        $result['responsibleRoles'] = [];
        foreach(array_keys($result['roleMissing']) as $rId){
            $result['responsibleRoles'][] = $fieldMatrix['userRoles'][$rId];
            $result['missing'] = array_merge($result['missing'],(array)$result['roleMissing'][$rId]);
        }
        $result['missing'] = array_unique($result['missing']);

        $result['channelMissing'] = [];

        $channelConfig= include \Pimcore\Config::locateConfigFile("channels.php");

        foreach($channelConfig['channels'] as $key => $channelEntry){
            $channelMissing = [];
            $channelFields = $fieldMatrix['channelFields'][$key]['mandatory'];
            foreach($missingFields as $e){
                if(in_array($e['fieldName'],(array)$channelFields)){
                    if($l = $e['locale']){
                        $channelMissing[$e['fieldName']][] = $e['locale'];
                    }else{
                        $channelMissing[$e['fieldName']][] = '';
                    }

                }
            }

            foreach($channelMissing as $k => $v){
                $niceName = $fieldMatrix['fieldTitles'][$k];
                $channelMissing[$k] = $niceName;
                if($x = implode(', ',array_filter($v))){
                    $channelMissing[$k] .= ' (' . $x .')';
                }
            }
            sort($channelMissing);
            $result['channelMissing'][$key] = $channelMissing;
        }
        #p_r($result); exit;

        sort($result['missing']);
        sort($result['responsibleRoles']);


        return $result;
    }


}