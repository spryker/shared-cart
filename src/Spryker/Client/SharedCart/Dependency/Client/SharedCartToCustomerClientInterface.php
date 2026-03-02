<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart\Dependency\Client;

use Generated\Shared\Transfer\CustomerTransfer;

interface SharedCartToCustomerClientInterface
{
    public function setCustomer(CustomerTransfer $customerTransfer): CustomerTransfer;

    public function getCustomer(): ?CustomerTransfer;
}
