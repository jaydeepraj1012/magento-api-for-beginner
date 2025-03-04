<?php
namespace Letscms\TestApi\Model;

use Letscms\TestApi\Api\ProductsInterfaces;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item as StockResource;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Api\Data\OrderInterface;


class Products implements ProductsInterfaces
{
    protected $productFactory;
    protected $productResource;
    protected $stockItemRepository;
    protected $stockResource;
    protected $orderCollectionFactory;
    protected $customerFactory;
    protected $customerResource;
    protected $categoryCollectionFactory;

    public function __construct(
        ProductFactory $productFactory,
        ProductResource $productResource,
        StockItemRepository $stockItemRepository,
        StockResource $stockResource,
        CustomerFactory $customerFactory,
        OrderCollectionFactory $orderCollectionFactory,
        CustomerResource $customerResource,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockResource = $stockResource;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
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
            $product = $this->productFactory->create();
            $this->productResource->load($product, $sku, 'sku');

            if (!$product->getId()) {
                return ["error" => "Product not found: " . $sku];
            }

            $product->setPrice($price);
            $this->productResource->save($product);

            return ["message" => "Price updated successfully for SKU: " . $sku];
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
            $stockItem = $this->stockItemRepository->get($sku);
            if (!$stockItem->getItemId()) {
                return ["error" => "Stock item not found for SKU: " . $sku];
            }

            $stockItem->setQty($qty);
            $stockItem->setIsInStock($qty > 0);
            $this->stockResource->save($stockItem);

            return ["message" => "Quantity updated successfully."];
        } catch (\Exception $e) {
            return ["error" => "Error updating quantity: " . $e->getMessage()];
        }
    }

    /**
     * Get product info by SKU
     * @param string $skuid    
     * @return array
     */
    public function getProductBySku($skuid)
    {
        try {
            $product = $this->productFactory->create();
            $this->productResource->load($product, $skuid, 'sku');

            if (!$product->getId()) {
                return ["error" => "Product not found."];
            }

            return [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $product->getPrice()
            ];
        } catch (\Exception $e) {
            return ["error" => "Error fetching product: " . $e->getMessage()];
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
            $customer = $this->customerFactory->create();
            $this->customerResource->load($customer, $id);

            if (!$customer->getId()) {
                return ['error' => "Customer with ID {$id} not found."];
            }

            return [
                'id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $customer->getEmail(),
                'passwords' => $customer->getPasswordHash(),
                'created_at' => $customer->getCreatedAt()
            ];
        } catch (\Exception $e) {
            return ['error' => "Error fetching customer data: " . $e->getMessage()];
        }
    }
     /**
     * Get order count
   
     * @return string
     */
    public function getOrderCount()
    {
        $orderCollection = $this->orderCollectionFactory->create();
        return $orderCollection->getSize();
    }
    /**
     * Get Customer's last order
     * 
     * @param int $id
     * @return OrderInterface|null
     */
    public function getCustomerLastOrder($id): ?array
{
    $collection = $this->orderCollectionFactory->create()
        ->addFieldToFilter('customer_id', $id)
        ->setOrder('created_at', 'DESC')
        ->setPageSize(1);

    $order = $collection->getFirstItem();

    if (!$order->getId()) {
        return null;
    }

    return [
        'order_id' => $order->getId(),
        'status' => $order->getStatus(),
        'Store Id' => $order->getStoreId(),
        'total' => $order->getGrandTotal(),
        'created_at' => $order->getCreatedAt(),
        'shipping_method' => $order->getShippingMethod(),
    ];
}

    
}
