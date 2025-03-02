<?php
namespace Letscms\TestApi\Model;

use Letscms\TestApi\Api\ProductListInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class ProductData implements ProductListInterface
{ 
    protected $_productCollectionFactory;
    protected $_categoryCollectionFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function getData()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'sku', 'price', 'special_price', 'image', 'small_image', 'thumbnail']);
        $collection->addAttributeToFilter('visibility', ['neq' => Visibility::VISIBILITY_NOT_VISIBLE]);

        $products = [];

        foreach ($collection as $product) {
            $categoryIds = $product->getCategoryIds();
            $categories = $this->getCategoryNames($categoryIds);

            $products[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'price' => $product->getPrice(),
                'special_price' => $product->getSpecialPrice(),
                'image' => $product->getImage(),
                'small_image' => $product->getSmallImage(),
                'thumbnail' => $product->getThumbnail(),
                'categories' => $categories
            ];
        }

        return $products;
    }

    private function getCategoryNames($categoryIds)
    {
        $categories = [];
        if (!empty($categoryIds)) {
            $categoryCollection = $this->_categoryCollectionFactory->create()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', ['in' => $categoryIds]);

            foreach ($categoryCollection as $category) {
                $categories[] = $category->getName();
            }
        }
        return $categories;
    }
}
