<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\SharedCart;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToCartClientBridge;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToCustomerClientBridge;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToMessengerClientBridge;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToMultiCartClientBridge;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToPersistentCartClientBridge;
use Spryker\Client\SharedCart\Dependency\Client\SharedCartToQuoteClientBridge;

class SharedCartDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';

    /**
     * @var string
     */
    public const CLIENT_MULTI_CART = 'CLIENT_MULTI_CART';

    /**
     * @var string
     */
    public const CLIENT_PERSISTENT_CART = 'CLIENT_PERSISTENT_CART';

    /**
     * @var string
     */
    public const CLIENT_ZED_REQUEST = 'CLIENT_ZED_REQUEST';

    /**
     * @var string
     */
    public const CLIENT_MESSENGER = 'CLIENT_MESSENGER';

    /**
     * @var string
     */
    public const CLIENT_CART = 'CLIENT_CART';

    /**
     * @var string
     */
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = parent::provideServiceLayerDependencies($container);
        $container = $this->addCartClient($container);
        $container = $this->addCustomerClient($container);
        $container = $this->addMessengerClient($container);
        $container = $this->addMultiCartClient($container);
        $container = $this->addPersistentCartClient($container);
        $container = $this->addZedRequestClient($container);
        $container = $this->addQuoteClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCartClient(Container $container): Container
    {
        $container->set(static::CLIENT_CART, function (Container $container) {
            return new SharedCartToCartClientBridge($container->getLocator()->cart()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container->set(static::CLIENT_CUSTOMER, function (Container $container) {
            return new SharedCartToCustomerClientBridge($container->getLocator()->customer()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addMessengerClient(Container $container): Container
    {
        $container->set(static::CLIENT_MESSENGER, function (Container $container) {
            return new SharedCartToMessengerClientBridge($container->getLocator()->messenger()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addMultiCartClient(Container $container): Container
    {
        $container->set(static::CLIENT_MULTI_CART, function (Container $container) {
            return new SharedCartToMultiCartClientBridge($container->getLocator()->multiCart()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addPersistentCartClient(Container $container): Container
    {
        $container->set(static::CLIENT_PERSISTENT_CART, function (Container $container) {
            return new SharedCartToPersistentCartClientBridge($container->getLocator()->persistentCart()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addZedRequestClient(Container $container): Container
    {
        $container->set(static::CLIENT_ZED_REQUEST, function (Container $container) {
            return $container->getLocator()->zedRequest()->client();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUOTE, function (Container $container) {
            return new SharedCartToQuoteClientBridge($container->getLocator()->quote()->client());
        });

        return $container;
    }
}
