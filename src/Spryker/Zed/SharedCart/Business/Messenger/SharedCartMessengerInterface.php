<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\Messenger;

use Generated\Shared\Transfer\QuoteTransfer;

interface SharedCartMessengerInterface
{
    public function addDefaultSharedQuoteChangedMessage(QuoteTransfer $quoteTransfer): void;
}
