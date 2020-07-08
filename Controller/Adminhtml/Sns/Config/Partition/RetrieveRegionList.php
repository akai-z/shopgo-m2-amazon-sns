<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Controller\Adminhtml\Sns\Config\Partition;

class RetrieveRegionList extends \ShopGo\Aws\Controller\Adminhtml\Config\Partition\RetrieveRegionList
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ShopGo_AmazonSns::config_amazon_sns');
    }
}
