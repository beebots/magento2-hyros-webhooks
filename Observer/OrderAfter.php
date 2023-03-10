<?php
/** @noinspection PhpUnused */
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace BeeBots\HyrosWebhooks\Observer;

use BeeBots\HyrosWebhooks\Model\Config;
use GuzzleHttp\Client;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class OrderAfter implements ObserverInterface
{
    /**
     * @param Config $config
     * @param Client $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Config $config,
        private readonly Client $httpClient,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Function: execute
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->config->isEnabled() || !$this->config->getWebhookUrl()) {
            return;
        }

        $order = $observer->getData('order');
        if (!$order || !$order->getId()) {
            return;
        }

        try {
            $this->httpClient->post(
                $this->config->getWebhookUrl(),
                [
                    'json' => [
                        'transactionId' => $order->getId(),
                        'eventType' => 'SALE_CREATED',
                        'provider' => 'MAGENTO',
                    ],
                ]
            );
        } catch (Throwable $exception) {
            $this->logger->error('There was an error while notifying Hyros of the order', ['exception' => $exception]);
        }
    }
}
