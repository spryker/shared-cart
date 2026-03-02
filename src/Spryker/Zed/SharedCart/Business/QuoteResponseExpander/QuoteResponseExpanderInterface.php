<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\QuoteResponseExpander;

use Generated\Shared\Transfer\QuoteResponseTransfer;

interface QuoteResponseExpanderInterface
{
    public function expand(QuoteResponseTransfer $quoteResponseTransfer): QuoteResponseTransfer;
}
