<?php

namespace Pimcore\Model\Document\Tag;

use Elements\OutputDataConfigToolkit\OutputDefinition;
use \Pimcore\Model\Document;

class Windhageroutputchanneltable extends Document\Tag\Outputchanneltable
{

    public $selectedClass = 'Product';

    public function getOutputChannel()
    {
        $outputDefinition = parent::getOutputChannel();

        if ($channel = $this->selectedFavouriteOutputChannel) {
            /**
             * @var \Elements\OutputDataConfigToolkit\OutputDefinition $data
             */

            $list = new \Web2Print\FavoriteOutputDefinition\Listing();
            $list->setLimit(1)->setCondition('description = ' . \Pimcore\Db::get()->quote($channel));
            $predefinedConfig = $list->load()[0];
            if ($predefinedConfig) {
                $outputDefinition->setConfiguration($predefinedConfig->getConfiguration());
            }
        }
        return $outputDefinition;
    }

    public function getType()
    {
        return "windhageroutputchanneltable";
    }
}