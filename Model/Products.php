<?php
namespace Letscms\TestApi\Model;

use Letscms\TestApi\Api\ProductsInterfaces;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Products implements ProductsInterfaces
{
    protected $productRepository;
    protected $stockRegistry;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry
    ) {
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Update product price
     * @param string $sku
     * @param int $price
     * @return string
     */
    public function updatePrice($sku,$price)
    {
        // return $price;
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
     * Update product price
     * @param string $sku
     * @param int $price
     * @return string
     */
    public function updateQty($sku,$qty)
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
}
