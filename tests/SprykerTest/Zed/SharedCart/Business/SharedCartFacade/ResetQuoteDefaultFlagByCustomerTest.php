<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\SharedCart\Business\SharedCartFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Propel\Runtime\Propel;
use Spryker\Zed\SharedCart\Communication\Plugin\ReadSharedCartPermissionPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group SharedCart
 * @group Business
 * @group SharedCartFacade
 * @group ResetQuoteDefaultFlagByCustomerTest
 * Add your own group annotations below this line
 */
class ResetQuoteDefaultFlagByCustomerTest extends Unit
{
    /**
     * @var string
     */
    protected const QUOTE_PERMISSION_GROUP_READ_ONLY = 'READ_ONLY';

    /**
     * @var \SprykerTest\Zed\SharedCart\SharedCartBusinessTester
     */
    protected $tester;

    public function testClearsTheDefaultFlagOnTheCompanyUsersSharedQuote(): void
    {
        // Arrange
        $companyUserTransfer = $this->haveCompanyUser();
        $quoteTransfer = $this->haveSharedQuote($companyUserTransfer, true);

        // Act
        $this->tester->getFacade()->resetQuoteDefaultFlagByCustomer($companyUserTransfer->getIdCompanyUserOrFail());

        // Assert
        $this->assertFalse($this->isSharedQuoteDefault($quoteTransfer, $companyUserTransfer));
    }

    public function testDoesNotTouchAnotherCompanyUsersDefaultSharedQuote(): void
    {
        // Arrange
        $companyUserTransfer = $this->haveCompanyUser();
        $otherCompanyUserTransfer = $this->haveCompanyUser();
        $otherQuoteTransfer = $this->haveSharedQuote($otherCompanyUserTransfer, true);

        // Act
        $this->tester->getFacade()->resetQuoteDefaultFlagByCustomer($companyUserTransfer->getIdCompanyUserOrFail());

        // Assert
        $this->assertTrue($this->isSharedQuoteDefault($otherQuoteTransfer, $otherCompanyUserTransfer));
    }

    /**
     * Covers the early return taken by a company user with no shared cart yet. The guard exists to
     * leave the (fk_company_user, fk_quote) index unlocked rather than to change the outcome, so
     * this asserts the no-op; only a concurrency run can show the locking difference.
     */
    public function testIsANoOpForACompanyUserWithoutSharedQuotes(): void
    {
        // Arrange
        $companyUserTransfer = $this->haveCompanyUser();
        $otherCompanyUserTransfer = $this->haveCompanyUser();
        $otherQuoteTransfer = $this->haveSharedQuote($otherCompanyUserTransfer, true);

        // Act
        $this->tester->getFacade()->resetQuoteDefaultFlagByCustomer($companyUserTransfer->getIdCompanyUserOrFail());

        // Assert
        $this->assertTrue($this->isSharedQuoteDefault($otherQuoteTransfer, $otherCompanyUserTransfer));
    }

    /**
     * The guard exists so that nothing writes through the (fk_company_user, fk_quote) index when
     * there is nothing to reset, leaving the gap unlocked for the rest of the enclosing
     * quote-create transaction. Revert the guard and this test fails; the behavioural tests above
     * stay green either way.
     */
    public function testEmitsNoWriteThroughTheCompanyUserIndexWhenThereIsNothingToReset(): void
    {
        // Arrange
        $companyUserTransfer = $this->haveCompanyUser();
        /** @var \Propel\Runtime\Connection\ConnectionWrapper $connection */
        $connection = Propel::getConnection();
        $connection->useDebug(true);

        // Act
        $this->tester->getFacade()->resetQuoteDefaultFlagByCustomer($companyUserTransfer->getIdCompanyUserOrFail());
        $lastExecutedQuery = $connection->getLastExecutedQuery();
        $connection->useDebug(false);

        // Assert
        $this->assertStringStartsWith('SELECT', $lastExecutedQuery);
        $this->assertStringNotContainsString('UPDATE', $lastExecutedQuery);
    }

    protected function haveCompanyUser(): CompanyUserTransfer
    {
        $companyTransfer = $this->tester->haveCompany();
        $customerTransfer = $this->tester->haveCustomer();

        return $this->tester->haveCompanyUser([
            CompanyUserTransfer::FK_CUSTOMER => $customerTransfer->getIdCustomer(),
            CompanyUserTransfer::CUSTOMER => $customerTransfer,
            CompanyUserTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);
    }

    protected function haveSharedQuote(CompanyUserTransfer $companyUserTransfer, bool $isDefault): QuoteTransfer
    {
        $ownerCustomerTransfer = $this->tester->haveCustomer();
        $quoteTransfer = $this->tester->havePersistentQuote([
            QuoteTransfer::CUSTOMER_REFERENCE => $ownerCustomerTransfer->getCustomerReference(),
            QuoteTransfer::CUSTOMER => $ownerCustomerTransfer,
        ]);

        $quotePermissionGroupEntityTransfer = $this->tester->haveQuotePermissionGroup(
            static::QUOTE_PERMISSION_GROUP_READ_ONLY,
            [ReadSharedCartPermissionPlugin::KEY],
        );

        $this->tester->haveQuoteCompanyUser(
            $companyUserTransfer,
            $quoteTransfer,
            $quotePermissionGroupEntityTransfer,
            $isDefault,
        );

        return $quoteTransfer;
    }

    protected function isSharedQuoteDefault(QuoteTransfer $quoteTransfer, CompanyUserTransfer $companyUserTransfer): bool
    {
        return $this->tester->getFacade()->isSharedQuoteDefault(
            $quoteTransfer->getIdQuoteOrFail(),
            $companyUserTransfer->getIdCompanyUserOrFail(),
        );
    }
}
