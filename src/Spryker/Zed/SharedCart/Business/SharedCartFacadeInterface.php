<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer;

interface SharedCartFacadeInterface
{
    /**
     * Specification:
     * - Get permissions for customer company user.
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByIdCompanyUser($idCompanyUser): PermissionCollectionTransfer;

    /**
     * Specification:
     * - Adds customer shared cart filtered by store to QuoteResponseTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function expandQuoteResponseWithSharedCarts(QuoteResponseTransfer $quoteResponseTransfer): QuoteResponseTransfer;

    /**
     * Specification:
     * - Add base shared quote permission group list to database.
     *
     * @api
     *
     * @return void
     */
    public function installSharedCartPermissions(): void;

    /**
     * Specification:
     * - Get filtered quote permission groups list.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer
     */
    public function getQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): QuotePermissionGroupResponseTransfer;

    /**
     * Specification:
     * - Update quote share details for quote.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function updateQuoteShareDetails(QuoteTransfer $quoteTransfer): QuoteTransfer;

    /**
     * Specification:
     * - Reset is_default flag for all quotes shared with customer.
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return void
     */
    public function resetQuoteDefaultFlagByCustomer(int $idCompanyUser): void;

    /**
     * Specification:
     * - Mark share connection for quote and customer as default.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function quoteSetDefault(QuoteTransfer $quoteTransfer): QuoteTransfer;

    /**
     * Specification:
     * - Remove all share connection for quote.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function deleteShareForQuote(QuoteTransfer $quoteTransfer): void;

    /**
     * Specification:
     *  - Add quote permissions for customer company user to customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expandCustomer(CustomerTransfer $customerTransfer): CustomerTransfer;

    /**
     * Specification:
     *  - Checks if shared cart default for company user.
     *
     * @api
     *
     * @param int $idQuote
     * @param int $idCompanyUser
     *
     * @return bool
     */
    public function isSharedQuoteDefault(int $idQuote, int $idCompanyUser): bool;

    /**
     * Specification:
     *  - Returns share details collection of quote by quote id.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShareDetailCollectionTransfer
     */
    public function getShareDetailsByIdQuote(QuoteTransfer $quoteTransfer): ShareDetailCollectionTransfer;

    /**
     * Specification:
     *  - Un-shares quotes for company user.
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return void
     */
    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void;

    /**
     * Specification:
     *  - Shares cart to company user with permission group.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return void
     */
    public function addQuoteCompanyUser(ShareCartRequestTransfer $shareCartRequestTransfer): void;

    /**
     * Specification:
     *  - Finds quote permission group by id.
     *  - Requires idQuotePermissionGroup field to be set in QuotePermissionGroupTransfer.
     *  - If quote permission group not found, returns QuotePermissionGroupResponseTransfer with `isSuccess=false`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuotePermissionGroupTransfer $quotePermissionGroupTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer
     */
    public function findQuotePermissionGroupById(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupResponseTransfer;

    /**
     * Specification:
     * - Returns a collection of quotes shared with the customer.
     * - Requires CompanyUser::idCompanyUser to be set on the CustomerTransfer taken as a parameter.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function getCustomerSharedQuoteCollection(SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer): QuoteCollectionTransfer;

    /**
     * Specification:
     * - Expands a collection of quotes with collection of quotes shared with the customer.
     * - Requires CompanyUser::idCompanyUser to be set on the CustomerTransfer taken as a parameter.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteCollectionTransfer $quoteCollectionTransfer
     * @param \Generated\Shared\Transfer\QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function expandQuoteCollectionWithCustomerSharedQuoteCollection(
        QuoteCollectionTransfer $quoteCollectionTransfer,
        QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
    ): QuoteCollectionTransfer;
}
