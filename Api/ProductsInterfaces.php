<?php
namespace Letscms\TestApi\Api;

interface ProductsInterfaces
{
   /**
     * Update product price
     * @param string $sku
     * @param int $price
     * @return string
     */
    public function updatePrice($sku,$price);
   /**
     * Update product price
     * @param string $sku
     * @param int $qty
     * @return string
     */
    public function updateQty($sku,$qty);
}
