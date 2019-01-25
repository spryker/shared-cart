<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\Model;

use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use Generated\Shared\Transfer\SpyQuoteCompanyUserEntityTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface;
use Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface;

class QuoteCompanyUserWriter implements QuoteCompanyUserWriterInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface
     */
    protected $sharedCartRepository;

    /**
     * @var \Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface
     */
    protected $sharedCartEntityManager;

    /**
     * @param \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface $sharedCartRepository
     * @param \Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface $sharedCartEntityManager
     */
    public function __construct(SharedCartRepositoryInterface $sharedCartRepository, SharedCartEntityManagerInterface $sharedCartEntityManager)
    {
        $this->sharedCartRepository = $sharedCartRepository;
        $this->sharedCartEntityManager = $sharedCartEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function updateQuoteCompanyUsers(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($quoteTransfer) {
            return $this->executeUpdateQuoteCompanyUsersTransaction($quoteTransfer);
        });
    }

    /**
     * @param int $idCompanyUser
     *
     * @return void
     */
    public function deleteShareRelationsForCompanyUserId(int $idCompanyUser): void
    {
        $this->sharedCartEntityManager
            ->deleteShareRelationsForCompanyUserId($idCompanyUser);
    }

    /**
     * @param int $idQuote
     * @param int $idCompanyUser
     * @param string $permissionGroupName
     *
     * @return void
     */
    public function shareQuoteWithCompanyUser(int $idQuote, int $idCompanyUser, string $permissionGroupName): void
    {
        $shareDetailTransfer = new ShareDetailTransfer();
        $shareDetailTransfer->setIdCompanyUser($idCompanyUser);
        $shareDetailTransfer->setQuotePermissionGroup(
            $this->getPermissionGroupByName($permissionGroupName)
        );

        $this->createNewQuoteCompanyUser($idQuote, $shareDetailTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function executeUpdateQuoteCompanyUsersTransaction(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $currentQuoteCompanyUserIdCollection = $this->sharedCartRepository->findQuoteCompanyUserIdCollection($quoteTransfer->getIdQuote());
        $this->addNewQuoteCompanyUsers($quoteTransfer);
        $this->updateExistingQuoteCompanyUsers($quoteTransfer, $currentQuoteCompanyUserIdCollection);
        $this->removeQuoteCompanyUsers((array)$quoteTransfer->getShareDetails(), $currentQuoteCompanyUserIdCollection);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param int[] $storedQuoteCompanyUserIdIndexes
     *
     * @return void
     */
    protected function updateExistingQuoteCompanyUsers(
        QuoteTransfer $quoteTransfer,
        array $storedQuoteCompanyUserIdIndexes
    ): void {
        $quoteShareDetails = $quoteTransfer->getShareDetails();
        $formQuoteCompanyUserIdIndexes = $this->indexQuoteCompanyUserId((array)$quoteShareDetails);

        $commonQuoteCompanyUserIdIndexes = array_intersect(
            $formQuoteCompanyUserIdIndexes,
            $storedQuoteCompanyUserIdIndexes
        );

        $quoteTransfer->requireIdQuote();
        $storedQuotePermissionGroupIdIndexes = $this->sharedCartRepository->findAllCompanyUserQuotePermissionGroupIdIndexes(
            $quoteTransfer->getIdQuote()
        );

        foreach ($quoteShareDetails as $shareDetailTransfer) {
            $this->updateCompanyUserQuotePermissionGroup($shareDetailTransfer, $commonQuoteCompanyUserIdIndexes, $storedQuotePermissionGroupIdIndexes);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ShareDetailTransfer $shareDetailTransfer
     * @param int[] $commonQuoteCompanyUserIdIndexes
     * @param int[] $storedQuotePermissionGroupIdIndexes
     *
     * @return void
     */
    protected function updateCompanyUserQuotePermissionGroup(
        ShareDetailTransfer $shareDetailTransfer,
        array $commonQuoteCompanyUserIdIndexes,
        array $storedQuotePermissionGroupIdIndexes
    ): void {
        if (!$shareDetailTransfer->getIdQuoteCompanyUser()) {
            return;
        }

        $shareDetailTransfer->requireIdCompanyUser()
            ->requireQuotePermissionGroup();

        if (in_array($shareDetailTransfer->getIdQuoteCompanyUser(), $commonQuoteCompanyUserIdIndexes, false)
            && $this->isQuotePermissionGroupChanged($shareDetailTransfer, $storedQuotePermissionGroupIdIndexes)
        ) {
            $this->sharedCartEntityManager->updateCompanyUserQuotePermissionGroup($shareDetailTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ShareDetailTransfer $shareDetailTransfer
     * @param int[] $storedQuotePermissionGroupIdIndexes
     *
     * @return bool
     */
    protected function isQuotePermissionGroupChanged(
        ShareDetailTransfer $shareDetailTransfer,
        array $storedQuotePermissionGroupIdIndexes
    ): bool {
        return $shareDetailTransfer->getQuotePermissionGroup()->getIdQuotePermissionGroup()
            !== $storedQuotePermissionGroupIdIndexes[$shareDetailTransfer->getIdQuoteCompanyUser()];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addNewQuoteCompanyUsers(QuoteTransfer $quoteTransfer): void
    {
        $defaultPermissionGroupTransfer = $this->getDefaultPermissionGroup();
        foreach ($quoteTransfer->getShareDetails() as $shareDetailTransfer) {
            if ($shareDetailTransfer->getIdQuoteCompanyUser()) {
                continue;
            }

            if (!$shareDetailTransfer->getQuotePermissionGroup()) {
                $shareDetailTransfer->setQuotePermissionGroup($defaultPermissionGroupTransfer);
            }

            $this->createNewQuoteCompanyUser($quoteTransfer->getIdQuote(), $shareDetailTransfer);
        }
    }

    /**
     * @param int $idQuote
     * @param \Generated\Shared\Transfer\ShareDetailTransfer $shareDetailTransfer
     *
     * @return void
     */
    protected function createNewQuoteCompanyUser(int $idQuote, ShareDetailTransfer $shareDetailTransfer): void
    {
        $companyUserEntityTransfer = new SpyQuoteCompanyUserEntityTransfer();
        $companyUserEntityTransfer
            ->setFkCompanyUser($shareDetailTransfer->getIdCompanyUser())
            ->setFkQuote($idQuote)
            ->setFkQuotePermissionGroup(
                $shareDetailTransfer->getQuotePermissionGroup()->getIdQuotePermissionGroup()
            );

        $this->sharedCartEntityManager->saveQuoteCompanyUser($companyUserEntityTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer
     */
    protected function getDefaultPermissionGroup(): QuotePermissionGroupTransfer
    {
        $criteriaFilterTransfer = new QuotePermissionGroupCriteriaFilterTransfer();
        $criteriaFilterTransfer->setIsDefault(true);
        $permissionGroupTransferCollection = $this->sharedCartRepository->findQuotePermissionGroupList($criteriaFilterTransfer);

        return reset($permissionGroupTransferCollection);
    }

    /**
     * @param string $permissionGroupName
     *
     * @return \Generated\Shared\Transfer\QuotePermissionGroupTransfer
     */
    protected function getPermissionGroupByName(string $permissionGroupName): QuotePermissionGroupTransfer
    {
        $criteriaFilterTransfer = new QuotePermissionGroupCriteriaFilterTransfer();
        $criteriaFilterTransfer->setName($permissionGroupName);
        $permissionGroupTransferCollection = $this->sharedCartRepository->findQuotePermissionGroupList($criteriaFilterTransfer);

        return reset($permissionGroupTransferCollection);
    }

    /**
     * @param \Generated\Shared\Transfer\ShareDetailTransfer[] $shareDetailTransferCollection
     * @param int[] $currentQuoteCompanyUserIdCollection
     *
     * @return void
     */
    protected function removeQuoteCompanyUsers(array $shareDetailTransferCollection, array $currentQuoteCompanyUserIdCollection): void
    {
        $quoteCompanyUserIdIndex = $this->indexQuoteCompanyUserId($shareDetailTransferCollection);
        foreach ($currentQuoteCompanyUserIdCollection as $idQuoteCompanyUser) {
            if (!in_array($idQuoteCompanyUser, $quoteCompanyUserIdIndex)) {
                $this->sharedCartEntityManager->deleteQuoteCompanyUser($idQuoteCompanyUser);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ShareDetailTransfer[] $shareDetailTransferCollection
     *
     * @return array
     */
    protected function indexQuoteCompanyUserId(array $shareDetailTransferCollection): array
    {
        $quoteCompanyUserIdIndex = [];
        foreach ($shareDetailTransferCollection as $shareDetailTransfer) {
            if ($shareDetailTransfer->getIdQuoteCompanyUser()) {
                $quoteCompanyUserIdIndex[] = $shareDetailTransfer->getIdQuoteCompanyUser();
            }
        }

        return $quoteCompanyUserIdIndex;
    }
}
