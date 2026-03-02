<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\SharedCart\Helper;

use Codeception\Module;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SpyQuoteCompanyUserEntityTransfer;
use Generated\Shared\Transfer\SpyQuotePermissionGroupEntityTransfer;
use Orm\Zed\Permission\Persistence\SpyPermissionQuery;
use Orm\Zed\SharedCart\Persistence\SpyQuoteCompanyUser;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroup;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroupToPermission;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class SharedCartHelper extends Module
{
    use LocatorHelperTrait;

    public function haveQuotePermissionGroup(string $name, array $permissionKeys): SpyQuotePermissionGroupEntityTransfer
    {
        $quotePermissionGroupEntity = new SpyQuotePermissionGroup();
        $quotePermissionGroupEntity->setName($name);

        foreach ($permissionKeys as $permissionKey) {
            $permissionEntity = SpyPermissionQuery::create()
                ->filterByKey($permissionKey)
                ->findOneOrCreate();

            $quotePermissionGroupToPermissionEntity = new SpyQuotePermissionGroupToPermission();
            $quotePermissionGroupToPermissionEntity
                ->setPermission($permissionEntity);

            $quotePermissionGroupEntity->addSpyQuotePermissionGroupToPermission($quotePermissionGroupToPermissionEntity);
        }

        $quotePermissionGroupEntity->save();

        $quotePermissionGroupEntityTransfer = new SpyQuotePermissionGroupEntityTransfer();
        $quotePermissionGroupEntityTransfer->fromArray($quotePermissionGroupEntity->toArray(), true);

        return $quotePermissionGroupEntityTransfer;
    }

    public function haveQuoteCompanyUser(
        CompanyUserTransfer $companyUserTransfer,
        QuoteTransfer $quoteTransfer,
        SpyQuotePermissionGroupEntityTransfer $spyQuotePermissionGroupEntityTransfer
    ): SpyQuoteCompanyUserEntityTransfer {
        $quoteCompanyUserEntity = new SpyQuoteCompanyUser();
        $quoteCompanyUserEntity
            ->setFkCompanyUser($companyUserTransfer->requireIdCompanyUser()->getIdCompanyUser())
            ->setFkQuote($quoteTransfer->requireIdQuote()->getIdQuote())
            ->setFkQuotePermissionGroup($spyQuotePermissionGroupEntityTransfer->requireIdQuotePermissionGroup()->getIdQuotePermissionGroup())
            ->save();

        $quoteCompanyUserEntityTransfer = new SpyQuoteCompanyUserEntityTransfer();
        $quoteCompanyUserEntityTransfer->fromArray($quoteCompanyUserEntity->toArray(), true);

        return $quoteCompanyUserEntityTransfer;
    }
}
