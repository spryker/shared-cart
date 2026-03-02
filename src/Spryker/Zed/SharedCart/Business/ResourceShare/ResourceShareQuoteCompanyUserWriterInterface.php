<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\ResourceShare;

use Generated\Shared\Transfer\ResourceShareRequestTransfer;
use Generated\Shared\Transfer\ResourceShareResponseTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;

interface ResourceShareQuoteCompanyUserWriterInterface
{
    public function createCartShareForCompanyUser(ResourceShareRequestTransfer $resourceShareRequestTransfer): ResourceShareResponseTransfer;

    public function updateCartShareForCompanyUser(
        ResourceShareRequestTransfer $resourceShareRequestTransfer,
        ShareDetailTransfer $shareDetailTransfer
    ): ResourceShareResponseTransfer;
}
