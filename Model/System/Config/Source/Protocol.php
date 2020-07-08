<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Source model for SNS protocols
 */
class Protocol implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'http', 'label' => 'HTTP'],
            ['value' => 'https', 'label' => 'HTTPS']
        ];
    }
}
