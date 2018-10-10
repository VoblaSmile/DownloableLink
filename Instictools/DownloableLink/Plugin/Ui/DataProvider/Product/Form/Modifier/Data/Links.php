<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Plugin\Ui\DataProvider\Product\Form\Modifier\Data;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Downloadable\Api\Data\LinkInterface;
use Magento\Downloadable\Helper\File as DownloadableFile;
use Magento\Downloadable\Model\Link as LinkModel;
use Magento\Downloadable\Model\Product\Type;
use Magento\Downloadable\Ui\DataProvider\Product\Form\Modifier\Data\Links as MagentoLinks;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;

/**
 * Class Links
 * @package Instictools\DownloableLink\Plugin\Ui\DataProvider\Product\Form\Modifier\Data
 */
class Links
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var DownloadableFile
     */
    protected $downloadableFile;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var LinkModel
     */
    protected $linkModel;

    /**
     * @param Escaper $escaper
     * @param LocatorInterface $locator
     * @param DownloadableFile $downloadableFile
     * @param UrlInterface $urlBuilder
     * @param LinkModel $linkModel
     */
    public function __construct(
        Escaper $escaper,
        LocatorInterface $locator,
        DownloadableFile $downloadableFile,
        UrlInterface $urlBuilder,
        LinkModel $linkModel
    ) {
        $this->escaper = $escaper;
        $this->locator = $locator;
        $this->downloadableFile = $downloadableFile;
        $this->urlBuilder = $urlBuilder;
        $this->linkModel = $linkModel;
    }

    public function afterGetLinksData(MagentoLinks $object, $result)
    {
        $linksData = [];
        if ($this->locator->getProduct()->getTypeId() !== Type::TYPE_DOWNLOADABLE) {
            return $result;
        }

        $links = $this->locator->getProduct()->getTypeInstance()->getLinks(
            $this->locator->getProduct()
        );
        /** @var LinkInterface $link */
        foreach ($links as $link) {
            $linkData = [];
            $linkData['link_id'] = $link->getId();
            $linkData['title'] = $this->escaper->escapeHtml($link->getTitle());
            $linkData['price'] = $object->getPriceValue($link->getPrice());
            $linkData['number_of_downloads'] = $link->getNumberOfDownloads();
            $linkData['is_shareable'] = $link->getIsShareable();
            $linkData['link_url'] = $link->getLinkUrl();
            $linkData['type'] = $link->getLinkType();
            $linkData['is_visible'] = $link->getIsVisible();
            $linkData['sample']['url'] = $link->getSampleUrl();
            $linkData['sample']['type'] = $link->getSampleType();
            $linkData['sort_order'] = $link->getSortOrder();
            $linkData['is_unlimited'] = $linkData['number_of_downloads'] ? '0' : '1';

            if ($this->locator->getProduct()->getStoreId()) {
                $linkData['use_default_price'] = $link->getWebsitePrice() ? '0' : '1';
                $linkData['use_default_title'] = $link->getStoreTitle() ? '0' : '1';
            }

            $linkData = $this->addLinkFile($linkData, $link);
            $linkData = $this->addSampleFile($linkData, $link);

            $linksData[] = $linkData;
        }
        $result = $linksData;

        return $result;
    }

    /**
     * Add Link File info into $linkData
     *
     * @param array $linkData
     * @param LinkInterface $link
     * @return array
     */
    protected function addLinkFile(array $linkData, LinkInterface $link)
    {
        $linkFile = $link->getLinkFile();
        if ($linkFile) {
            $file = $this->downloadableFile->getFilePath($this->linkModel->getBasePath(), $linkFile);
            if ($this->downloadableFile->ensureFileInFilesystem($file)) {
                $linkData['file'][0] = [
                    'file' => $linkFile,
                    'name' => $this->downloadableFile->getFileFromPathFile($linkFile),
                    'size' => $this->downloadableFile->getFileSize($file),
                    'status' => 'old',
                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $link->getId(), 'type' => 'link', '_secure' => true]
                    ),
                ];
            }
        }

        return $linkData;
    }

    /**
     * Add Sample File info into $linkData
     *
     * @param array $linkData
     * @param LinkInterface $link
     * @return array
     */
    protected function addSampleFile(array $linkData, LinkInterface $link)
    {
        $sampleFile = $link->getSampleFile();
        if ($sampleFile) {
            $file = $this->downloadableFile->getFilePath($this->linkModel->getBaseSamplePath(), $sampleFile);
            if ($this->downloadableFile->ensureFileInFilesystem($file)) {
                $linkData['sample']['file'][0] = [
                    'file' => $sampleFile,
                    'name' => $this->downloadableFile->getFileFromPathFile($sampleFile),
                    'size' => $this->downloadableFile->getFileSize($file),
                    'status' => 'old',
                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $link->getId(), 'type' => 'sample', '_secure' => true]
                    ),
                ];
            }
        }

        return $linkData;
    }
}
