<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Model;

use Aws\Sns\SnsClient;
use Aws\Sns\Message as SnsMessage;
use Aws\Sns\MessageValidator as SnsMessageValidator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Topic as TopicModel;

/**
 * SNS model
 */
class Sns extends \Magento\Framework\Model\AbstractModel
{
    /**
     * SNS message type "subscription confirmation"
     */
    const MESSAGE_TYPE_SUBSCRIPTION_CONFIRMATION = 'SubscriptionConfirmation';

    /**
     * SNS message type "notification"
     */
    const MESSAGE_TYPE_NOTIFICATION = 'Notification';

    /**
     * SNS standard endpoint path
     */
    const STANDARD_ENDPOINT_PATH = 'amazon/sns/endpoint';

    /**
     * SNS API endpoint path
     *
     * @TODO This is currently not supported,
     * due to an issue that is described in the following link:
     * https://forums.aws.amazon.com/thread.jspa?messageID=418160
     * Once they fix it, this option will be used.
     * You can read more about it here:
     * http://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.html
     */
    const API_ENDPOINT_PATH = 'rest/default/V1/amazon/sns/endpoint';

    /**
     * SNS event "notificaiton"
     */
    const SNS_EVENT_NOTIFICATION = 'sns_notification';

    /**
     * XML path general protocol
     */
    const XML_PATH_GENERAL_PROTOCOL = 'amazon_sns/general/protocol';

    /**
     * @var SnsClient
     */
    protected $_client;

    /**
     * @var SnsMessageValidator
     */
    protected $_messageValidator;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    protected $_topicFileConfig;

    /**
     * @var TopicFactory
     */
    protected $_topicFactory;

    /**
     * @var \ShopGo\AmazonSns\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SnsMessageValidator $messageValidator
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Config\ReaderInterface $topicFileConfig
     * @param TopicFactory $topicFactory
     * @param \ShopGo\AmazonSns\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SnsMessageValidator $messageValidator,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Config\ReaderInterface $topicFileConfig,
        TopicFactory $topicFactory,
        \ShopGo\AmazonSns\Helper\Data $helper
    ) {
        parent::__construct($context, $registry);
        $this->_messageValidator = $messageValidator;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_topicFileConfig = $topicFileConfig;
        $this->_topicFactory = $topicFactory;
        $this->_helper = $helper;
    }

    /**
     * Get helper
     *
     * @return \ShopGo\AmazonSns\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->_helper->getConfig()->getValue(self::XML_PATH_GENERAL_PROTOCOL);
    }

    /**
     * Get SNS endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        return $baseUrl . self::STANDARD_ENDPOINT_PATH;
    }

    /**
     * Get SNS Client
     *
     * @return SnsClient
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client = SnsClient::factory($this->_helper->getAwsClientConfig());
        }

        return $this->_client;
    }

    /**
     * Verify SNS message signature
     *
     * @return boolean
     */
    public function verifyMessageSignature()
    {
        $message = SnsMessage::fromRawPostData();
        return $this->_messageValidator->isValid($message);
    }

    /**
     * Load topic model
     *
     * @param int|string|TopicModel $topic
     * @param int|string|null $id
     * @return mixed
     */
    public function loadTopicModel($topic = '', $id = null)
    {
        switch (gettype($topic)) {
            case 'integer':
            case 'string':
                $topic = $this->_topicFactory->create()->load($topic);
                break;
            case 'object':
                if ($topic instanceof TopicModel && !$topic->getTopicId() && $id) {
                    $topic->load($id);
                    break;
                }
            default:
                $topic = $this->_topicFactory->create();
                if ($id) {
                    $topic->load($id);
                }
        }

        return $topic;
    }

    /**
     * Load XML topic
     *
     * @param string $topicAttribute
     * @param string $topicAttributeValue
     * @return array
     */
    public function loadXmlTopic($topicAttribute, $topicAttributeValue)
    {
        $topic = [];
        $topicConfigData = [
            'topic' => [],
            'item' => ['attributes' => [$topicAttribute => $topicAttributeValue]]
        ];

        $topicElement = $this->_topicFileConfig->getConfigElement($topicConfigData);

        if ($topicElement !== null) {
            $topic['name'] = $topicElement->getAttribute('name');
            $topic['arn'] = $topicElement->getAttribute('arn');
            $topic['is_active'] = $topicElement->getAttribute('is_active');
        }

        return $topic;
    }

    /**
     * Check whether a flag is set in SNS message
     *
     * @param array $ifConfig
     * @return bool
     */
    public function isSetMessageFlag($ifConfig)
    {
        $flag = true;

        $scope = $ifConfig['scope'] ? $ifConfig['scope'] : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeCode = $ifConfig['scope_code'] ? $ifConfig['scope_code'] : null;

        $config = $this->_helper->getConfig()->getValue($ifConfig['xpath'], $scope, $scopeCode);

        if ($config != $ifConfig['value']) {
            $flag = false;
        }

        return $flag;
    }

    /**
     * Process SNS message
     *
     * @param string $body
     * @return boolean|void
     */
    public function processMessage($body)
    {
        if (!$this->verifyMessageSignature()) {
            return false;
        }

        $data = json_decode($body, true);

        switch ($data['Type']) {
            case self::MESSAGE_TYPE_SUBSCRIPTION_CONFIRMATION:
                $this->confirmSubscription($data['Token'], $data['TopicArn']);
                break;
            case self::MESSAGE_TYPE_NOTIFICATION:
                $message = json_decode($data['Message'], true);

                if (isset($message['arguments']['ifconfig'])) {
                    if (!$this->isSetMessageFlag($message['arguments']['ifconfig'])) {
                        break;
                    }
                }

                $topic = $this->loadXmlTopic('arn', $data['TopicArn']);
                if (!isset($topic['name'])) {
                    $topic = $this->loadTopicModel($data['TopicArn']);
                    $isTopicActive = $topic->getIsActive();
                } else {
                    $isTopicActive = $topic['is_active'];
                    if ($isTopicActive == '') {
                        $isTopicActive = 1;
                    }
                }

                if ($isTopicActive) {
                    $this->_eventManager->dispatch(
                        self::SNS_EVENT_NOTIFICATION,
                        [
                            'notification' => $message
                        ]
                    );
                }
                break;
        }
    }

