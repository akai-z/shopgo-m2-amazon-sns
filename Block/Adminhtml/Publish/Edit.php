<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Block\Adminhtml\Publish;

/**
 * SNS topic publish form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        #$this->_objectId = 'topic_id';
        $this->_blockGroup = 'ShopGo_AmazonSns';
        $this->_controller = 'adminhtml_publish';

        parent::_construct();

        $this->buttonList->remove('delete');
        $this->buttonList->update('save', 'label', __('Publish'));
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $publishData = $this->_coreRegistry->registry('amazon_sns_publish_topic_data');

        if ($publishData['topic_id']) {
            return __(
                "Publish To Topic '%1'",
                $this->escapeHtml($publishData['topic_name'])
            );
        }
    }
}
