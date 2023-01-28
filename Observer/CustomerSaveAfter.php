<?php
namespace BeeBots\HyrosWebhooks\Observer;

use BeeBots\HyrosWebhooks\Model\Config;
use GuzzleHttp\Client;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CustomerSaveAfter implements ObserverInterface
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

        $originalCustomerData = $observer->getData('orig_customer_data_object');
        // Only send for new customers
        if ($originalCustomerData) {
            return;
        }

        $customerData = $observer->getData('customer_data_object');
        if (!$customerData) {
            return;
        }

        try {
            $this->httpClient->post(
                $this->config->getWebhookUrl(),
                [
                    'json' => [
                            'customerId' => $customerData->getId(),
                            'customerName' => $customerData->getFirstname(),
                            'customerLastName' => $customerData->getLastname(),
                            'customerEmail' => $customerData->getEmail(),
                            'eventType' => 'LEAD_CREATED',
                            'provider' => 'MAGENTO'
                    ],
                ]
            );
        } catch (Throwable $exception) {
            $this->logger->error('There was an error while notifying Hyros of the new customer', ['exception' => $exception]);
        }
    }
}
