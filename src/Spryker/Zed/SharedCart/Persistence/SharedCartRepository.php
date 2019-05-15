<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence;

use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer;
use Orm\Zed\CompanyUser\Persistence\Map\SpyCompanyUserTableMap;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Quote\Persistence\Map\SpyQuoteTableMap;
use Orm\Zed\SharedCart\Persistence\Map\SpyQuoteCompanyUserTableMap;
use Orm\Zed\SharedCart\Persistence\Map\SpyQuotePermissionGroupTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Spryker\Shared\SharedCart\SharedCartConfig;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartPersistenceFactory getFactory()
 */
class SharedCartRepository extends AbstractRepository implements SharedCartRepositoryInterface
{
    /**
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer
    {
        $permissionCollectionTransfer = new PermissionCollectionTransfer();

        $ownQuoteIdCollection = $this->findOwnQuotes($idCompanyUser);

        $permissionEntities = $this->getFactory()
            ->createPermissionQuery()
            ->joinSpyQuotePermissionGroupToPermission()
            ->groupByIdPermission()
            ->find();

        foreach ($permissionEntities as $permissionEntity) {
            $sharedQuoteIdCollection = $this->getSharedQuoteIds($idCompanyUser, $permissionEntity->getIdPermission());

            $permissionTransfer = new PermissionTransfer();
            $permissionTransfer->fromArray($permissionEntity->toArray(), true);
            $permissionTransfer->setConfiguration([
                SharedCartConfig::PERMISSION_CONFIG_ID_QUOTE_COLLECTION => array_merge($ownQuoteIdCollection, $sharedQuoteIdCollection),
            ]);

            $permissionCollectionTransfer->addPermission($permissionTransfer);
        }

        return $permissionCollectionTransfer;
    }

    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByCustomer(string $customerReference): PermissionCollectionTransfer
    {
        $permissionCollectionTransfer = new PermissionCollectionTransfer();

        $ownQuoteIdCollection = $this->getFactory()
            ->createQuoteQuery()
            ->filterByCustomerReference($customerReference)
            ->select([SpyQuoteTableMap::COL_ID_QUOTE])
            ->find()
            ->toArray();

        $permissionEntities = $this->getFactory()
            ->createPermissionQuery()
            ->joinSpyQuotePermissionGroupToPermission()
            ->groupByIdPermission()
            ->find();

        foreach ($permissionEntities as $permissionEntity) {
            $permissionTransfer = new PermissionTransfer();
            $permissionTransfer->fromArray($permissionEntity->toArray(), true);
            $permissionTransfer->setConfiguration([
                SharedCartConfig::PERMISSION_CONFIG_ID_QUOTE_COLLECTION => $ownQuoteIdCollection,
            ]);

            $permissionCollectionTransfer->addPermission($permissionTransfer);
        }

        return $permissionCollectionTransfer;
    }

    /**
     * @module Quote
     *
     * @param \Generated\Shared\Transfer\SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer
     *
     * @return int[]
     */
    public function getIsDefaultFlagForSharedCartsBySharedQuoteCriteriaFilter(SharedQuoteCriteriaFilterTransfer $sharedQuoteCriteriaFilterTransfer): array
    {
        $sharedQuoteCriteriaFilterTransfer->requireIdCompanyUser();

        $quoteQuery = $this->getFactory()->createQuoteCompanyUserQuery()
            ->filterByFkCompanyUser($sharedQuoteCriteriaFilterTransfer->getIdCompanyUser())
            ->useSpyQuoteQuery()
                ->filterByFkStore($sharedQuoteCriteriaFilterTransfer->getIdStore())
            ->endUse()
            ->select([
                SpyQuoteCompanyUserTableMap::COL_FK_QUOTE,
                SpyQuoteCompanyUserTableMap::COL_IS_DEFAULT,
            ]);

        return $quoteQuery->find()
            ->toKeyValue(SpyQuoteCompanyUserTableMap::COL_FK_QUOTE, SpyQuoteCompanyUserTableMap::COL_IS_DEFAULT);
    }

    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\SpyCompanyUserEntityTransfer[]
     */
    public function findShareInformationCustomer($customerReference): array
    {
        $companyUserQuery = $this->getFactory()
            ->createCompanyUserQuery()
            ->useSpyQuoteCompanyUserQuery()
                ->useSpyQuoteQuery()
                    ->filterByCustomerReference($customerReference)
                ->endUse()
            ->endUse()
            ->joinWithCustomer()
            ->joinWithSpyQuoteCompanyUser();
        return $this->buildQueryFromCriteria($companyUserQuery)->find();
    }

