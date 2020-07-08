<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Block\Adminhtml\Publish\Edit;

/**
 * Adminhtml SNS topic publish form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_coreRegistry = $registry;
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('topic_publish_form');
        $this->setTitle(__('Publish to a Topic'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $data = $this->_coreRegistry->registry('amazon_sns_publish_topic_data');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('amazon/sns_topic/publishpost'),
                    'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('topic_publish_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Publish to a Topic'), 'class' => 'fieldset-wide']
        );

        if (isset($data['topic_id'])) {
            $fieldset->addField('topic_id', 'hidden', ['name' => 'topic_id']);
        }

        $fieldset->addField(
            'subject',
            'text',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'message_structure',
            'select',
            [
                'name' => 'message_structure',
                'label' => __('Message Format'),
                'title' => __('Message Format'),
                'required' => false,
                'options' => ['' => __('Raw'), 'json' => __('JSON')]
            ]
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
                'label' => __('Message'),
                'title' => __('Message'),
                'required' => true
            ]
        );

        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
