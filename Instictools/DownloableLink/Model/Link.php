<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Model;

use Magento\Downloadable\Model\Link as MagentoLink;

/**
 * Class Link
 * @package Instictools\DownloableLink\Model
 */
class Link extends MagentoLink
{
    const KEY_IS_VISIBLE = 'is_visible';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getIsVisible()
    {
        return $this->getData(self::KEY_IS_VISIBLE);
    }

    /**
     * @param int $isShareable
     * @return $this
     */
    public function setIsVisible($isShareable)
    {
        return $this->setData(self::KEY_IS_VISIBLE, $isShareable);
    }
}
