<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\QuoteShareDetails;

use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Generated\Shared\Transfer\ShareDetailCriteriaFilterTransfer;

interface QuoteShareDetailsReaderInterface
{
    public function getShareDetailsByIdQuote(QuoteTransfer $quoteTransfer, bool $excludeAnonymizedCustomers = false): ShareDetailCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     * @param bool $excludeAnonymizedCustomers
     *
     * @return array<\Generated\Shared\Transfer\ShareDetailCollectionTransfer>
     */
    public function getSharedCartDetails(ShareCartRequestTransfer $shareCartRequestTransfer, bool $excludeAnonymizedCustomers = false): array;

    public function getShareDetailCollectionByShareDetailCriteria(
        ShareDetailCriteriaFilterTransfer $shareDetailCriteriaFilterTransfer
    ): ShareDetailCollectionTransfer;

    public function getCustomerCollectionByQuote(QuoteTransfer $quoteTransfer): CustomerCollectionTransfer;
}
