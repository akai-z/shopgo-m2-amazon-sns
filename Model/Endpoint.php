<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ShopGo\AmazonSns\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Webapi\Request;

class Endpoint implements \ShopGo\AmazonSns\Api\EndpointInterface
{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var Sns
     */
    protected $_sns;

    /**
     * @param Request $request
     * @param Sns $sns
     */
    public function __construct(Request $request, Sns $sns)
    {
        $this->_request = $request;
        $this->_sns = $sns;
    }

    /**
     * {@inheritdoc}
     */
    public function listen()
    {
        $message = $this->_request->getContent();
        $this->_sns->processMessage($message);

        return 1;
    }
}
