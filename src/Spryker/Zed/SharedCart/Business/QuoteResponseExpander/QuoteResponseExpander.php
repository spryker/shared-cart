<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\QuoteResponseExpander;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\MultiCart\Dependency\Facade\MultiCartToQuoteFacadeInterface;

class QuoteResponseExpander implements QuoteResponseExpanderInterface
{
    /**
     * @var \Spryker\Zed\MultiCart\Dependency\Facade\MultiCartToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @param \Spryker\Zed\MultiCart\Dependency\Facade\MultiCartToQuoteFacadeInterface $quoteFacade
     */
    public function __construct(MultiCartToQuoteFacadeInterface $quoteFacade)
    {
        $this->quoteFacade = $quoteFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function expand(QuoteResponseTransfer $quoteResponseTransfer): QuoteResponseTransfer
    {
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();
        $customerTransfer = $quoteTransfer->requireCustomer()->getCustomer();

        $sharedQuoteCollectionTransfer = $this->findSharedCustomerQuotes($customerTransfer);
        $quoteResponseTransfer->addSharedCustomerQuotes($sharedQuoteCollectionTransfer);

        if (!$quoteResponseTransfer->getQuoteTransfer()->getIsActive() && count($sharedQuoteCollectionTransfer->getQuotes())) {
            $quoteResponseTransfer->setQuoteTransfer($this->getActiveQuote($sharedQuoteCollectionTransfer));
        }

//        $this->deactivateActiveQuotes($customerQuoteCollectionTransfer, $quoteResponseTransfer->getQuoteTransfer()->getIdQuote());

        return $quoteResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $quoteCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function getActiveQuote(QuoteCollectionTransfer $quoteCollectionTransfer): QuoteTransfer
    {
        $quoteTransferList = (array)$quoteCollectionTransfer->getQuotes();
        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            if ($quoteTransfer->getIsActive()) {
                return $quoteTransfer;
            }
        }
        $quoteTransfer = reset($quoteTransferList);

        return $this->setQuoteAsActive($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function setQuoteAsActive(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteTransfer->setIsActive(true);
        $this->quoteFacade->persistQuote($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function findSharedCustomerQuotes(CustomerTransfer $customerTransfer): QuoteCollectionTransfer
    {
        $quoteCriteriaFilterTransfer = new QuoteCriteriaFilterTransfer();
        $quoteCriteriaFilterTransfer->setCustomerReference($customerTransfer->getCustomerReference());
        $customerQuoteCollectionTransfer = $this->quoteFacade->getQuoteCollection($quoteCriteriaFilterTransfer);

        foreach ($customerQuoteCollectionTransfer->getQuotes() as $customerQuoteTransfer) {
            $customerQuoteTransfer->setCustomer($customerTransfer);
        }

        return $customerQuoteCollectionTransfer;
    }

//    /**
//     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $quotesCollectionTransfer
//     * @param int $idActiveQuote
//     *
//     * @return void
//     */
//    protected function deactivateActiveQuotes(QuoteCollectionTransfer $quotesCollectionTransfer, $idActiveQuote)
//    {
//        foreach ($quotesCollectionTransfer->getQuotes() as $quoteTransfer) {
//            if ($quoteTransfer->getIsActive() && $idActiveQuote !== $quoteTransfer->getIdQuote()) {
//                $quoteTransfer->setIsActive(false);
//                $this->quoteFacade->persistQuote($quoteTransfer);
//            }
//        }
//    }
}