    /**
     * Create SNS topic
     *
     * @param string $name
     * @param bool|int $subscribe
     * @param int|string|TopicModel $topicModel
     * @return \Aws\Result
     */
    public function createTopic($name, $subscribe = false, $topicModel = 0)
    {
        $result = $this->getClient()->createTopic([
            'Name' => $name
        ]);

        $topicArn = $result->get('TopicArn');

        if ($subscribe && $topicArn) {
            $this->subscribe($topicArn);
        }

        if ($topicModel && $topicArn) {
            try {
                $topic = $this->loadTopicModel($topicModel);
                $topic->setName($name)
                    ->setArn($topicArn)
                    ->save();
            } catch (\Exception $e) {}
        }

        return $result;
    }

    /**
     * Delete SNS topic
     *
     * @param string $arn
     * @param string $subscriptionArn
     * @param int|string|TopicModel $topicModel
     * @return \Aws\Result
     */
    public function deleteTopic($arn, $subscriptionArn = '', $topicModel = 0)
    {
        if ($subscriptionArn) {
            $this->unsubscribe($subscriptionArn);
        }

        $result = $this->getClient()->deleteTopic([
            'TopicArn' => $arn
        ]);

        if ($topicModel) {
            try {
                if ($topicModel) {
                    if (gettype($topicModel) == 'object' && $topicModel->getTopicId()) {
                        $topic = $topicModel;
                    } else {
                        $topic = $this->loadTopicModel($topicModel);
                    }
                } else {
                    $topic = $this->loadTopicModel($arn);
                }

                $topic->delete();
            } catch (\Exception $e) {}
        }

        return $result;
    }

    /**
     * Subscribe to SNS topic
     *
     * @param string $topicArn
     * @param string $protocol
     * @param string $endpoint
     * @return \Aws\Result
     */
    public function subscribe($topicArn, $protocol = '', $endpoint = '')
    {
        if (!$protocol) {
            $protocol = $this->getProtocol();
        }
        if (!$endpoint) {
            $endpoint = $this->getEndpoint();
        }

        $result = $this->getClient()->subscribe([
            'TopicArn' => $topicArn,
            'Protocol' => $protocol,
            'Endpoint' => $endpoint
        ]);

        if ($endpoint != $this->getEndpoint()) {
            Try {
                $topic = $this->loadTopicModel($topicArn);
                $topic->setEndpointType(
                    \ShopGo\AmazonSns\Model\Topic::ENDPOINT_TYPE_EXTERNAL
                )->save();
            } catch (\Exception $e) {}
        }

        return $result;
    }

    /**
     * Unsubscribe from SNS topic
     *
     * @param string $subscriptionArn
     * @param int|string|TopicModel $topicModel
     * @return \Aws\Result
     */
    public function unsubscribe($subscriptionArn, $topicModel = 0)
    {
        $result = $this->getClient()->unsubscribe([
            'SubscriptionArn' => $subscriptionArn
        ]);

        if ($topicModel) {
            try {
                $topicArn = substr($subscriptionArn, 0, strrpos($subscriptionArn, ':'));
                $topic = $this->loadTopicModel($topicArn);
                $topic->setSubscriptionArn('')->save();
            } catch (\Exception $e) {}
        }

        return $result;
    }

    /**
     * Confirm SNS topic subscription
     *
     * @param string $token
     * @param string $topicArn
     * @param string $authenticateOnUnsubscribe
     * @return \Aws\Result
     */
    public function confirmSubscription($token, $topicArn, $authenticateOnUnsubscribe = 'true')
    {
        $result = $this->getClient()->confirmSubscription([
            'Token'    => $token,
            'TopicArn' => $topicArn,
            'AuthenticateOnUnsubscribe' => $authenticateOnUnsubscribe
        ]);

        try {
            $topic = $this->loadTopicModel($topicArn);
            $subscriptionArn = $result->get('SubscriptionArn');

            if ($topic->getTopicId() && $subscriptionArn) {
                $topic->setSubscriptionArn($subscriptionArn)->save();
            }
        } catch (\Exception $e) {}

        return $result;
    }

    /**
     * Publish notifications to topic
     *
     * @param string $message
     * @param string $topicArn
     * @param string $subject
     * @param string $targetArn
     * @param string $messageStructure
     * @param array $messageAttributes
     * @return \Aws\Result
     */
    public function publish($message, $topicArn = '', $subject = '', $targetArn = '', $messageStructure = '', $messageAttributes = [])
    {
        $data = [
            'Message' => $message
        ];

        if ($topicArn) {
            $data['TopicArn'] = $topicArn;
        }
        if ($subject) {
            $data['Subject'] = $subject;
        }
        if ($targetArn) {
            $data['TargetArn'] = $targetArn;
        }
        if ($messageStructure) {
            $data['MessageStructure'] = $messageStructure;
        }
        if ($messageAttributes) {
            $data['MessageAttributes'] = $messageAttributes;
        }

        $result = $this->getClient()->publish($data);

        return $result;
    }
}
