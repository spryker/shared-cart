<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence;

use Orm\Zed\CompanyUser\Persistence\SpyCompanyUserQuery;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Orm\Zed\Permission\Persistence\SpyPermissionQuery;
use Orm\Zed\Quote\Persistence\SpyQuoteQuery;
use Orm\Zed\SharedCart\Persistence\SpyQuoteCompanyUserQuery;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroupQuery;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroupToPermissionQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\CustomerMapper;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\QuoteCompanyUserMapper;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\QuotePermissionGroupMapper;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\QuotePermissionGroupMapperInterface;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\QuoteShareDetailMapper;
use Spryker\Zed\SharedCart\Persistence\Propel\Mapper\QuoteShareDetailMapperInterface;

/**
 * @method \Spryker\Zed\SharedCart\SharedCartConfig getConfig()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface getRepository()
 */
class SharedCartPersistenceFactory extends AbstractPersistenceFactory
{
    public function createPermissionQuery(): SpyPermissionQuery
    {
        return SpyPermissionQuery::create();
    }

    public function createQuoteQuery(): SpyQuoteQuery
    {
        return SpyQuoteQuery::create();
    }

    public function createQuotePermissionGroupQuery(): SpyQuotePermissionGroupQuery
    {
        return SpyQuotePermissionGroupQuery::create();
    }

    public function createQuoteCompanyUserQuery(): SpyQuoteCompanyUserQuery
    {
        return SpyQuoteCompanyUserQuery::create();
    }

    public function createQuotePermissionGroupToPermissionQuery(): SpyQuotePermissionGroupToPermissionQuery
    {
        return SpyQuotePermissionGroupToPermissionQuery::create();
    }

    public function createCompanyUserQuery(): SpyCompanyUserQuery
    {
        return SpyCompanyUserQuery::create();
    }

    public function createSpyCustomerQuery(): SpyCustomerQuery
    {
        return SpyCustomerQuery::create();
    }

    public function createQuotePermissionGroupMapper(): QuotePermissionGroupMapperInterface
    {
        return new QuotePermissionGroupMapper();
    }

    public function createQuoteShareDetailMapper(): QuoteShareDetailMapperInterface
    {
        return new QuoteShareDetailMapper();
    }

    public function createQuoteCompanyUserMapper(): QuoteCompanyUserMapper
    {
        return new QuoteCompanyUserMapper();
    }

    public function createCustomerMapper(): CustomerMapper
    {
        return new CustomerMapper();
    }
}
