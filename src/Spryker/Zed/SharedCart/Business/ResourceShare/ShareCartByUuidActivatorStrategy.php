<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\ResourceShare;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ResourceShareRequestTransfer;
use Generated\Shared\Transfer\ResourceShareResponseTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface;
use Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface;

class ShareCartByUuidActivatorStrategy implements ShareCartByUuidActivatorStrategyInterface
{
    protected const GLOSSARY_KEY_CART_ACCESS_DENIED = 'shared_cart.resource_share.strategy.error.cart_access_denied';
    /**
     * @uses \Spryker\Zed\PersistentCartShare\Business\Quote\QuoteReader::GLOSSARY_KEY_QUOTE_IS_NOT_AVAILABLE
     */
    protected const GLOSSARY_KEY_QUOTE_IS_NOT_AVAILABLE = 'persistent_cart_share.error.quote_is_not_available';

    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface
     */
    protected $sharedCartRepository;

    /**
     * @var \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @var \Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteCompanyUserWriterInterface
     */
    protected $resourceShareQuoteCompanyUserWriter;

    /**
     * @param \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface $sharedCartRepository
     * @param \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface $quoteFacade
     * @param \Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteCompanyUserWriterInterface $resourceShareQuoteCompanyUserWriter
     */
    public function __construct(
        SharedCartRepositoryInterface $sharedCartRepository,
        SharedCartToQuoteFacadeInterface $quoteFacade,
        ResourceShareQuoteCompanyUserWriterInterface $resourceShareQuoteCompanyUserWriter
    ) {
        $this->sharedCartRepository = $sharedCartRepository;
        $this->quoteFacade = $quoteFacade;
        $this->resourceShareQuoteCompanyUserWriter = $resourceShareQuoteCompanyUserWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ResourceShareResponseTransfer
     */
    public function applyShareCartByUuidActivatorStrategy(
        ResourceShareRequestTransfer $resourceShareRequestTransfer
    ): ResourceShareResponseTransfer {
        $resourceShareRequestTransfer->requireCustomer()
            ->requireResourceShare();

        if ($this->isProvidedCompanyUserResourceShareOwner($resourceShareRequestTransfer)) {
            return (new ResourceShareResponseTransfer())
                ->setIsSuccessful(true)
                ->setResourceShare($resourceShareRequestTransfer->getResourceShare());
        }

        if (!$this->isProvidedCompanyUserAllowedToShareCart($resourceShareRequestTransfer)) {
            return (new ResourceShareResponseTransfer())
                ->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setValue(static::GLOSSARY_KEY_CART_ACCESS_DENIED)
                );
        }

        if ($this->isSharedCartLocked($resourceShareRequestTransfer)) {
            return (new ResourceShareResponseTransfer())
                ->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setValue(static::GLOSSARY_KEY_QUOTE_IS_NOT_AVAILABLE)
                );
        }

        $shareDetailTransfer = $this->findShareDetail($resourceShareRequestTransfer);
        if ($shareDetailTransfer) {
            return $this->resourceShareQuoteCompanyUserWriter->updateCartShareForProvidedCompanyUser($resourceShareRequestTransfer, $shareDetailTransfer);
        }

        return $this->createCartShareForProvidedCompanyUser($resourceShareRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ResourceShareResponseTransfer
     */
    protected function createCartShareForProvidedCompanyUser(ResourceShareRequestTransfer $resourceShareRequestTransfer): ResourceShareResponseTransfer
    {
        $idQuote = $resourceShareRequestTransfer->getResourceShare()
            ->getResourceShareData()
            ->getIdQuote();

        $quoteResponseTransfer = $this->quoteFacade->findQuoteById($idQuote);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return (new ResourceShareResponseTransfer())
                ->setIsSuccessful(false)
                ->addMessage(
                    (new MessageTransfer())->setValue(static::GLOSSARY_KEY_QUOTE_IS_NOT_AVAILABLE)
                );
        }

        return $this->resourceShareQuoteCompanyUserWriter->createCartShareForProvidedCompanyUser($resourceShareRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ShareDetailTransfer|null
     */
    protected function findShareDetail(ResourceShareRequestTransfer $resourceShareRequestTransfer): ?ShareDetailTransfer
    {
        $idCompanyUser = $resourceShareRequestTransfer->getCustomer()
            ->getCompanyUserTransfer()
            ->getIdCompanyUser();

        $idQuote = $resourceShareRequestTransfer->getResourceShare()
            ->getResourceShareData()
            ->getIdQuote();

        return $this->sharedCartRepository->findShareDetailByIdQuoteAndIdCompanyUser($idQuote, $idCompanyUser);
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return bool
     */
    protected function isProvidedCompanyUserResourceShareOwner(ResourceShareRequestTransfer $resourceShareRequestTransfer): bool
    {
        $ownerIdCompanyUser = $resourceShareRequestTransfer->getResourceShare()
            ->getResourceShareData()
            ->getOwnerIdCompanyUser();

        $customerTransfer = $resourceShareRequestTransfer->getCustomer();

        return $customerTransfer->getCompanyUserTransfer()->getIdCompanyUser() === $ownerIdCompanyUser;
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return bool
     */
    protected function isProvidedCompanyUserAllowedToShareCart(ResourceShareRequestTransfer $resourceShareRequestTransfer): bool
    {
        $customerTransfer = $resourceShareRequestTransfer->getCustomer();
        $companyUserTransfer = $customerTransfer->getCompanyUserTransfer();

        $resourceShareTransfer = $resourceShareRequestTransfer->getResourceShare();
        $resourceShareDataTransfer = $resourceShareTransfer->getResourceShareData();

        $ownerIdCompanyBusinessUnit = $resourceShareDataTransfer->getOwnerIdCompanyBusinessUnit();
        if (!$ownerIdCompanyBusinessUnit || $ownerIdCompanyBusinessUnit !== $companyUserTransfer->getFkCompanyBusinessUnit()) {
            return false;
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareRequestTransfer $resourceShareRequestTransfer
     *
     * @return bool
     */
    protected function isSharedCartLocked(ResourceShareRequestTransfer $resourceShareRequestTransfer): bool
    {
        $quoteResponseTransfer = $this->quoteFacade->findQuoteById(
            $resourceShareRequestTransfer->getResourceShare()
                ->getResourceShareData()
                ->getIdQuote()
        );

        return (bool)$quoteResponseTransfer->requireQuoteTransfer()
            ->getQuoteTransfer()
            ->getIsLocked();
    }
}
