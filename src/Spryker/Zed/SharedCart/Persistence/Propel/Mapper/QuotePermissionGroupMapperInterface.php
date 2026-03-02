<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuotePermissionGroupTransfer;
use Generated\Shared\Transfer\SpyQuotePermissionGroupEntityTransfer;
use Orm\Zed\SharedCart\Persistence\SpyQuotePermissionGroup;

interface QuotePermissionGroupMapperInterface
{
    public function mapQuotePermissionGroup(SpyQuotePermissionGroupEntityTransfer $quotePermissionGroupEntityTransfer): QuotePermissionGroupTransfer;

    public function mapQuotePermissionGroupEntityToQuotePermissionGroupTransfer(
        SpyQuotePermissionGroup $quotePermissionGroupEntity,
        QuotePermissionGroupTransfer $quotePermissionGroupTransfer
    ): QuotePermissionGroupTransfer;
}
