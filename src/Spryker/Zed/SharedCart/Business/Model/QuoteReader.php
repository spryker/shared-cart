<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\Model;

use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface;
use Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface;

class QuoteReader implements QuoteReaderInterface
{
    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface
     */
    protected $sharedCartRepository;

    /**
     * @var \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface
     */
    protected $quoteFacade;

    public function __construct(SharedCartRepositoryInterface $sharedCartRepository, SharedCartToQuoteFacadeInterface $quoteFacade)
    {
        $this->sharedCartRepository = $sharedCartRepository;
        $this->quoteFacade = $quoteFacade;
    }

    public function findSharedQuoteCollectionBySharedQuoteCriteriaFilter(
        SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer
    ): QuoteCollectionTransfer {
        $sharedCartsIdDefaultFlagData = $this->sharedCartRepository->getIsDefaultFlagForSharedCartsBySharedQuoteCriteriaFilter($sharedQuoteCriteriaFilterTransfer);

        $quoteCriteriaFilterTransfer = (new QuoteCriteriaFilterTransfer())
            ->setQuoteIds(array_keys($sharedCartsIdDefaultFlagData));

        return $this->applyIsDefaultFlagForSharedQuotes(
            $this->quoteFacade->getQuoteCollection($quoteCriteriaFilterTransfer),
            $sharedCartsIdDefaultFlagData,
        );
    }

    protected function applyIsDefaultFlagForSharedQuotes(
        QuoteCollectionTransfer $quoteCollectionTransfer,
        array $quotesIsDefaultFlagData
    ): QuoteCollectionTransfer {
        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $quoteTransfer->setIsDefault($quotesIsDefaultFlagData[$quoteTransfer->getIdQuote()]);
        }

        return $quoteCollectionTransfer;
    }
}
