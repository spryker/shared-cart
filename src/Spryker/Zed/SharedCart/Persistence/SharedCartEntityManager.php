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
use Orm\Zed\SharedCart\Persistence\Map\SpyQuoteCompanyUserTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartPersistenceFactory getFactory()
 */
class SharedCartEntityManager extends AbstractEntityManager implements SharedCartEntityManagerInterface
{
    public function savePermission(PermissionTransfer $permissionTransfer): PermissionTransfer
    {
        $permissionEntity = $this->getFactory()
            ->createPermissionQuery()
            ->filterByKey($permissionTransfer->getKey())
            ->findOneOrCreate();

        $permissionEntity->fromArray($permissionTransfer->modifiedToArray());
        $permissionEntity->save();

        $permissionTransfer->fromArray($permissionEntity->toArray(), true);

        return $permissionTransfer;
    }

    public function saveQuotePermissionGroup(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupTransfer
    {
        $quotePermissionGroupEntity = $this->getFactory()
            ->createQuotePermissionGroupQuery()
            ->filterByName($quotePermissionGroupTransfer->getName())
            ->findOneOrCreate();

        $quotePermissionGroupEntity->fromArray($quotePermissionGroupTransfer->modifiedToArray());
        $quotePermissionGroupEntity->save();

        $quotePermissionGroupTransfer->fromArray($quotePermissionGroupEntity->toArray(), true);

        return $quotePermissionGroupTransfer;
    }

    public function saveQuotePermissionGroupToPermission(
        QuotePermissionGroupTransfer $quotePermissionGroupTransfer,
        PermissionTransfer $permissionTransfer
    ): void {
        $this->getFactory()
            ->createQuotePermissionGroupToPermissionQuery()
            ->filterByFkQuotePermissionGroup($quotePermissionGroupTransfer->getIdQuotePermissionGroup())
            ->filterByFkPermission($permissionTransfer->getIdPermission())
            ->findOneOrCreate()
            ->save();
    }

    public function saveQuoteCompanyUser(SpyQuoteCompanyUserEntityTransfer $companyUserEntityTransfer): void
    {
        $this->save($companyUserEntityTransfer);
    }

    public function deleteQuoteCompanyUser(int $idQuoteCompanyUser): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByIdQuoteCompanyUser($idQuoteCompanyUser)
            ->delete();
    }

    public function setQuoteDefault(int $idCompanyUser, int $idQuote): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkQuote($idQuote)
            ->filterByFkCompanyUser($idCompanyUser)
            ->update(['IsDefault' => true]);
    }

    public function resetQuoteDefaultFlagByCustomer(int $idCompanyUser): void
    {
        $idsQuoteCompanyUser = $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkCompanyUser($idCompanyUser)
            ->filterByIsDefault(true)
            ->select(SpyQuoteCompanyUserTableMap::COL_ID_QUOTE_COMPANY_USER)
            ->find()
            ->toArray();

        if (!$idsQuoteCompanyUser) {
            return;
        }

        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByIdQuoteCompanyUser($idsQuoteCompanyUser, Criteria::IN)
            ->update(['IsDefault' => false]);
    }

    public function deleteQuoteCompanyUserByQuote(QuoteTransfer $quoteTransfer): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkQuote($quoteTransfer->getIdQuote())
            ->delete();
    }

    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void
    {
        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkCompanyUser($idCompanyUser)
            ->delete();
    }

    public function createQuoteCompanyUser(QuoteCompanyUserTransfer $quoteCompanyUserTransfer): QuoteCompanyUserTransfer
    {
        $quoteCompanyUserEntity = $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkCompanyUser($quoteCompanyUserTransfer->getFkCompanyUser())
            ->filterByFkQuotePermissionGroup($quoteCompanyUserTransfer->getIdQuoteCompanyUser())
            ->filterByFkQuote($quoteCompanyUserTransfer->getFkQuote())
            ->findOneOrCreate();

        $quoteCompanyUserEntity = $this->getFactory()->createQuoteCompanyUserMapper()
            ->mapQuoteCompanyUserTransferToQuoteCompanyUserEntity($quoteCompanyUserTransfer, $quoteCompanyUserEntity);

        $quoteCompanyUserEntity->save();

        return $this->getFactory()->createQuoteCompanyUserMapper()
            ->mapQuoteCompanyUserEntityToQuoteCompanyUserTransfer($quoteCompanyUserEntity, new QuoteCompanyUserTransfer());
    }

    public function updateCompanyUserQuotePermissionGroup(ShareDetailTransfer $shareDetailTransfer): void
    {
        $quotePermissionGroupTransfer = $shareDetailTransfer->getQuotePermissionGroup();

        $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByIdQuoteCompanyUser($shareDetailTransfer->getIdQuoteCompanyUser())
            ->update([
                ucfirst(SpyQuoteCompanyUserEntityTransfer::FK_QUOTE_PERMISSION_GROUP) => $quotePermissionGroupTransfer->getIdQuotePermissionGroup(),
            ]);
    }

    public function updateQuoteCompanyUserQuotePermissionGroup(ShareDetailTransfer $shareDetailTransfer): ?QuoteCompanyUserTransfer
    {
        $quoteCompanyUserEntity = $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByIdQuoteCompanyUser($shareDetailTransfer->getIdQuoteCompanyUser())
            ->findOne();

        if (!$quoteCompanyUserEntity) {
            return null;
        }

        $quoteCompanyUserEntity->setFkQuotePermissionGroup($shareDetailTransfer->getQuotePermissionGroup()->getIdQuotePermissionGroup());
        $quoteCompanyUserEntity->save();

        return $this->getFactory()->createQuoteCompanyUserMapper()
            ->mapQuoteCompanyUserEntityToQuoteCompanyUserTransfer($quoteCompanyUserEntity, new QuoteCompanyUserTransfer());
    }
}
