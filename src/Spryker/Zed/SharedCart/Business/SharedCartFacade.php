<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\SharedCart\Business\SharedCartBusinessFactory getFactory()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface getRepository()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface getEntityManager()
 */
class SharedCartFacade extends AbstractFacade implements SharedCartFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByIdCompanyUser($idCompanyUser): PermissionCollectionTransfer
    {
        return $this->getRepository()->findPermissionsByIdCompanyUser($idCompanyUser);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function expandQuoteResponseWithSharedCarts(QuoteResponseTransfer $quoteResponseTransfer): QuoteResponseTransfer
    {
        return $this->getFactory()
            ->createQuoteResponseExpander()
            ->expand($quoteResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return void
     */
    public function installSharedCartPermissions(): void
    {
        $this->getFactory()
            ->createQuotePermissionGroupInstaller()
            ->install();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer
     */
    public function getQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): QuotePermissionGroupResponseTransfer
    {
        return $this->getFactory()->createQuotePermissionGroupReader()->getQuotePermissionGroupList($criteriaFilterTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function updateQuoteShareDetails(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->getFactory()->createQuoteCompanyUserWriter()->updateQuoteCompanyUsers($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return void
     */
    public function resetQuoteDefaultFlagByCustomer(int $idCompanyUser): void
    {
        $this->getEntityManager()->resetQuoteDefaultFlagByCustomer($idCompanyUser);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function quoteSetDefault(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->getFactory()->createQuoteActivator()->setDefaultQuote($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function deleteShareForQuote(QuoteTransfer $quoteTransfer): void
    {
        $this->getEntityManager()->deleteQuoteCompanyUserByQuote($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expandCustomer(CustomerTransfer $customerTransfer): CustomerTransfer
    {
        return $this->getFactory()->createCustomerExpander()->expandCustomer($customerTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idQuote
     * @param int $idCompanyUser
     *
     * @return bool
     */
    public function isSharedQuoteDefault(int $idQuote, int $idCompanyUser): bool
    {
        return $this->getRepository()->isSharedQuoteDefault($idQuote, $idCompanyUser);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShareDetailCollectionTransfer
     */
    public function getShareDetailsByIdQuote(QuoteTransfer $quoteTransfer): ShareDetailCollectionTransfer
    {
        return $this->getFactory()
            ->createQuoteShareDetailsReader()
            ->getShareDetailsByIdQuote($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return void
     */
    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserWriter()
            ->deleteShareRelationsForCompanyUserId($idCompanyUser);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ShareCartRequestTransfer $shareCartRequestTransfer
     *
     * @return void
     */
    public function addQuoteCompanyUser(ShareCartRequestTransfer $shareCartRequestTransfer): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserWriter()
            ->addQuoteCompanyUser($shareCartRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuotePermissionGroupTransfer $quotePermissionGroupTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer
     */
    public function findQuotePermissionGroupById(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupResponseTransfer
    {
        return $this->getFactory()
            ->createQuotePermissionGroupReader()
            ->findQuotePermissionGroupById($quotePermissionGroupTransfer);
    }
}
