<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart\Plugin;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\PersistentCartExtension\Dependency\Plugin\PersistentCartChangeExpanderPluginInterface;

/**
 * @method \Spryker\Client\SharedCart\SharedCartFactory getFactory()
 */
class ProductSeparatePersistentCartChangeExpanderPlugin extends AbstractPlugin implements PersistentCartChangeExpanderPluginInterface
{
    /**
     * @var string
     */
    public const PARAM_SEPARATE_PRODUCT = 'separate_product';

    /**
     * Specification:
     * - Takes quote id form params and replace it in quote change request.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PersistentCartChangeTransfer $persistentCartChangeTransfer
     * @param array<string, mixed> $params
     *
     * @return \Generated\Shared\Transfer\PersistentCartChangeTransfer
     */
    public function extend(PersistentCartChangeTransfer $persistentCartChangeTransfer, array $params = []): PersistentCartChangeTransfer
    {
        if (!empty($params[static::PARAM_SEPARATE_PRODUCT])) {
            $quoteTransfer = $this->getFactory()->getMultiCartClient()->findQuoteById($persistentCartChangeTransfer->getIdQuote());
            if ($quoteTransfer) {
                $this->addSeparatorToItems($persistentCartChangeTransfer, $quoteTransfer);
            }
        }

        return $persistentCartChangeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PersistentCartChangeTransfer $cartChangeTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addSeparatorToItems(PersistentCartChangeTransfer $cartChangeTransfer, QuoteTransfer $quoteTransfer): void
    {
        foreach ($cartChangeTransfer->getItems() as $itemTransfer) {
            if ($this->findQuoteItem($quoteTransfer, $itemTransfer->getSku())) {
                $itemTransfer->setGroupKeyPrefix(uniqid('', true));
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    protected function findQuoteItem(QuoteTransfer $quoteTransfer, $sku): ?ItemTransfer
    {
        return $this->getFactory()->getCartClient()->findQuoteItem($quoteTransfer, $sku);
    }
}
