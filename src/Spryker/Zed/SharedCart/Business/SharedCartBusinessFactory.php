<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SharedCart\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\SharedCart\Business\Activator\QuoteActivator;
use Spryker\Zed\SharedCart\Business\Activator\QuoteActivatorInterface;
use Spryker\Zed\SharedCart\Business\CustomerExpander\CustomerExpander;
use Spryker\Zed\SharedCart\Business\CustomerExpander\CustomerExpanderInterface;
use Spryker\Zed\SharedCart\Business\Installer\QuotePermissionGroupInstaller;
use Spryker\Zed\SharedCart\Business\Installer\QuotePermissionGroupInstallerInterface;
use Spryker\Zed\SharedCart\Business\Messenger\SharedCartMessenger;
use Spryker\Zed\SharedCart\Business\Messenger\SharedCartMessengerInterface;
use Spryker\Zed\SharedCart\Business\Model\QuoteCompanyUserWriter;
use Spryker\Zed\SharedCart\Business\Model\QuoteCompanyUserWriterInterface;
use Spryker\Zed\SharedCart\Business\Model\QuotePermissionGroupReader;
use Spryker\Zed\SharedCart\Business\Model\QuotePermissionGroupReaderInterface;
use Spryker\Zed\SharedCart\Business\Model\QuoteReader;
use Spryker\Zed\SharedCart\Business\Model\QuoteReaderInterface;
use Spryker\Zed\SharedCart\Business\QuoteCollectionExpander\SharedCartQuoteCollectionExpander;
use Spryker\Zed\SharedCart\Business\QuoteCollectionExpander\SharedCartQuoteCollectionExpanderInterface;
use Spryker\Zed\SharedCart\Business\QuoteCompanyUser\QuoteCompanyUserReader;
use Spryker\Zed\SharedCart\Business\QuoteCompanyUser\QuoteCompanyUserReaderInterface;
use Spryker\Zed\SharedCart\Business\QuoteResponseExpander\CustomerPermissionQuoteResponseExpander;
use Spryker\Zed\SharedCart\Business\QuoteResponseExpander\CustomerShareCartQuoteResponseExpander;
use Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpander;
use Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface;
use Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteShareDetailsQuoteResponseExpander;
use Spryker\Zed\SharedCart\Business\QuoteShareDetails\QuoteShareDetailsReader;
use Spryker\Zed\SharedCart\Business\QuoteShareDetails\QuoteShareDetailsReaderInterface;
use Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteCompanyUserWriter;
use Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteCompanyUserWriterInterface;
use Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteShare;
use Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteShareInterface;
use Spryker\Zed\SharedCart\Business\Validator\SharedCartCommentValidator;
use Spryker\Zed\SharedCart\Business\Validator\SharedCartCommentValidatorInterface;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToCustomerFacadeInterface;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToMessengerFacadeInterface;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToPermissionFacadeInterface;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface;
use Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToStoreFacadeInterface;
use Spryker\Zed\SharedCart\SharedCartDependencyProvider;

/**
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\SharedCart\Persistence\SharedCartRepositoryInterface getRepository()
 * @method \Spryker\Zed\SharedCart\SharedCartConfig getConfig()
 */
class SharedCartBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface
     */
    public function createQuoteResponseExpander(): QuoteResponseExpanderInterface
    {
        return new QuoteResponseExpander($this->getQuoteResponseExpanderList());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface
     */
    public function createCustomerPermissionQuoteResponseExpander(): QuoteResponseExpanderInterface
    {
        return new CustomerPermissionQuoteResponseExpander($this->getCustomerFacade(), $this->getRepository());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface
     */
    public function createCustomerShareCartQuoteResponseExpander(): QuoteResponseExpanderInterface
    {
        return new CustomerShareCartQuoteResponseExpander(
            $this->createQuoteReader(),
            $this->getStoreFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface
     */
    public function createQuoteShareDetailsQuoteResponseExpander(): QuoteResponseExpanderInterface
    {
        return new QuoteShareDetailsQuoteResponseExpander($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Model\QuoteReaderInterface
     */
    public function createQuoteReader(): QuoteReaderInterface
    {
        return new QuoteReader($this->getRepository(), $this->getQuoteFacade());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Model\QuotePermissionGroupReaderInterface
     */
    public function createQuotePermissionGroupReader(): QuotePermissionGroupReaderInterface
    {
        return new QuotePermissionGroupReader($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Installer\QuotePermissionGroupInstallerInterface
     */
    public function createQuotePermissionGroupInstaller(): QuotePermissionGroupInstallerInterface
    {
        return new QuotePermissionGroupInstaller(
            $this->getConfig(),
            $this->getEntityManager(),
            $this->getPermissionFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Activator\QuoteActivatorInterface
     */
    public function createQuoteActivator(): QuoteActivatorInterface
    {
        return new QuoteActivator($this->getEntityManager());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Model\QuoteCompanyUserWriterInterface
     */
    public function createQuoteCompanyUserWriter(): QuoteCompanyUserWriterInterface
    {
        return new QuoteCompanyUserWriter(
            $this->getRepository(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\CustomerExpander\CustomerExpanderInterface
     */
    public function createCustomerExpander(): CustomerExpanderInterface
    {
        return new CustomerExpander(
            $this->getRepository(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteShareDetails\QuoteShareDetailsReaderInterface
     */
    public function createQuoteShareDetailsReader(): QuoteShareDetailsReaderInterface
    {
        return new QuoteShareDetailsReader(
            $this->getRepository(),
            $this->getCustomerFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteCompanyUser\QuoteCompanyUserReaderInterface
     */
    public function createQuoteCompanyUserReader(): QuoteCompanyUserReaderInterface
    {
        return new QuoteCompanyUserReader($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteShareInterface
     */
    public function createResourceShareQuoteShare(): ResourceShareQuoteShareInterface
    {
        return new ResourceShareQuoteShare(
            $this->getRepository(),
            $this->getQuoteFacade(),
            $this->createResourceShareQuoteCompanyUserWriter(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\ResourceShare\ResourceShareQuoteCompanyUserWriterInterface
     */
    public function createResourceShareQuoteCompanyUserWriter(): ResourceShareQuoteCompanyUserWriterInterface
    {
        return new ResourceShareQuoteCompanyUserWriter(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createQuoteCompanyUserWriter(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToCustomerFacadeInterface
     */
    public function getCustomerFacade(): SharedCartToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(SharedCartDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToQuoteFacadeInterface
     */
    public function getQuoteFacade(): SharedCartToQuoteFacadeInterface
    {
        return $this->getProvidedDependency(SharedCartDependencyProvider::FACADE_QUOTE);
    }

    /**
     * @return \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToPermissionFacadeInterface
     */
    public function getPermissionFacade(): SharedCartToPermissionFacadeInterface
    {
        return $this->getProvidedDependency(SharedCartDependencyProvider::FACADE_PERMISSION);
    }

    /**
     * @return array<\Spryker\Zed\SharedCart\Business\QuoteResponseExpander\QuoteResponseExpanderInterface>
     */
    protected function getQuoteResponseExpanderList(): array
    {
        return [
            $this->createCustomerPermissionQuoteResponseExpander(),
            $this->createCustomerShareCartQuoteResponseExpander(),
            $this->createQuoteShareDetailsQuoteResponseExpander(),
        ];
    }

    /**
     * @return \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToStoreFacadeInterface
     */
    public function getStoreFacade(): SharedCartToStoreFacadeInterface
    {
        return $this->getProvidedDependency(SharedCartDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\QuoteCollectionExpander\SharedCartQuoteCollectionExpanderInterface
     */
    public function createSharedCartQuoteCollectionExpander(): SharedCartQuoteCollectionExpanderInterface
    {
        return new SharedCartQuoteCollectionExpander(
            $this->createQuoteReader(),
            $this->getStoreFacade(),
            $this->createQuoteShareDetailsReader(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Messenger\SharedCartMessengerInterface
     */
    public function createSharedCartMessenger(): SharedCartMessengerInterface
    {
        return new SharedCartMessenger(
            $this->getRepository(),
            $this->getMessengerFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\SharedCart\Business\Validator\SharedCartCommentValidatorInterface
     */
    public function createSharedCartCommentValidator(): SharedCartCommentValidatorInterface
    {
        return new SharedCartCommentValidator($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\SharedCart\Dependency\Facade\SharedCartToMessengerFacadeInterface
     */
    public function getMessengerFacade(): SharedCartToMessengerFacadeInterface
    {
        return $this->getProvidedDependency(SharedCartDependencyProvider::FACADE_MESSENGER);
    }
}
