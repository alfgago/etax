<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductCategory;
use App\CodigoIvaSoportado;
use App\CodigoIvaRepercutido;

class ProductCategoryController extends Controller
{
      
    public function getAllCategories(){
    	$categories = new ProductCategory();
        return $this->createResponse('200', 'OK' , 'Categorias Hacienda.', $categories->getAllCategories());  
    }
    
    
    public function getCategories($code){
    	$venta = CodigoIvaRepercutido::where('id',$code)->count();
    	$compra = CodigoIvaSoportado::where('id',$code)->count();
    	$total = $venta + $compra;
    	if($total == 0){
        	return $this->createResponse('400', 'ERROR' , 'Codigo no encontrado.', []); 
    	}
    	$categories = new ProductCategory();
        return $this->createResponse('200', 'OK' , 'Categorias Hacienda.', $categories->getCategories($code));        
    }

}
