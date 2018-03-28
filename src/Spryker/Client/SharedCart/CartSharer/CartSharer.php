<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart\CartSharer;

use ArrayObject;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToMessengerClientInterface;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToMultiCartClientInterface;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToPersistentCartClientInterface;
use Spryker\Client\SharedCart\Exception\CartNotFoundException;
use Spryker\Client\SharedCart\Zed\SharedCartStubInterface;

class CartSharer implements CartSharerInterface
{
    /**
     * @var \Spryker\Client\SharedCart\Dependency\Client\SharedCartToMultiCartClientInterface
     */
    protected $multiCartClient;

    /**
     * @var \Spryker\Client\SharedCart\Dependency\Client\SharedCartToPersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @var \Spryker\Client\SharedCart\Zed\SharedCartStubInterface
     */
    protected $sharedCartStub;

    /**
     * @var \Spryker\Client\SharedCart\Dependency\Client\SharedCartToMessengerClientInterface
     */
    protected $messengerClient;

    /**
     * @param \Spryker\Client\SharedCart\Zed\SharedCartStubInterface $sharedCartStub
     * @param \Spryker\Client\SharedCart\Dependency\Client\SharedCartToMultiCartClientInterface $multiCartClient
     * @param \Spryker\Client\SharedCart\Dependency\Client\SharedCartToPersistentCartClientInterface $persistentCartClient
     * @param \Spryker\Client\SharedCart\Dependency\Client\SharedCartToMessengerClientInterface $messengerClient
     */
    public function __construct(
        SharedCartStubInterface $sharedCartStub,
        SharedCartToMultiCartClientInterface $multiCartClient,
        SharedCartToPersistentCartClientInterface $persistentCartClient,
        SharedCartToMessengerClientInterface $messengerClient
    ) {
        $this->multiCartClient = $multiCartClient;
        $this->persistentCartClient = $persistentCartClient;
        $this->sharedCartStub = $sharedCartStub;
        $this->messengerClient = $messengerClient;
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function addShareCart(ShareCartRequestTransfer $shareCartRequestTransfer): QuoteResponseTransfer
    {
        $quoteTransfer = $this->getQuote($shareCartRequestTransfer->getIdQuote());
        if (!$this->validateShareCartRequest($shareCartRequestTransfer, $quoteTransfer)) {
            $this->messengerClient->addErrorMessage('shared_cart.share.error.already_exist'); //TODO: extract to constant
            $quoteResponseTransfer = new QuoteResponseTransfer();
            $quoteResponseTransfer->setIsSuccessful(false);

            return $quoteResponseTransfer;
        }
        $quoteTransfer->addShareDetail($this->createShareCartDetail($shareCartRequestTransfer));

        return $this->persistentCartClient->persistQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function removeShareCart(ShareCartRequestTransfer $shareCartRequestTransfer): QuoteResponseTransfer
    {
        $quoteTransfer = $this->getQuote($shareCartRequestTransfer->getIdQuote());
        $quoteTransfer->setShareDetails(
            $this->filterShareCartToRemove($quoteTransfer->getShareDetails(), $shareCartRequestTransfer)
        );

        return $this->persistentCartClient->persistQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function validateShareCartRequest(ShareCartRequestTransfer $shareCartRequestTransfer, QuoteTransfer $quoteTransfer): bool
    {
        foreach ($quoteTransfer->getShareDetails() as $shareDetailTransfer) {
            if ($shareDetailTransfer->getIdCompanyUser() === $shareCartRequestTransfer->getIdCompanyUser()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $idQuote
     *
     * @throws \Spryker\Client\SharedCart\Exception\CartNotFoundException
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function getQuote(int $idQuote): QuoteTransfer
    {
        $customerQuoteCollectionTransfer = $this->multiCartClient->getQuoteCollection();
        foreach ($customerQuoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            if ($quoteTransfer->getIdQuote() === $idQuote) {
                return $quoteTransfer;
            }
        }

        throw new CartNotFoundException('Cart not found'); // TODO: exception messages should always contain some extra info to help to identify the problem (e.g. add which idQuote was not found).
    }

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ShareDetailTransfer
     */
    protected function createShareCartDetail(ShareCartRequestTransfer $shareCartRequestTransfer): ShareDetailTransfer
    {
        $shareDetailTransfer = new ShareDetailTransfer();
        $shareDetailTransfer->setIdCompanyUser($shareCartRequestTransfer->getIdCompanyUser());
        $shareDetailTransfer->setQuotePermissionGroup($this->findQuotePermissionGroup($shareCartRequestTransfer->getIdQuotePermissionGroup()));

        return $shareDetailTransfer;
    }

    /**
     * @param int $idQuotePermissionGroup
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer|null
     */
    protected function findQuotePermissionGroup(int $idQuotePermissionGroup)
    {
        $criteriaFilterTransfer = new QuotePermissionGroupCriteriaFilterTransfer();
        $quotePermissionGroupTransferList = $this->sharedCartStub->getQuotePermissionGroupList($criteriaFilterTransfer);
        foreach ($quotePermissionGroupTransferList->getQuotePermissionGroups() as $quotePermissionGroupTransfer) {
            if ($quotePermissionGroupTransfer->getIdQuotePermissionGroup() === $idQuotePermissionGroup) {
                return $quotePermissionGroupTransfer;
            }
        }

        return null;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ShareDetailTransfer[] $shareDetailTransferList
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ShareDetailTransfer[]
     */
    protected function filterShareCartToRemove(ArrayObject $shareDetailTransferList, ShareCartRequestTransfer $shareCartRequestTransfer)
    {
        $filteredShareDetailTransferList = new ArrayObject();
        foreach ($shareDetailTransferList as $shareDetailTransfer) {
            if ($shareDetailTransfer->getIdCompanyUser() === $shareCartRequestTransfer->getIdCompanyUser()
                && $shareDetailTransfer->getQuotePermissionGroup()->getIdQuotePermissionGroup() === $shareCartRequestTransfer->getIdQuotePermissionGroup()
            ) {
                continue;
            }
            $filteredShareDetailTransferList->append($shareDetailTransfer);
        }

        return $filteredShareDetailTransferList;
    }
}
