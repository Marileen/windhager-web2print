<?php
/**
 * Created by PhpStorm.
 * User: ckogler
 * Date: 18.05.2016
 * Time: 13:04
 */
namespace Web2PrintBlackbit\Events;


class Mail {
    public static function setTransport(\Zend_EventManager_Event $e){
        /**
         * @var \Pimcore\Mail $mail
         */
        $mail = $e->getTarget();
        if(\Pimcore\Config::getEnvironment() != 'development'){

            if(strpos($mail->getFrom(),'@venilia.com') || strpos($mail->getFrom(),'@windhager.eu' || $mail->getFrom() == '')){ //empty <- fallback e-mail is noreply@windhager.eu in system settings
                $eventContainer = $e->getParams();
                $data = $eventContainer->getData();
                $data['transport'] = new \Zend_Mail_Transport_Smtp("mail.windhager.eu");
                $eventContainer->setData($data);
            }
        }
    }
}