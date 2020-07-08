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
 * Create an SNS topic command
 */
class Create extends Command
{
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
     * @param State $state
     * @param \ShopGo\AmazonSns\Model\SnsFactory $snsFactory
     */
    public function __construct(
        State $state,
        \ShopGo\AmazonSns\Model\SnsFactory $snsFactory
    ) {
        $this->_state = $state;
        $this->_snsFactory = $snsFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('amazon-sns:topic:create')
            ->setDescription('Create an SNS topic command')
            ->setDefinition([
                new InputArgument(
                    self::TOPIC_NAME_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Topic name'
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

        $topicName = $input->getArgument(self::TOPIC_NAME_ARGUMENT);
        $subscribe = $input->getArgument(self::SUBSCRIBE_ARGUMENT);

        $sns = $this->_snsFactory->create();

        $result   = $sns->createTopic($topicName, $subscribe, 1);
        $topicArn = $sns->getHelper()->getAwsClientResult($result, 'TopicArn');

        $result = $topicArn
            ? 'Topic has been created successfully!'
            : 'Could not create topic!';

        $output->writeln('<info>' . $result . '</info>');
    }
}
