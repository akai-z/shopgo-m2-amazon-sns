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
 * Add an SNS topic ARN command
 */
class AddArn extends Command
{
    /**
     * Topic ARN argument
     */
    const TOPIC_ARN_ARGUMENT = 'topic_arn';

    /**
     * Topic name argument
     */
    const TOPIC_NAME_ARGUMENT = 'topic_name';

    /**
     * Subscribe argument
     */
    const SUBSCRIBE_ARGUMENT = 'subscribe';

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
        $this->setName('amazon-sns:topic:add-arn')
            ->setDescription('Add an existing SNS topic ARN command')
            ->setDefinition([
                new InputArgument(
                    self::TOPIC_ARN_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Topic ARN'
                ),
                new InputArgument(
                    self::TOPIC_NAME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Topic Name'
                ),
                new InputArgument(
                    self::SUBSCRIBE_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Subscribe'
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

        $topicArn  = $input->getArgument(self::TOPIC_ARN_ARGUMENT);
        $topicName = $input->getArgument(self::TOPIC_NAME_ARGUMENT);
        $subscribe = $input->getArgument(self::SUBSCRIBE_ARGUMENT);

        if (!$topicName) {
            $topicName = $topicArn;
        }

        try {
            $topic = $this->_topicFactory->create();
            $topic->setName($topicName)
                ->setArn($topicArn)
                ->save();

            if ($subscribe) {
                $sns = $this->_snsFactory->create();
                $sns->subscribe($topicArn);
            }

            $result = 'Topic has been added successfully!';
        } catch (\Exception $e) {
            $result = 'Could not add topic!';
        }

        $output->writeln('<info>' . $result . '</info>');
    }
}
