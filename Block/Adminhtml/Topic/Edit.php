<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Block\Adminhtml\Topic;

/**
 * SNS topic edit form container
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
        $this->_objectId = 'topic_id';
        $this->_blockGroup = 'ShopGo_AmazonSns';
        $this->_controller = 'adminhtml_topic';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Topic'));
        $this->buttonList->update('delete', 'label', __('Delete Topic'));
    }

    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('amazon_sns_topic')->getId()) {
            return __(
                "Edit Topic '%1'",
                $this->escapeHtml($this->_coreRegistry->registry('amazon_sns_topic')->getName())
            );
        } else {
            return __('New Topic');
        }
    }
}
