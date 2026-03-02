<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Communication\Controller;

use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupResponseTransfer;
use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShareDetailCollectionTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\SharedCart\Business\SharedCartFacadeInterface getFacade()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface getRepository()
 * @method \Spryker\Zed\SharedCart\Communication\SharedCartCommunicationFactory getFactory()
 */
class GatewayController extends AbstractGatewayController
{
    public function getQuotePermissionGroupsAction(QuotePermissionGroupCriteriaFilterTransfer $criteriaFilterTransfer): QuotePermissionGroupResponseTransfer
    {
        return $this->getFacade()->getQuotePermissionGroupList($criteriaFilterTransfer);
    }

    public function getShareDetailsByIdQuoteAction(QuoteTransfer $quoteTransfer): ShareDetailCollectionTransfer
    {
        return $this->getFacade()->getShareDetailsByIdQuote($quoteTransfer);
    }

    public function findQuotePermissionGroupByIdAction(QuotePermissionGroupTransfer $quotePermissionGroupTransfer): QuotePermissionGroupResponseTransfer
    {
        return $this->getFacade()->findQuotePermissionGroupById($quotePermissionGroupTransfer);
    }

    public function getCustomerCollectionByQuoteAction(QuoteTransfer $quoteTransfer): CustomerCollectionTransfer
    {
        return $this->getFacade()->getCustomerCollectionByQuote($quoteTransfer);
    }
}
