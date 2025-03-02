<?php
namespace Letscms\TestApi\Model;

use Letscms\TestApi\Api\ProductsInterfaces;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Products implements ProductsInterfaces
{
    protected $productRepository;
    protected $stockRegistry;
    protected $customerRepository;
    protected $categoryCollectionFactory;
    protected $categoryRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        CollectionFactory $categoryCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Update product price
     * @param string $sku
     * @param int $price
     * @return array
     */
    public function updatePrice($sku, $price)
    {
        try {
            $product = $this->productRepository->get($sku);
            $product->setPrice($price);
            $this->productRepository->save($product);

            return ["message" => "Price updated successfully for SKU: " . $sku];
        } catch (NoSuchEntityException $e) {
            return ["error" => "Product not found: " . $sku];
        } catch (\Exception $e) {
            return ["error" => "Error updating price: " . $e->getMessage()];
        }
    }

    /**
     * Update product quantity
     * @param string $sku
     * @param int $qty
     * @return array
     */
    public function updateQty($sku, $qty)
    {       
        try {            
            $product = $this->productRepository->get($sku);
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($qty);
            $stockItem->setIsInStock($qty > 0);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);

            return ["message" => "Quantity updated successfully."];
        } catch (NoSuchEntityException $e) {
            return ["error" => "Product with SKU '{$sku}' not found."];
        } catch (\Exception $e) {
            return ["error" => "Error updating quantity: " . $e->getMessage()];
        }
    }

    /**
     * Get product info by SKU
     * @param string $skuid    
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($skuid): ProductInterface
    {
        try {
            return $this->productRepository->get($skuid);
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Product not found.'));
        }
    }

    /**
     * Get all categories list
     * @return array
     */
    public function getCategoriesList(): array
    {
        $categories = [];
        try {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'id', 'parent_id', 'is_active']);

            foreach ($collection as $category) {
                $categories[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'parent_id' => $category->getParentId(),
                    'is_active' => $category->getIsActive(),
                ];
            }
        } catch (\Exception $e) {
            return ['error' => 'Error fetching categories: ' . $e->getMessage()];
        }

        return $categories;
    }
     /**
     * Get customer info by ID
     * @param int $id  
     * @return array
     */
    public function getCustomerInfoById($id)
    {
        try {
            $customer = $this->customerRepository->getById($id);

            return [
                'id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $customer->getEmail(),
                'created_at' => $customer->getCreatedAt()
            ];
        } catch (NoSuchEntityException $e) {
            return ['error' => "Customer with ID {$id} not found."];
        } catch (\Exception $e) {
            return ['error' => "Error fetching customer data: " . $e->getMessage()];
        }
    }
}
