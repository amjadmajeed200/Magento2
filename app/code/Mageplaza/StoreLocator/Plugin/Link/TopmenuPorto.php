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

namespace Mageplaza\StoreLocator\Plugin\Link;

use Mageplaza\StoreLocator\Block\Frontend;
use Smartwave\Megamenu\Block\Topmenu;

/**
 * Class TopmenuPorto
 * @package Mageplaza\StoreLocator\Plugin\Link
 */
class TopmenuPorto
{
    /**
     * @param Topmenu $topmenu
     * @param $html
     *
     * @return string
     */
    public function afterGetMegamenuHtml(Topmenu $topmenu, $html)
    {
        $menuHtml = $topmenu->getLayout()->createBlock(Frontend::class)
            ->setTemplate('Mageplaza_StoreLocator::menu/topmenuporto.phtml')->toHtml();

        return $html . $menuHtml;
    }
}
