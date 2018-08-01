<?php

namespace Web2PrintBlackbit\OnlineShop\Pricing\Action;

use OnlineShop\Framework\PricingManager\IAction;
use OnlineShop\Framework\PricingManager\IEnvironment;

class Badge implements IAction {
    protected $badge;

    /**
     * @return \Pimcore\Model\Object\Badge
     */
    public function getBadge() {
        return $this->badge;
    }

    /**
     * @param \Pimcore\Model\Object\Badge $badge
     * @return $this
     */
    public function setBadge(\Pimcore\Model\Object\Badge $badge) {
        $this->badge = $badge;
        return $this;
    }

    /**
     * @param IEnvironment $environment
     *
     * @return IAction
     */
    public function executeOnProduct(IEnvironment $environment)
    {
        // TODO: Implement executeOnProduct() method.
    }

    /**
     * @param IEnvironment $environment
     *
     * @return IAction
     */
    public function executeOnCart(IEnvironment $environment)
    {
        // TODO: Implement executeOnCart() method.
    }

    /**
     * @return string
     */
    public function toJSON()
    {
        return json_encode([
            "type" => "Badge",
            "badge" => $this->getBadge() ? $this->getBadge()->getFullPath() : null
        ]);
    }

    /**
     * @param string $string
     *
     * @return IAction
     */
    public function fromJSON($string)
    {
        $json = json_decode($string);
        $badge = \Pimcore\Model\Object\Badge::getByPath($json->badge);

        if($badge) {
            $this->setBadge($badge);
        }

        return $this;
    }

    /**
     * dont cache the entire badge object
     * @return array
     */
    public function __sleep()
    {
        if($this->badge)
            $this->badge = $this->badge->getFullPath();

        return array('badge');
    }

    /**
     * restore badge
     */
    public function __wakeup()
    {
        if($this->badge != '')
            $this->badge = \Pimcore\Model\Object\Badge::getByPath($this->badge);

    }
}