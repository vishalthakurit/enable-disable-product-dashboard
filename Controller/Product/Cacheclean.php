<?php
namespace Bistro\ProductDashboard\Controller\Product;
 
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Bistro\ProductDashboard\Helper\Data;
 
class Cacheclean extends Action
{ 
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
 
 
    /**
     * View constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context, 
        Data $helper,
        JsonFactory $resultJsonFactory
    ){
        $this->helper = $helper;
        $this->_resultJsonFactory = $resultJsonFactory; 
        parent::__construct($context);
    }
 
 
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $this->helper->cleanMagentoCache();
        return $result->setData([
            'success' => 'true'
        ]);
    } 
}