<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AmazonSns\Console\Command\Topic;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

/**
 * Publish to an SNS topic command
 */
class Publish extends Command
{
    /**
     * Message argument
     */
    const MESSAGE_ARGUMENT = 'message';

    /**
     * Topic attribute argument
     */
    const TOPIC_ATTRIBUTE_ARGUMENT = 'topic_attribute';

    /**
     * Topic attribute value argument
     */
    const TOPIC_ATTRIBUTE_VALUE_ARGUMENT = 'topic_attribute_value';

    /**
     * Topic ARN argument
     */
    const TOPIC_ARN_ARGUMENT = 'topic_arn';

    /**
     * Subject argument
     */
    const SUBJECT_ARGUMENT = 'subject';

    /**
     * Target ARN argument
     */
    const TARGET_ARN_ARGUMENT = 'target_arn';

    /**
     * Message type argument
     */
    const MESSAGE_TYPE_ARGUMENT = 'message_type';

    /**
     * Message attributes argument
     */
    const MESSAGE_ATTRIBUTES_ARGUMENT = 'message_attributes';

    /**
     * @var State
     */
    private $_state;

    /**
     * @var \ShopGo\AmazonSns\Model\SnsFactory
     */
    private $_snsFactory;

    /**
     * @var \ShopGo\AmazonSns\Model\TopicFactory
     */
    private $_topicFactory;

    /**
     * @var \Magento\Framework\Config\ReaderInterface
     */
    private $_topicFileConfig;

    /**
     * @param State $state
     * @param \ShopGo\AmazonSns\Model\SnsFactory $snsFactory
     * @param \ShopGo\AmazonSns\Model\TopicFactory $topicFactory
     * @param \Magento\Framework\Config\ReaderInterface $topicFileConfig
     */
    public function __construct(
        State $state,
        \ShopGo\AmazonSns\Model\SnsFactory $snsFactory,
        \ShopGo\AmazonSns\Model\TopicFactory $topicFactory,
        \Magento\Framework\Config\ReaderInterface $topicFileConfig
    ) {
        $this->_state = $state;
        $this->_snsFactory = $snsFactory;
        $this->_topicFactory = $topicFactory;
        $this->_topicFileConfig = $topicFileConfig;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $arguments = [
            new InputArgument(
                self::MESSAGE_ARGUMENT,
                InputArgument::REQUIRED,
                'Message'
            ),
            new InputArgument(
                self::TOPIC_ATTRIBUTE_ARGUMENT,
                InputArgument::OPTIONAL,
                'Topic attribute'
            ),
            new InputArgument(
                self::TOPIC_ATTRIBUTE_VALUE_ARGUMENT,
                InputArgument::OPTIONAL,
                'Topic attribute value'
            ),
            new InputArgument(
                self::SUBJECT_ARGUMENT,
                InputArgument::OPTIONAL,
                'Subject'
            ),
            new InputArgument(
                self::MESSAGE_TYPE_ARGUMENT,
                InputArgument::OPTIONAL,
                'Message type'
            ),
            new InputArgument(
                self::TOPIC_ARN_ARGUMENT,
                InputArgument::OPTIONAL,
                'Topic ARN'
            ),
            new InputArgument(
                self::TARGET_ARN_ARGUMENT,
                InputArgument::OPTIONAL,
                'Target ARN'
            ),
            new InputArgument(
                self::MESSAGE_ATTRIBUTES_ARGUMENT,
                InputArgument::OPTIONAL,
                'Message attributes'
            )
        ];

        $this->setName('amazon-sns:topic:publish')
            ->setDescription('Publish to an SNS topic command')
            ->setDefinition($arguments);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode('adminhtml');

        $message = $input->getArgument(self::MESSAGE_ARGUMENT);
        $topicAttribute = $input->getArgument(self::TOPIC_ATTRIBUTE_ARGUMENT);
        $topicAttributeValue = $input->getArgument(self::TOPIC_ATTRIBUTE_VALUE_ARGUMENT);
        $topicArn = $input->getArgument(self::TOPIC_ARN_ARGUMENT);
        $subject = $input->getArgument(self::SUBJECT_ARGUMENT);
        $targetArn = $input->getArgument(self::TARGET_ARN_ARGUMENT);
        $messageType = $input->getArgument(self::MESSAGE_TYPE_ARGUMENT);
        $messageAttributes = $input->getArgument(self::MESSAGE_ATTRIBUTES_ARGUMENT);

        if ($messageAttributes) {
            $messageAttributes = json_decode($messageAttributes);
        }

        if ($topicAttribute && $topicAttributeValue) {
            $topic = $this->_topicFactory->create();
            $topicArn = '';
            $isTopicActive = false;

            $topicConfigData = [
                'topic' => [],
                'item' => ['attributes' => [$topicAttribute => $topicAttributeValue]]
            ];

            $configElement = $this->_topicFileConfig->getConfigElement($topicConfigData)->item(0);

            if ($configElement) {
                $topicArn = $configElement->getAttribute('arn');
                $isTopicActive = $configElement->getAttribute('is_active');
            }

            if ($isTopicActive || $isTopicActive == '') {
                if (!$topicArn) {
                    $topicArn = $topic->getCollection()
                        ->addFieldToSelect('arn')
                        ->addFieldToFilter($topicAttribute, $topicAttributeValue)
                        ->getItems();

                    $topicArn = reset($topicArn);
                    $topicArn = $topicArn ? $topicArn->getArn() : '';
                }
            }
        }

        $sns = $this->_snsFactory->create();
        $result = $sns->publish($message, $topicArn, $targetArn, $subject, $messageType, $messageAttributes);
        $messageId = $sns->getHelper()->getAwsClientResult($result, 'MessageId');

        $result = $messageId
            ? 'Topic has been published to successfully!'
            : 'Could not publish to topic!';

        $output->writeln('<info>' . $result . '</info>');
    }
}
