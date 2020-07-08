<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Model;

/**
 * Topic model
 */
class Topic extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Topic subscription endpoint types
     */
    const ENDPOINT_TYPE_INTERNAL = 1;
    const ENDPOINT_TYPE_EXTERNAL = 2;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ShopGo\AmazonSns\Model\ResourceModel\Topic');
    }
}
