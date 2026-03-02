<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart\CartDeleteChecker;

use Generated\Shared\Transfer\QuoteTransfer;

interface CartDeleteCheckerInterface
{
    public function isQuoteDeletable(QuoteTransfer $quoteTransfer): bool;
}
