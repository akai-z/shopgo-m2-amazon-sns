<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Block\Adminhtml;

/**
 * Adminhtml SNS topics content block
 */
class Topic extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'ShopGo_AmazonSns';
        $this->_controller = 'adminhtml_topic';
        $this->_headerText = __('SNS Topics');
        $this->_addButtonLabel = __('Add New Topic');
        parent::_construct();
    }
}
