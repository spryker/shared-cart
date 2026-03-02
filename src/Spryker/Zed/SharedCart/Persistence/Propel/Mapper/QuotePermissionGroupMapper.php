<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\SpyQuotePermissionGroupEntityTransfer;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroup;

class QuotePermissionGroupMapper implements QuotePermissionGroupMapperInterface
{
    public function mapQuotePermissionGroup(SpyQuotePermissionGroupEntityTransfer $quotePermissionGroupEntityTransfer): QuotePermissionGroupTransfer
    {
        $quotePermissionGroupTransfer = new QuotePermissionGroupTransfer();
        $quotePermissionGroupTransfer->fromArray($quotePermissionGroupEntityTransfer->modifiedToArray(), true);

        return $quotePermissionGroupTransfer;
    }

    public function mapQuotePermissionGroupEntityToQuotePermissionGroupTransfer(
        SpyQuotePermissionGroup $quotePermissionGroupEntity,
        QuotePermissionGroupTransfer $quotePermissionGroupTransfer
    ): QuotePermissionGroupTransfer {
        return $quotePermissionGroupTransfer->fromArray($quotePermissionGroupEntity->toArray(), true);
    }
}
