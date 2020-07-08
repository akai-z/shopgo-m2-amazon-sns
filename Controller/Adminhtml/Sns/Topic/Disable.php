<?php
/**
 *
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic;

class Disable extends \ShopGo\AmazonSns\Controller\Adminhtml\Sns\Topic
{
    /**
     * Disable action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('topic_id');

        if ($id) {
            try {
                $model = $this->_objectManager->create('ShopGo\AmazonSns\Model\Topic');
                $model->load($id);

                $model->setIsActive(0);
                $model->save();

                $this->messageManager->addSuccess(__("You disabled the SNS topic."));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->messageManager->addError(__('We cannot find an SNS topic to disable.'));

        return $resultRedirect->setPath('*/*/');
    }
}
