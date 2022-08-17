<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreLocator
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreLocator\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;
use Mageplaza\StoreLocator\Helper\Data as HelperData;

/**
 * Class Router
 * @package Mageplaza\StoreLocator\Controller
 */
class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param HelperData $helperData
     */
    public function __construct(
        ActionFactory $actionFactory,
        HelperData $helperData
    ) {
        $this->actionFactory = $actionFactory;
        $this->_helperData   = $helperData;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $routePath  = explode('/', $identifier);
        $urlSuffix  = $this->_helperData->getUrlSuffix();
        $route      = $routePath[0];

        if ($urlSuffix) {
            if (strpos($route, $urlSuffix) !== false) {
                $pos   = strpos($route, $urlSuffix);
                $route = substr($route, 0, $pos);
            } else {
                return null;
            }
        }

        if (!$this->_helperData->isEnabled() ||
            count($routePath) !== 1 ||
            !in_array($route, $this->_helperData->getAllLocationUrl(), true)) {
            return null;
        }

        if ($route === $this->_helperData->getRoute()) {
            $request->setModuleName('mpstorelocator')
                ->setControllerName('storelocator')
                ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                ->setActionName('store')->setPathInfo('/mpstorelocator/storelocator/store');
        }

        $location = $this->_helperData->getLocationByUrl($route);

        if ($location && $location->getLocationId()) {
            $request->setModuleName('mpstorelocator')
                ->setControllerName('storelocator')
                ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                ->setActionName('view')->setPathInfo('mpstorelocator/storelocator/view/' . $location->getLocationId());
        }

        return $this->actionFactory->create(Forward::class);
    }
}
