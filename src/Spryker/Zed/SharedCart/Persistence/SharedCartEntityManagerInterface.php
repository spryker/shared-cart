<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence;

use Generated\Shared\Transfer\PermissionTransfer;
use Generated\Shared\Transfer\QuoteCompanyUserTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Generated\Shared\Transfer\SpyQuoteCompanyUserEntityTransfer;

interface SharedCartEntityManagerInterface
{
    public function savePermission(PermissionTransfer $permissionTransfer): PermissionTransfer;

    public function saveQuotePermissionGroup(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupTransfer;

    public function saveQuotePermissionGroupToPermission(
        QuotePermissionGroupTransfer $quotePermissionGroupTransfer,
        PermissionTransfer $permissionTransfer
    ): void;

    public function saveQuoteCompanyUser(SpyQuoteCompanyUserEntityTransfer $companyUserEntityTransfer): void;

    public function deleteQuoteCompanyUser(int $idQuoteCompanyUser): void;

    public function setQuoteDefault(int $idCompanyUser, int $idQuote): void;

    public function resetQuoteDefaultFlagByCustomer(int $idCompanyUser): void;

    public function deleteQuoteCompanyUserByQuote(QuoteTransfer $quoteTransfer): void;

    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void;

    public function createQuoteCompanyUser(QuoteCompanyUserTransfer $quoteCompanyUserTransfer): QuoteCompanyUserTransfer;

    public function updateCompanyUserQuotePermissionGroup(ShareDetailTransfer $shareDetailTransfer): void;

    public function updateQuoteCompanyUserQuotePermissionGroup(ShareDetailTransfer $shareDetailTransfer): ?QuoteCompanyUserTransfer;
}
