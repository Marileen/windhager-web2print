<?php 
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object|Class
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Model\Object\ClassDefinition\Layout;

use Pimcore\Model;

class ExtendedPanel extends Model\Object\ClassDefinition\Layout
{

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "extendedPanel";


    /**
     * Width of input field labels
     * @var int
     */
    public $labelWidth = 100;

    /**
     * @var string
     */
    public $layout;

    /**
     * @var int
     */
    public $role;

    /**
     * @var bool
     */
    public $mandatory;

    /**
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @param $labelWidth
     * @return $this
     */
    public function setLabelWidth($labelWidth)
    {
        if (!empty($labelWidth)) {
            $this->labelWidth = intval($labelWidth);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getLabelWidth()
    {
        return $this->labelWidth;
    }

    /**
     * @param $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param boolean $mandatory
     * @return $this
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = (bool)$mandatory;
        return $this;
    }

}
