<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Admin source yes/no/default model
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Model_Adminhtml_Source_Yesnodefault
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    const YES = 1;
    const NO = 0;
    const USE_DEFAULT = 2;
    /**
     * get possible values
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function toOptionArray(){
        return array(
            array(
                'label' => Mage::helper('ultimate_events')->__('Use default config'),
                'value' => self::USE_DEFAULT
            ),
            array(
                'label' => Mage::helper('ultimate_events')->__('Yes'),
                'value' => self::YES
            ),
            array(
                'label' => Mage::helper('ultimate_events')->__('No'),
                'value' => self::NO
            )
        );
    }
    /**
     * Get list of all available values
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllOptions() {
        return $this->toOptionArray();
    }
}