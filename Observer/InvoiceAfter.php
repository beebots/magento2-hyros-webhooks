<?php
namespace BeeBots\HyrosWebhooks\Observer;

use BeeBots\HyrosWebhooks\Model\Config;
use GuzzleHttp\Client;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class InvoiceAfter implements ObserverInterface
{
    /**
     * @param Config $config
     */
    public function __construct(
        private Config $config,
        private Client $httpClient,
        private LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * Function: execute
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled() || !$this->config->getWebhookUrl()) {
            return;
        }

        $invoice = $observer->getInvoice();
        if (!$invoice || !$invoice->getOrder()) {
            return;
        }

        $order = $invoice->getOrder();
        try {
            $this->httpClient->post(
                $this->config->getWebhookUrl(),
                [
                    'json' => [
                        'transactionId' => $order->getIncrementId(),
                        'eventType' => 'SALE_CREATED',
                        'provider' => 'MAGENTO',
                    ],
                ]
            );
        } catch (Throwable $exception) {
            $this->logger->error('There was an error while notifying Hyros of the credit memo', ['exception' => $exception]);
        }
    }
}
