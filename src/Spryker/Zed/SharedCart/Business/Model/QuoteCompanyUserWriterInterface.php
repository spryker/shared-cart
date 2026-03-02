<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\Model;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareCartResponseTransfer;

interface QuoteCompanyUserWriterInterface
{
    public function updateQuoteCompanyUsers(QuoteTransfer $quoteTransfer): QuoteTransfer;

    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void;

    public function addQuoteCompanyUser(ShareCartRequestTransfer $shareCartRequestTransfer): void;

    public function createQuoteCompanyUser(ShareCartRequestTransfer $shareCartRequestTransfer): ShareCartResponseTransfer;

    public function updateQuoteCompanyUserPermissionGroup(ShareCartRequestTransfer $shareCartRequestTransfer): ShareCartResponseTransfer;

    public function deleteQuoteCompanyUser(ShareCartRequestTransfer $shareCartRequestTransfer): void;
}
