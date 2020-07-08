<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic;

class Subscribe extends \ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic
{
    /**
     * SNS model
     *
     * @var \ShopGo\AmazonSns\Model\Sns
     */
    protected $_sns;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \ShopGo\AmazonSns\Model\Sns $sns
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \ShopGo\AmazonSns\Model\Sns $sns
    ) {
        $this->_sns = $sns;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $topicId = $this->getRequest()->getParam('topic_id');

        try {
            $model = $this->_objectManager->create('ShopGo\AmazonSns\Model\Topic');

            if (empty($topicId)) {
                $this->messageManager->addError(__('Could not subscribe to SNS topic. The topic is not specified.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->load($topicId);

            if (!$model->getId()) {
                $this->messageManager->addError(__('Could not subscribe to SNS topic. The topic no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $this->_sns->subscribe($model->getArn());

            $this->messageManager->addSuccess(__('The SNS topic has been subscribed to successfully.'));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
