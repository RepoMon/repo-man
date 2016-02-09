<?php namespace Ace\Repository\Queue;

use Ace\Repository\Configuration;
use Ace\Repository\Queue\QueueClient;

/**
 * @author timrodger
 * Date: 07/12/15
 */
class QueueClientFactory
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @return QueueClient
     */
    public function create()
    {
        return new QueueClient(
            $this->config->getRabbitHost(),
            $this->config->getRabbitPort(),
            $this->config->getRabbitChannelName()
        );
    }
}
