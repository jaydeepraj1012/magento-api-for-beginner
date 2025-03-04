<?php

namespace Letscms\TestApi\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface ProductsInterfaces
{
  /**
   * Update product price
   * @param string $sku
   * @param int $price
   * @return string
   */
  public function updatePrice($sku, $price);
  /**
   * Update product price
   * @param string $sku
   * @param int $qty
   * @return string
   */
  public function updateQty($sku, $qty);

  /**
   * Get product info by SKU
   * @param string $skuid    
   * @return ProductInterface
   * @throws \Magento\Framework\Exception\NoSuchEntityException
   */
  public function getProductBySku($skuid);

  /**
   * Get all categories list
   * @return array
   */
  public function getCategoriesList();
  /**
   * Get all Customer Info list
   *  * @param int $id  
   * @return array
   */
  public function getCustomerInfoById($id);

  /**
   * Get all Customer Info list
  
   * @return string
   */
  public function getOrderCount();
   /**
     * Get Customer's last order
     * 
     * @param int $id
     * @return Magento\Sales\Api\Data\OrderInterface;
     */
  public function getCustomerlastOrder($id);
}
