<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Model\Source;

use Magento\Downloadable\Model\Link;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Visible
 * @package Instictools\DownloableLink\Model\Source
 */
class Visible implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => Link::LINK_SHAREABLE_YES, 'label' => __('Yes')],
            ['value' => Link::LINK_SHAREABLE_NO, 'label' => __('No')],
        ];
    }
}
