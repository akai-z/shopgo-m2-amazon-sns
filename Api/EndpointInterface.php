<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Api;

/**
 * Amazon SNS endpoint
 * @api
 */
interface EndpointInterface
{
    /**
     * Listen to endpoint requests
     *
     * @return int
     */
    public function listen();
}
