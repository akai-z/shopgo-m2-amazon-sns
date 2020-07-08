<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Controller\Sns;

use Magento\Framework\App\Action;

class Endpoint extends Action\Action
{
    /**
     * @var \ShopGo\AmazonSns\Model\Sns
     */
    protected $_sns;

    /**
     * @param Action\Context $context
     * @param \ShopGo\AmazonSns\Model\Sns $sns
     */
    public function __construct(
        Action\Context $context,
        \ShopGo\AmazonSns\Model\Sns $sns
    ) {
        parent::__construct($context);
        $this->_sns = $sns;
    }

    /**
     * Handle SNS endpoint requests
     */
    public function execute()
    {
        $message = $this->getRequest()->getContent();
        $this->_sns->processMessage($message);
    }
}
