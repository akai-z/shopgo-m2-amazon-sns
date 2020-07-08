<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Block\Adminhtml\Topic\Edit;

/**
 * Adminhtml SNS topic edit form
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
        $this->setId('topic_form');
        $this->setTitle(__('Topic Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('amazon_sns_topic');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $form->setHtmlIdPrefix('topic_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getTopicId()) {
            $fieldset->addField('topic_id', 'hidden', ['name' => 'topic_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'subscribe',
            'select',
            [
                'label' => __('Subscribe'),
                'title' => __('Subscribe'),
                'name' => 'subscribe',
                'required' => false,
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );
        if (!$this->getData('subscribe')) {
            $model->setData('subscribe', '1');
        }

        // Edit mode is disabled, so the code inside the if statement
        // will never get executed.
        if ($this->_coreRegistry->registry('amazon_sns_topic')->getId()) {
            $fieldset->addField(
                'arn',
                'text',
                [
                    'name' => 'arn',
                    'label' => __('ARN'),
                    'title' => __('ARN'),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'subscription_arn',
                'text',
                [
                    'name' => 'subscription_arn',
                    'label' => __('Subscription ARN'),
                    'title' => __('Subscription ARN'),
                    'required' => false
                ]
            );
        }

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => false,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
