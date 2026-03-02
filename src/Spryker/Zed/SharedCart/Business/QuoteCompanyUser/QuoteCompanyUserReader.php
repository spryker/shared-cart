<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\QuoteCompanyUser;

use Generated\Shared\Transfer\QuoteCompanyUserTransfer;
use Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface;

class QuoteCompanyUserReader implements QuoteCompanyUserReaderInterface
{
    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface
     */
    protected $sharedCartRepository;

    public function __construct(SharedCartRepositoryInterface $sharedCartRepository)
    {
        $this->sharedCartRepository = $sharedCartRepository;
    }

    public function findQuoteCompanyUserByUuid(QuoteCompanyUserTransfer $quoteCompanyUserTransfer): ?QuoteCompanyUserTransfer
    {
        $quoteCompanyUserTransfer->requireUuid();

        return $this->sharedCartRepository->findQuoteCompanyUserByUuid($quoteCompanyUserTransfer->getUuid());
    }
}
