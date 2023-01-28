<?php
namespace BeeBots\HyrosWebhooks\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const ENABLED_PATH = 'beebots/hyros_webhooks/enabled';
    public const WEBHOOK_URL_PATH = 'beebots/hyros_webhooks/webhook_url';

    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Function: isEnabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::ENABLED_PATH);
    }

    public function getWebhookUrl(): string
    {
        return (string)$this->scopeConfig->getValue(self::WEBHOOK_URL_PATH);
    }
}
