<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart\Dependency\Client;

use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface SharedCartToMultiCartClientInterface
{
    public function getQuoteCollection(): QuoteCollectionTransfer;

    public function setQuoteCollection(QuoteCollectionTransfer $quoteCollectionTransfer): void;

    public function markQuoteAsDefault(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;
}
