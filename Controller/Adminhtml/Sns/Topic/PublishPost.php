<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic;

class PublishPost extends \ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic
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
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $topicId = ['topic_id' => $this->getRequest()->getParam('topic_id')];

            try {
                $model = $this->_objectManager->create('ShopGo\AmazonSns\Model\Topic');

                if (empty($topicId['topic_id'])) {
                    $this->messageManager->addError(__('SNS topic is not specified.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);

                    return $resultRedirect->setPath('*/*/publish');
                }

                $model->load($topicId['topic_id']);

                if (!$model->getId()) {
                    $this->messageManager->addError(__('This SNS topic no longer exists.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);

                    return $resultRedirect->setPath('*/*/publish');
                }

                $this->_sns->publish($data['message'], $model->getArn(), $data['subject'], '', $data['message_structure']);

                $this->messageManager->addSuccess(__('The SNS message has been published to topic.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);

                return $resultRedirect->setPath('*/*/publish', $topicId);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
