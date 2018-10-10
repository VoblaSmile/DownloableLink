<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Downloadable\Api\Data\LinkInterface;
use Magento\Downloadable\Model\LinkRepository as MagentoLinkRepository;
use Magento\Downloadable\Model\Product\TypeHandler\Link as LinkHandler;
use Magento\Framework\App\ObjectManager;

/**
 * Class LinkRepository
 * @package Instictools\DownloableLink\Model
 */
class LinkRepository extends MagentoLinkRepository
{
    /**
     * @var LinkHandler
     */
    private $linkTypeHandler;

    /**
     * @param ProductInterface $product
     * @param LinkInterface $link
     * @param bool $isGlobalScopeContent
     * @return int
     */
    protected function saveLink(
        ProductInterface $product,
        LinkInterface $link,
        $isGlobalScopeContent
    ) {
        $linkData = [
            'link_id' => (int)$link->getId(),
            'is_delete' => 0,
            'type' => $link->getLinkType(),
            'sort_order' => $link->getSortOrder(),
            'title' => $link->getTitle(),
            'price' => $link->getPrice(),
            'number_of_downloads' => $link->getNumberOfDownloads(),
            'is_shareable' => $link->getIsShareable(),
            'is_visible' => $link->getIsVisible(),
        ];

        if ($link->getLinkType() == 'file' && $link->getLinkFile() === null) {
            $linkData['file'] = $this->jsonEncoder->encode(
                [
                    $this->fileContentUploader->upload($link->getLinkFileContent(), 'link_file'),
                ]
            );
        } elseif ($link->getLinkType() === 'url') {
            $linkData['link_url'] = $link->getLinkUrl();
        } else {
            //existing link file
            $linkData['file'] = $this->jsonEncoder->encode(
                [
                    [
                        'file' => $link->getLinkFile(),
                        'status' => 'old',
                    ]
                ]
            );
        }

        if ($link->getSampleType() == 'file') {
            $linkData['sample']['type'] = 'file';
            if ($link->getSampleFile() === null) {
                $fileData = [
                    $this->fileContentUploader->upload($link->getSampleFileContent(), 'link_sample_file'),
                ];
            } else {
                $fileData = [
                    [
                        'file' => $link->getSampleFile(),
                        'status' => 'old',
                    ]
                ];
            }
            $linkData['sample']['file'] = $this->jsonEncoder->encode($fileData);
        } elseif ($link->getSampleType() == 'url') {
            $linkData['sample']['type'] = 'url';
            $linkData['sample']['url'] = $link->getSampleUrl();
        }

        $downloadableData = ['link' => [$linkData]];
        if ($isGlobalScopeContent) {
            $product->setStoreId(0);
        }
        $this->getLinkTypeHandler()->save($product, $downloadableData);
        return $product->getLastAddedLinkId();
    }

    /**
     * Get LinkTypeHandler  instance
     *
     * @deprecated 100.1.0 MAGETWO-52273
     * @return LinkHandler
     */
    private function getLinkTypeHandler()
    {
        if (!$this->linkTypeHandler) {
            $this->linkTypeHandler = ObjectManager::getInstance()->get(LinkHandler::class);
        }

        return $this->linkTypeHandler;
    }
}
