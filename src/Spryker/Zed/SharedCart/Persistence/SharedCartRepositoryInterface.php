<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence;

use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\QuoteCompanyUserTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Generated\Shared\Transfer\ShareDetailCriteriaFilterTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer;

interface SharedCartRepositoryInterface
{
    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer;

    public function findPermissionsByCustomer(string $customerReference): PermissionCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer
     *
     * @return array<int>
     */
    public function getIsDefaultFlagForSharedCartsBySharedQuoteCriteriaFilter(SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer): array;

    /**
     * @param string $customerReference
     *
     * @return array<\Generated\Shared\Transfer\SpyCompanyUserEntityTransfer>
     */
    public function findShareInformationCustomer($customerReference): array;

    /**
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return array<\Generated\Shared\Transfer\QuotePermissionGroupTransfer>
     */
    public function findQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): array;

    /**
     * @param int $idQuote
     *
     * @return array<int>
     */
    public function findQuoteCompanyUserIdCollection(int $idQuote): array;

    public function getCustomerIdByReference(string $customerReference): string;

    public function isSharedQuoteDefault(int $idQuote, int $idCompanyUser): bool;

    /**
     * @param int $idQuote
     *
     * @return array<int>
     */
    public function findAllCompanyUserQuotePermissionGroupIdIndexes(int $idQuote): array;

    public function findShareDetailsByQuoteId(int $idQuote, bool $excludeAnonymizedCustomers = false): ShareDetailCollectionTransfer;

    public function findQuotePermissionGroupById(int $idQuotePermissionGroup): ?QuotePermissionGroupTransfer;

    public function getShareDetailCollectionByShareDetailCriteria(
        ShareDetailCriteriaFilterTransfer $shareDetailCriteriaFilterTransfer
    ): ShareDetailCollectionTransfer;

    public function findQuoteCompanyUserByUuid(string $quoteCompanyUserUuid): ?QuoteCompanyUserTransfer;

    public function findShareDetailByIdQuoteAndIdCompanyUser(int $idQuote, int $idCompanyUser): ?ShareDetailTransfer;

    /**
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     * @param bool $excludeAnonymizedCustomers
     *
     * @return array<\Generated\Shared\Transfer\ShareDetailCollectionTransfer>
     */
    public function getSharedCartDetails(ShareCartRequestTransfer $shareCartRequestTransfer, bool $excludeAnonymizedCustomers = false): array;

    public function getCustomerCollectionByQuote(int $idQuote): CustomerCollectionTransfer;
}
