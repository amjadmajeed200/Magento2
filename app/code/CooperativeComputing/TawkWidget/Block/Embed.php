<?php
/**
 * CooperativeComputing
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   CooperativeComputing
 * @package    CooperativeComputing_TawkWidget
 * @copyright  Copyright (c) 2022 CooperativeComputing (https://www.magento.com/)
 */

namespace CooperativeComputing\TawkWidget\Block;

use Magento\Framework\View\Element\Template;
use Tawk\Widget\Model\WidgetFactory;
use Magento\Customer\Model\SessionFactory;

class Embed extends \Tawk\Widget\Block\Embed
{

    const TAWK_EMBED_URL = 'https://embed.tawk.to';

    /**
     * @var WidgetFactory
     */
    protected $modelWidgetFactory;
        
    /**
     * @var Tawk\Widget\Model\Widget
     */    
    protected $model;
    
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */    
    protected $storeManager;
    
    /**
     * @var Magento\Framework\App\Request\Http
     */    
    protected $request;
    
    /**
     * Embed Construct.
     *
     * @param SessionFactory    $sessionFactory
     * @param WidgetFactory     $modelFactory
     * @param Template\Context  $context
     * @param array             $data
     */
    public function __construct(
        SessionFactory $sessionFactory,
        WidgetFactory $modelFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($sessionFactory, $modelFactory, $context, $data);
        $this->modelWidgetFactory = $modelFactory->create();
        $this->storeManager = $context->getStoreManager();
        $this->model = $this->getWidgetModel();
        $this->request = $context->getRequest();
    }

    /**
     * Get widget model
     */
    private function getWidgetModel()
    {
        $store = $this->storeManager->getStore();

        $storeId   = $store->getId();
        $groupId   = $store->getGroup()->getId();
        $websiteId = $store->getWebsite()->getId();

        //order in which we select widget
        $ids = [$websiteId.'_'.$groupId.'_'.$storeId, $websiteId.'_'.$groupId, $websiteId, 'global'];

        foreach ($ids as $id) {
            $tmpModel = $this->modelWidgetFactory->loadByForStoreId($id);

            if ($tmpModel->hasId()) {
                return $tmpModel;
            }
        }

        return null;
    }

    /**
     * Return html or boolean if model not exist
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if ($this->model === null) {
            return '';
        }

        $alwaysdisplay = $this->model->getAlwaysDisplay();
        $donotdisplay = $this->model->getDoNotDisplay();
        $display = true;

        $httpHost = $this->request->getServer('HTTP_HOST');
        $requestUri = $this->request->getServer('REQUEST_URI');
        $httpsServer = $this->request->getServer('HTTPS');
        $serverProtocol = $this->request->getServer('SERVER_PROTOCOL');

        if ($alwaysdisplay == 1) {
            $display = true;

            $excluded_url_list = $this->model->getExcludeUrl();
            if (!empty($excluded_url_list) && strlen($excluded_url_list) > 0) {
                $current_url = $httpHost . $requestUri;
                $current_url = urldecode($current_url);

                $ssl = !empty($httpsServer) && $httpsServer == 'on';
                $sp = strtolower($serverProtocol);
                $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                $current_url = $protocol.'://'.$current_url;
                $current_url = strtolower($current_url);
                $current_url = trim(strtolower($current_url));

                $excluded_url_list = preg_split("/,/", $excluded_url_list);
                foreach ($excluded_url_list as $exclude_url) {
                    $exclude_url = strtolower(urldecode(trim($exclude_url)));
                    if (strpos($current_url, $exclude_url) !== false) {
                        $display = false;
                    }
                }
            }
        } else {
            $display = false;
        }

        if ($donotdisplay == 1) {
            $display = false;

            $included_url_list = $this->model->getIncludeUrl();
            if (strlen($included_url_list) > 0) {
                $current_url = $httpHost . $requestUri;
                $current_url = urldecode($current_url);

                $ssl = (!empty($httpsServer) && $httpsServer == 'on');
                $sp = strtolower($serverProtocol);
                $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                $current_url = $protocol.'://'.$current_url;
                $current_url = strtolower($current_url);
                $current_url = trim(strtolower($current_url));

                $included_url_list = preg_split("/,/", $included_url_list);
                foreach ($included_url_list as $include_url) {
                    $include_url = strtolower(urldecode(trim($include_url)));
                    if (strpos($current_url, $include_url) !== false) {
                        $display = true;
                    }
                }
            }
        }

        if ($display == true) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