    /**
     * @param \Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer[]
     */
    public function findQuotePermissionGroupList(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): array
    {
        $quotePermissionGroupQuery = $this->getFactory()
            ->createQuotePermissionGroupQuery();
        $modifiedParams = $criteriaFilterTransfer->modifiedToArray(true, true);

        if (isset($modifiedParams[QuotePermissionGroupCriteriaFilterTransfer::IS_DEFAULT])) {
            $quotePermissionGroupQuery->filterByIsDefault($modifiedParams[QuotePermissionGroupCriteriaFilterTransfer::IS_DEFAULT]);
        }

        if (isset($modifiedParams[QuotePermissionGroupCriteriaFilterTransfer::NAME])) {
            $quotePermissionGroupQuery->filterByName($modifiedParams[QuotePermissionGroupCriteriaFilterTransfer::NAME]);
        }

        $quotePermissionGroupEntityTransferList = $this->buildQueryFromCriteria($quotePermissionGroupQuery, $criteriaFilterTransfer->getFilter())->find();

        if (!count($quotePermissionGroupEntityTransferList)) {
            return [];
        }

        return $this->mapQuotePermissionGroupList($quotePermissionGroupEntityTransferList);
    }

    /**
     * @param int $idQuote
     *
     * @return int[]
     */
    public function findQuoteCompanyUserIdCollection(int $idQuote): array
    {
        return $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkQuote($idQuote)
            ->select([SpyQuoteCompanyUserTableMap::COL_ID_QUOTE_COMPANY_USER])
            ->find()
            ->toArray();
    }

    /**
     * @param int $idQuote
     *
     * @return int[]
     */
    public function findAllCompanyUserQuotePermissionGroupIdIndexes(int $idQuote): array
    {
        $storedQuotePermissionGroupIdIndexes = $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkQuote($idQuote)
            ->select([
                SpyQuoteCompanyUserTableMap::COL_ID_QUOTE_COMPANY_USER,
                SpyQuoteCompanyUserTableMap::COL_FK_QUOTE_PERMISSION_GROUP,
            ])
            ->find()
            ->toArray();

        $mappedQuotePermissionGroupIdIndexes = $this->mapStoredQuotePermissionGroupIdIndexesToAssociativeArray(
            $storedQuotePermissionGroupIdIndexes
        );

        return $mappedQuotePermissionGroupIdIndexes;
    }

    /**
     * @param array $storedQuotePermissionGroupIdIndexes
     *
     * @return int[]
     */
    protected function mapStoredQuotePermissionGroupIdIndexesToAssociativeArray(array $storedQuotePermissionGroupIdIndexes): array
    {
        $mappedQuotePermissionGroupIdIndexes = [];
        foreach ($storedQuotePermissionGroupIdIndexes as $storedQuotePermissionGroupIdIndex) {
            $idQuoteCompanyUser = $storedQuotePermissionGroupIdIndex[SpyQuoteCompanyUserTableMap::COL_ID_QUOTE_COMPANY_USER];
            $mappedQuotePermissionGroupIdIndexes[$idQuoteCompanyUser] = $storedQuotePermissionGroupIdIndex[SpyQuoteCompanyUserTableMap::COL_FK_QUOTE_PERMISSION_GROUP];
        }

        return $mappedQuotePermissionGroupIdIndexes;
    }

    /**
     * @param int $idCompanyUser
     *
     * @return array
     */
    protected function findOwnQuotes(int $idCompanyUser): array
    {
        $join = new Join(SpyCustomerTableMap::COL_ID_CUSTOMER, SpyCompanyUserTableMap::COL_FK_CUSTOMER);

        return $this->getFactory()
            ->createQuoteQuery()
            ->addJoin(SpyQuoteTableMap::COL_CUSTOMER_REFERENCE, SpyCustomerTableMap::COL_CUSTOMER_REFERENCE)
            ->addJoinObject($join, 'customerJoin')
            ->addJoinCondition('customerJoin', sprintf('%s = %d', SpyCompanyUserTableMap::COL_ID_COMPANY_USER, $idCompanyUser))
            ->select([SpyQuoteTableMap::COL_ID_QUOTE])
            ->find()
            ->toArray();
    }

