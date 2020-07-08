<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Ui\Component\Listing\Column\EndpointType;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \ShopGo\AmazonSns\Model\Topic::ENDPOINT_TYPE_INTERNAL,
                'label' => __('Internal')
            ],
            [
                'value' => \ShopGo\AmazonSns\Model\Topic::ENDPOINT_TYPE_EXTERNAL,
                'label' => __('External')
            ]
        ];
    }
}
