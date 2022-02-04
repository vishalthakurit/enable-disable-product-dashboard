<?php
namespace Bistro\ProductDashboard\Block;

use Magento\Framework\View\Element\Template;
use Bistro\ProductDashboard\Helper\Data;

class View extends Template
{
    protected $request;
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        Data $helper,
        array $data = []
    ){
        $this->request = $request;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getProductCollection(){
        return $this->helper->getProductCollection();
    }

    public function getStoreCode(){
        return $this->helper->getStoreCodeFromUrl();
    }

    // Function to get the client IP address
    public function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function allowAccess() 
    {
        $userIp = $this->getClientIp();
        $allowIps = $this->helper->getConfigValue('bristorestrictipconfugurtaion/bistro_allowip_config/allow_ips');
        $allowIpsArray = explode(',', $allowIps);
        if (in_array($userIp, $allowIpsArray))
        {
            return true;
        }
    }
}