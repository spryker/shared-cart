<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\Model;

use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface;

class QuotePermissionGroupReader implements QuotePermissionGroupReaderInterface
{
    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface
     */
    protected $sharedCartRepository;

    public function __construct(SharedCartRepositoryInterface $sharedCartRepository)
    {
        $this->sharedCartRepository = $sharedCartRepository;
    }

    public function getQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): QuotePermissionGroupResponseTransfer
    {
        $quotePermissionGroupResponseTransfer = new QuotePermissionGroupResponseTransfer();
        $quotePermissionGroupResponseTransfer->setIsSuccessful(true);

        foreach ($this->sharedCartRepository->findQuotePermissionGroupList($criteriaFilterTransfer) as $quotePermissionGroupTransfer) {
            $quotePermissionGroupResponseTransfer->addQuotePermissionGroup($quotePermissionGroupTransfer);
        }

        return $quotePermissionGroupResponseTransfer;
    }

    public function findQuotePermissionGroupById(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupResponseTransfer
    {
        $quotePermissionGroupTransfer->requireIdQuotePermissionGroup();

        $quotePermissionGroupResponseTransfer = new QuotePermissionGroupResponseTransfer();
        $quotePermissionGroupResponseTransfer->setIsSuccessful(false);

        $quotePermissionGroupTransfer = $this->sharedCartRepository->findQuotePermissionGroupById($quotePermissionGroupTransfer->getIdQuotePermissionGroup());
        if (!$quotePermissionGroupTransfer) {
            return $quotePermissionGroupResponseTransfer;
        }

        return $quotePermissionGroupResponseTransfer->addQuotePermissionGroup($quotePermissionGroupTransfer)
            ->setIsSuccessful(true);
    }
}
