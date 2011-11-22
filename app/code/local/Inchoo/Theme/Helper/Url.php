<?php

class Inchoo_Theme_Helper_Url extends Mage_Core_Helper_Abstract
{
	
	public function getProductUrl($productId, $categoryId=null, $routeParams=array(), $storeId=null)
	{
		$routePath      = '';
        $storeId		= $storeId ? $storeId : Mage::app()->getStore()->getId();
		
		$idPath = sprintf('product/%d', $productId);
		if ($categoryId) {
			$idPath = sprintf('%s/%d', $idPath, $categoryId);
		}
		
		$rewrite = Mage::getModel('core/url_rewrite')
					->setStoreId($storeId)
                    ->loadByIdPath($idPath);
		
		if ($rewrite->getId()) {
			$routeParams['_direct'] = $rewrite->getRequestPath();
		} else {
            $routePath = 'catalog/product/view';
            $routeParams['id']  = $productId;
            if ($categoryId) {
                $routeParams['category'] = $categoryId;
            }
        }
		
		return Mage::getModel('core/url')->setStore($storeId)->getUrl($routePath, $routeParams);
	}
	
	public function getCategoryUrl($categoryId, $routeParams=array(), $storeId=null)
	{
		$routePath      = '';
		$storeId		= $storeId ? $storeId : Mage::app()->getStore()->getId();
		
		$idPath	 = 'category/' . $categoryId;
		
		$rewrite = Mage::getModel('core/url_rewrite')
					->setStoreId($storeId)
					->loadByIdPath($idPath);

		if ($rewrite->getId()) {
			$routeParams['_direct'] = $rewrite->getRequestPath();
		} else {
			$routePath = 'catalog/category/view';
			$routeParams['id']  = $categoryId;
		}
		
		return Mage::getModel('core/url')->getDirectUrl($routePath, $routeParams);
		

		/*
        if ($category instanceof Mage_Catalog_Model_Category) {
            return $category->getUrl();
        }
        return Mage::getModel('catalog/category')
            ->load($category)
            ->getUrl();
		*/
	}
	
}