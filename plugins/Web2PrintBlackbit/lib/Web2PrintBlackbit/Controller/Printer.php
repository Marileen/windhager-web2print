<?php
/**
 * Created by PhpStorm.
 * User: jraab
 * Date: 10.03.2017
 * Time: 11:24
 */

namespace Web2PrintBlackbit\Controller;


use Pimcore\Controller\Action;
use Pimcore\Tool\Authentication;
use Web2PrintBlackbit\Exporter\Product\Exports\AbstractExport;

class Printer extends Action\Frontend
{
    public function init()
    {
        parent::init();
        $this->view->addHelperPath(PIMCORE_WEBSITE_PATH . "/lib/Website/View/Helper","Website\\View\\Helper\\");
        if(php_sapi_name() != 'cli' && !in_array($this->getParam('action'), ['catalogue'])) {

            $user = Authentication::authenticateSession();

            if (!$user || !$user->isAllowed("plugin_pm_permission_execute")) {
                if ($this->getParam("render_key") != AbstractExport::RENDER_KEY) {
                    throw new \Exception("The current user does not have the permission to render this document, or invalid render key provided.");
                }
            }
        }

    }

    public function pdfResponse($filename, $return = false)
    {
        $this->view->pdf = true;

        /* render pdf or html */
        if ($return) {
            ob_start();
        }

        $front = \Zend_Controller_Front::getInstance();
        if(!$front->getPlugins()[777]){
            $front->registerPlugin(new \Web2PrintBlackbit\Helper\ReactorPDF(null, $filename), 777);
        }

        if ($return) {
            return ob_get_clean();
        }
    }

}