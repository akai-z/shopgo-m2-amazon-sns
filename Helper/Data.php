<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Helper;

class Data extends \ShopGo\Aws\Helper\AbstractHelper
{
    /**
     * XML path AWS general partition
     */
    const XML_PATH_AWS_GENERAL_PARTITION = 'amazon_sns/general/partition';

    /**
     * XML path AWS general region
     */
    const XML_PATH_AWS_GENERAL_REGION = 'amazon_sns/general/region';

    /**
     * Log module directory path
     */
    const LOG_MODULE_PATH = 'aws/sns/';
}
