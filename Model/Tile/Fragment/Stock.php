<?php

namespace MageSuite\ProductTile\Model\Tile\Fragment;

class Stock implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \MageSuite\ProductTile\Model\Command\GetStockNameForCurrentWebsite
     */
    protected $getStockNameForCurrentWebsite;

    /**
     * @var \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;

    /**
     * @var ?string
     */
    protected $currentStockName = null;

    public function __construct(
        \MageSuite\ProductTile\Model\Command\GetStockNameForCurrentWebsite $getStockNameForCurrentWebsite,
        \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->getStockNameForCurrentWebsite = $getStockNameForCurrentWebsite;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }

    public function isSaleable($product)
    {
        return $product->isSaleable();
    }

    public function getQtyForCurrentStock($sku)
    {
        $salableQtys = $this->getSalableQuantityDataBySku->execute($sku);

        if (empty($salableQtys)) {
            return null;
        }

        $currentStockName = $this->getCurrentStockName();

        foreach ($salableQtys as $salableQty) {
            if ($salableQty['stock_name'] == $currentStockName) {
                return $salableQty['qty'];
            }
        }

        return null;
    }

    protected function getCurrentStockName()
    {
        if ($this->currentStockName == null) {
            $this->currentStockName = $this->getStockNameForCurrentWebsite->execute();
        }

        return $this->currentStockName;
    }
}
