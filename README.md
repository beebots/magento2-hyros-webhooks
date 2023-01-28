# Beebots/HyrosWebhooks
This module adds required webhooks for integration with hyros

## Installation
Disclaimer: Please don't do this directly on a production environment. Install on a dev environment and test first.

1. Install with composer
    ```
    composer require "beebots/magento2-hyros-webhooks"
    bin/magento setup:upgrade
    ```
1. Configuration
   - Navigate to Stores > Configuration > Bee Bots > Hyros Webhooks
   - Enable the extension
   - Add the webhook url you get from the Hyros setup instruction
   - Flush the cache




