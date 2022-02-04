<?php

namespace Bistro\ProductDashboard\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Message\ManagerInterface;

class Data extends AbstractHelper 
{         
    protected $_productCollectionFactory;
    protected $storeRepository;
    protected $request;
    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected  $_messageManager;
    
    public function __construct(
        Context $context, 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        ManagerInterface $messageManager
        )
    {
        $this->_productCollectionFactory = $productCollectionFactory; 
        $this->storeRepository= $storeRepository;
        $this->request = $request;
        $this->cacheManager = $cacheManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    public function getProductCollection()
    {
        $storeId = $this->getStoreIdByCode($this->request->getParam('store'));
        if($storeId){
            $collection = $this->_productCollectionFactory->create();
            $collection->setFlag('has_stock_status_filter', false);
            $collection->addStoreFilter($storeId);
            $collection->addAttributeToSelect(['name','sku','status','price','special_price']);
            $collection->addAttributeToFilter('allow_in_new_dashboard', ['eq' => 1]);
            return $collection;
        }        
    }

    public function updateProductStatus($productId, $productStatus)
    {
        try {
            $storeId = $this->getStoreIdByCode($this->request->getParam('storecode'));
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId)->setStoreId($storeId);
            if($productStatus == 1){
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
            } else {
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            }
            $product->save($product);
            return array('type' => 'success', 'message' => 'Product Status has been Updated.');
            //$this->_messageManager->addSuccess(__('Status has been updated.'));
        } catch (\Exception $e) {
            return array('type' => 'error', 'message' => $e->getMessage());
            //$this->_messageManager->addError(__($e->getMessage()));
        }
    }
   
    /**
     * Get Config value
     *
     * @return mixed $response
     */
    public function getConfigValue($configPath) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($configPath, $storeScope);
    }

    /**
     * Get Store code from url string parameter
     */
    public function getStoreCodeFromUrl(){
        return $this->request->getParam('store');
    }
    
    /**
     * Get Store ID by Store Code
     */
    public function getStoreIdByCode($storeCode) {
        try {
            $store = $this->storeRepository->get($storeCode);
            return $store->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // store not found
            //$this->_messageManager->addError(__('store not found'));
        }
    }

    /**
     * Clean Cache
     */
    public function cleanMagentoCache()
    {
        try {            
            $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
            $this->_messageManager->addSuccessMessage(__('Cache has been Cleaned Successfully.'));
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage(__($e->getMessage()));
        }
    }
}

