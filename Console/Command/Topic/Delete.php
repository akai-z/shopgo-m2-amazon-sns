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
 * Delete an SNS topic command
 */
class Delete extends Command
{
    /**
     * Topic attribute argument
     */
    const TOPIC_ATTRIBUTE_ARGUMENT = 'topic_attribute';

    /**
     * Topic attribute value argument
     */
    const TOPIC_ATTRIBUTE_VALUE_ARGUMENT = 'topic_attribute_value';

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
     * @param State $state
     * @param \ShopGo\AmazonSns\Model\SnsFactory $snsFactory
     * @param \ShopGo\AmazonSns\Model\TopicFactory $topicFactory
     */
    public function __construct(
        State $state,
        \ShopGo\AmazonSns\Model\SnsFactory $snsFactory,
        \ShopGo\AmazonSns\Model\TopicFactory $topicFactory
    ) {
        $this->_state = $state;
        $this->_snsFactory = $snsFactory;
        $this->_topicFactory = $topicFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('amazon-sns:topic:delete')
            ->setDescription('Delete an SNS topic command')
            ->setDefinition([
                new InputArgument(
                    self::TOPIC_ATTRIBUTE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Topic attribute'
                ),
                new InputArgument(
                    self::TOPIC_ATTRIBUTE_VALUE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Topic attribute value'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode('adminhtml');

        $topicAttribute = $input->getArgument(self::TOPIC_ATTRIBUTE_ARGUMENT);
        $topicAttributeValue = $input->getArgument(self::TOPIC_ATTRIBUTE_VALUE_ARGUMENT);

        $topic = $this->_topicFactory->create();
        $topic = $topic->getCollection()
            ->addFieldToFilter($topicAttribute, $topicAttributeValue)
            ->getItems();

        $topic = reset($topic);
        $sns = $this->_snsFactory->create();
        $result = $sns->deleteTopic($topic->getArn(), $topic->getSubscriptionArn(), $topic);

        $result = $result
            ? 'Topic has been deleted successfully!'
            : 'Could not delete topic!';

        $output->writeln('<info>' . $result . '</info>');
    }
}
