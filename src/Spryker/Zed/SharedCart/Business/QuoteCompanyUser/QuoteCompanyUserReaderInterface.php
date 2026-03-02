<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\QuoteCompanyUser;

use Generated\Shared\Transfer\QuoteCompanyUserTransfer;

interface QuoteCompanyUserReaderInterface
{
    public function findQuoteCompanyUserByUuid(QuoteCompanyUserTransfer $quoteCompanyUserTransfer): ?QuoteCompanyUserTransfer;
}
