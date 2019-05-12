<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business\ResourceShare;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ResourceShareDataTransfer;
use Generated\Shared\Transfer\ResourceShareResponseTransfer;
use Generated\Shared\Transfer\ResourceShareTransfer;
use Spryker\Shared\SharedCart\SharedCartConfig;

class ResourceShareDataExpanderStrategy implements ResourceShareDataExpanderStrategyInterface
{
    protected const KEY_ID_QUOTE = 'id_quote';
    protected const KEY_OWNER_ID_COMPANY_USER = 'owner_id_company_user';
    protected const KEY_OWNER_ID_COMPANY_BUSINESS_UNIT = 'owner_id_company_business_unit';

    protected const GLOSSARY_KEY_ONE_OR_MORE_REQUIRED_PROPERTIES_ARE_MISSING = 'shared_cart.resource_share.strategy.error.properties_are_missing';

    /**
     * @param \Generated\Shared\Transfer\ResourceShareTransfer $resourceShareTransfer
     *
     * @return \Generated\Shared\Transfer\ResourceShareResponseTransfer
     */
    public function applyResourceShareDataExpanderStrategy(ResourceShareTransfer $resourceShareTransfer): ResourceShareResponseTransfer
    {
        $resourceShareDataTransfer = $this->expandResourceShareData(clone $resourceShareTransfer->getResourceShareData());
        $resourceShareResponseTransfer = $this->validateExpandedResourceShareData($resourceShareDataTransfer);

        if (!$resourceShareResponseTransfer->getIsSuccessful()) {
            return $resourceShareResponseTransfer;
        }

        $resourceShareTransfer->setResourceShareData($resourceShareDataTransfer);

        return $resourceShareResponseTransfer->setResourceShare($resourceShareTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareDataTransfer $resourceShareDataTransfer
     *
     * @return \Generated\Shared\Transfer\ResourceShareResponseTransfer
     */
    protected function validateExpandedResourceShareData(ResourceShareDataTransfer $resourceShareDataTransfer): ResourceShareResponseTransfer
    {
        if ($resourceShareDataTransfer->getShareOption()
            && $resourceShareDataTransfer->getIdQuote()
            && $resourceShareDataTransfer->getOwnerIdCompanyUser()
            && $resourceShareDataTransfer->getOwnerIdCompanyBusinessUnit()
        ) {
            return (new ResourceShareResponseTransfer())
                ->setIsSuccessful(true);
        }

        return (new ResourceShareResponseTransfer())
            ->setIsSuccessful(false)
            ->addMessage(
                (new MessageTransfer())->setValue(static::GLOSSARY_KEY_ONE_OR_MORE_REQUIRED_PROPERTIES_ARE_MISSING)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ResourceShareDataTransfer $resourceShareDataTransfer
     *
     * @return \Generated\Shared\Transfer\ResourceShareDataTransfer
     */
    protected function expandResourceShareData(ResourceShareDataTransfer $resourceShareDataTransfer): ResourceShareDataTransfer
    {
        $resourceShareData = $resourceShareDataTransfer->getData();

        $resourceShareDataTransfer->setShareOption($resourceShareData[SharedCartConfig::KEY_SHARE_OPTION] ?? null)
            ->setIdQuote($resourceShareData[static::KEY_ID_QUOTE] ?? null)
            ->setOwnerIdCompanyUser($resourceShareData[static::KEY_OWNER_ID_COMPANY_USER] ?? null)
            ->setOwnerIdCompanyBusinessUnit($resourceShareData[static::KEY_OWNER_ID_COMPANY_BUSINESS_UNIT] ?? null);

        return $resourceShareDataTransfer;
    }
}
