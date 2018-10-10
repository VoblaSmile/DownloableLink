<?php
/**
 * @category    Instictools
 * @package     Instictools_DownloableLink
 * @copyright   Copyright (c)
 */
namespace Instictools\DownloableLink\Plugin\Ui\DataProvider\Product\Form\Modifier;

use Instictools\DownloableLink\Model\Source\Visible;
use Magento\Downloadable\Ui\DataProvider\Product\Form\Modifier\Links as MagentoLinks;
use Magento\Ui\Component\Form;

/**
 * Class Links
 * @package Instictools\DownloableLink\Plugin\Ui\DataProvider\Product\Form\Modifier
 */
class Links
{
    /**
     * @var Visible
     */
    protected $visible;

    /**
     * Links constructor.
     * @param Visible $visible
     */
    public function __construct(
        Visible $visible
    ) {
        $this->visible = $visible;
    }

    /**
     * Add field "is_visible" in object DataProvider links
     * @param MagentoLinks $object
     * @param array $result
     * @return array
     */
    public function afterModifyMeta(MagentoLinks $object, $result)
    {
        $result['downloadable']['children']['container_links']['children']['link']['children']['record']['children']['is_visible'] = $this->getIsVisibleColumn();
        return $result;
    }

    /**
     * Get Is Visible Column
     * @return mixed
     */
    private function getIsVisibleColumn()
    {
        $shareableField['arguments']['data']['config'] = [
            'label' => __('Is visible'),
            'formElement' => Form\Element\Select::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'is_visible',
            'options' => $this->visible->toOptionArray(),
        ];

        return $shareableField;
    }
}