    /**
     * @param int $idCompanyUser
     * @param int $idPermission
     *
     * @return array
     */
    protected function getSharedQuoteIds(int $idCompanyUser, int $idPermission): array
    {
        return $this->getFactory()
            ->createQuoteQuery()
            ->useSpyQuoteCompanyUserQuery()
                ->filterByFkCompanyUser($idCompanyUser)
                ->useSpyQuotePermissionGroupQuery()
                    ->useSpyQuotePermissionGroupToPermissionQuery()
                        ->filterByFkPermission($idPermission)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->groupByIdQuote()
            ->select([SpyQuoteTableMap::COL_ID_QUOTE])
            ->find()
            ->toArray();
    }

    /**
     * @param \Generated\Shared\Transfer\SpyQuotePermissionGroupEntityTransfer[] $quotePermissionGroupEntityTransferList
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer[]
     */
    protected function mapQuotePermissionGroupList(array $quotePermissionGroupEntityTransferList): array
    {
        $quotePermissionGroupTransferList = [];
        $mapper = $this->getFactory()->createQuotePermissionGroupMapper();
        foreach ($quotePermissionGroupEntityTransferList as $quotePermissionGroupEntityTransfer) {
            $quotePermissionGroupTransferList[] = $mapper->mapQuotePermissionGroup($quotePermissionGroupEntityTransfer);
        }
        return $quotePermissionGroupTransferList;
    }

    /**
     * @param string $customerReference
     *
     * @return string
     */
    public function getCustomerIdByReference(string $customerReference): string
    {
        return $this->getFactory()
            ->createSpyCustomerQuery()
            ->filterByCustomerReference($customerReference)
            ->select([SpyCustomerTableMap::COL_ID_CUSTOMER])
            ->findOne()
            ->getIdCustomer();
    }

    /**
     * @param int $idQuote
     * @param int $idCompanyUser
     *
     * @return bool
     */
    public function isSharedQuoteDefault(int $idQuote, int $idCompanyUser): bool
    {
        return (bool)$this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkQuote($idQuote)
            ->filterByFkCompanyUser($idCompanyUser)->count();
    }

    /**
     * @param int $idQuote
     *
     * @return \Generated\Shared\Transfer\ShareDetailCollectionTransfer
     */
    public function findShareDetailsByQuoteId(int $idQuote): ShareDetailCollectionTransfer
    {
        $quoteCompanyUserQuery = $this->getFactory()
            ->createQuoteCompanyUserQuery();
        $quoteCompanyUserQuery->filterByFkQuote($idQuote)
            ->joinWithSpyCompanyUser()
            ->useSpyCompanyUserQuery(null, Criteria::LEFT_JOIN)
                ->joinWithCustomer()
            ->endUse();
        $quoteCompanyUserEntities = $quoteCompanyUserQuery
            ->find();

        return $this->getFactory()
            ->createQuoteShareDetailMapper()
            ->mapShareDetailCollection($quoteCompanyUserEntities, $this->findQuotePermissionGroupList(new QuotePermissionGroupCriteriaFilterTransfer()));
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function findIdQuotePermissionGroupByName(string $name): ?int
    {
        return $this->getFactory()
            ->createQuotePermissionGroupQuery()
            ->filterByName($name)
            ->select(SpyQuotePermissionGroupTableMap::COL_ID_QUOTE_PERMISSION_GROUP)
            ->findOne();
    }

    /**
     * @param int $idQuote
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\ShareDetailTransfer|null
     */
    public function findShareDetailByIdQuoteAndIdCompanyUser(int $idQuote, int $idCompanyUser): ?ShareDetailTransfer
    {
        $quoteCompanyUserEntity = $this->getFactory()
            ->createQuoteCompanyUserQuery()
            ->filterByFkCompanyUser($idQuote)
            ->filterByFkQuote($idCompanyUser)
            ->findOne();

        if (!$quoteCompanyUserEntity) {
            return null;
        }

        return $this->getFactory()
            ->createQuoteShareDetailMapper()
            ->mapQuoteCompanyUserToShareDetailTransfer($quoteCompanyUserEntity);
    }
}
