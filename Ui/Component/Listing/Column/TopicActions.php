<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TopicActions
 */
class TopicActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_ENABLE      = 'amazon/sns_topic/enable';
    const URL_PATH_DISABLE     = 'amazon/sns_topic/disable';
    const URL_PATH_SUBSCRIBE   = 'amazon/sns_topic/subscribe';
    const URL_PATH_UNSUBSCRIBE = 'amazon/sns_topic/unsubscribe';
    const URL_PATH_PUBLISH     = 'amazon/sns_topic/publish';
    const URL_PATH_DELETE      = 'amazon/sns_topic/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['topic_id'])) {
                    $item[$this->getData('name')] = [
                        'enable' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_ENABLE,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Enable'),
                            'confirm' => [
                                'title' => __('Enable "${ $.$data.name }"'),
                                'message' => __('Are you sure you want to enable a "${ $.$data.name }" record?')
                            ]
                        ],
                        'disable' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DISABLE,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Disable'),
                            'confirm' => [
                                'title' => __('Disable "${ $.$data.name }"'),
                                'message' => __('Are you sure you want to disable a "${ $.$data.name }" record?')
                            ]
                        ],
                        'subscribe' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_SUBSCRIBE,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Subscribe'),
                            'confirm' => [
                                'title' => __('Subscribe to Topic "${ $.$data.name }"'),
                                'message' => __('Are you sure you want to subscribe to topic "${ $.$data.name }"?')
                            ]
                        ],
                        'unsubscribe' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_UNSUBSCRIBE,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Unsubscribe'),
                            'confirm' => [
                                'title' => __('Unsubscribe fromS Topic "${ $.$data.name }"'),
                                'message' => __('Are you sure you want to unsubscribe from topic "${ $.$data.name }"?')
                            ]
                        ],
                        'publish' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_PUBLISH,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Publish')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'topic_id' => $item['topic_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.name }"'),
                                'message' => __('Are you sure you want to delete a "${ $.$data.name }" record?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
