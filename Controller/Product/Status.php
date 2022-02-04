<?php
namespace Bistro\ProductDashboard\Controller\Product;
 
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Bistro\ProductDashboard\Helper\Data;
 
class Status extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
 
 
    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context, 
        Data $helper,
        PageFactory $resultPageFactory, 
        JsonFactory $resultJsonFactory
    ){
        $this->helper = $helper;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory; 
        parent::__construct($context);
    }
 
 
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $productId = $this->getRequest()->getParam('product_id');        
        $productStatus = $this->getRequest()->getParam('product_status'); 
        $response = $this->helper->updateProductStatus($productId, $productStatus);
        if($response['type'] == 'success'){
            return $result->setData([
                'type' => $response['type'],
                'productid' => $productId,
                'message' => $this->messageManager->addSuccessMessage($response['message'])
            ]);
        } else {
             return $result->setData([
                'type' => $response['type'],
                'productid' => $productId,
                'message' => $this->messageManager->addErrorMessage($response['message'])
            ]);
        }
    } 
}