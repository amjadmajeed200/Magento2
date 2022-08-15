<?php
namespace Cooperative\Customwork\Plugin;
class Links
{
    public function afterRenderLink(\Magento\Framework\View\Element\AbstractBlock $subject, $result)
    {
        $label = strtolower(str_replace('', '-', strip_tags($result) ) );
        return str_replace("nav", $label." nav", $result);
    }
}
?>
