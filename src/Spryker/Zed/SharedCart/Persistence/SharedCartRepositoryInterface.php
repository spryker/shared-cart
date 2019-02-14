<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence;

use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer;

interface SharedCartRepositoryInterface
{
    /**
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer;

    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByCustomer(string $customerReference): PermissionCollectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\SpyQuoteEntityTransfer[]
     */
    public function findQuotesBySharedQuoteCriteriaFilter(SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer): array;

    /**
     * @param int $idCompanyUser
     *
     * @return int[]
     */
    public function getIsDefaultFlagForSharedCartsByIdCompanyUser(int $idCompanyUser): array;

    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\SpyCompanyUserEntityTransfer[]
     */
    public function findShareInformationCustomer($customerReference): array;

    /**
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer[]
     */
    public function findQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): array;

    /**
     * @param int $idQuote
     *
     * @return int[]
     */
    public function findQuoteCompanyUserIdCollection(int $idQuote): array;

    /**
     * @param string $customerReference
     *
     * @return string
     */
    public function getCustomerIdByReference(string $customerReference): string;

    /**
     * @param int $idQuote
     * @param int $idCompanyUser
     *
     * @return bool
     */
    public function isSharedQuoteDefault(int $idQuote, int $idCompanyUser): bool;

    /**
     * @param int $idQuote
     *
     * @return int[]
     */
    public function findAllCompanyUserQuotePermissionGroupIdIndexes(int $idQuote): array;

    /**
     * @param int $idQuote
     *
     * @return \Generated\Shared\Transfer\ShareDetailCollectionTransfer
     */
    public function findShareDetailsByQuoteId(int $idQuote): ShareDetailCollectionTransfer;
}
