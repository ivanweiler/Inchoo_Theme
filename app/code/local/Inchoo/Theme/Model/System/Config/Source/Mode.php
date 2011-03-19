<?php
class Inchoo_Theme_Model_System_Config_Source_Mode extends Varien_Object
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('adminhtml')->__('Enable All'),
            ),
            array(
                'value' => 0,
                'label' => Mage::helper('adminhtml')->__('Disable All'),
            )
        );
    }
}