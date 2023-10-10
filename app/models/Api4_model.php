<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api4_model extends CI_Model {

    private $sales;
    public $select_fields;

    public function __construct() {
        parent::__construct();

        $this->sales = [];
    }
    
    /**
     * Add New Sales Request notification 
     * @param type $data
     * @return type
     */
    public function add_Sales_Request_Notification($data){
       
        $this->db->insert('notifications_purchases', $data);
        return ($this->db->affected_rows())?  TRUE: FALSE;
    }
    
    
    
   public function getSalesDetails($salesId ){
      $sales = $this->db->where(['id' => $salesId])->get('sales')->row();
      if($this->db->affected_rows()){
          
        $salesItems = $this->db->select('sale_items.*,product_variants.name as variants')->join('product_variants','product_variants.id = sale_items.option_id','left')->where(['sale_id'=> $salesId])->get('sale_items')->result();
   
        $sales->items = $salesItems;
        
       return $sales;
      }
      return false;
     
   }
   
   /**
    * 
    * @param type $productBarcode
    */
   public function getProdutsDetails($productBarcode){
     $productDetails =   $this->db->select('products.*,units.code as unit_code,units.name as unit_name, units.base_unit as unit_base_unit,units.operator as unit_operator,units.unit_value as unit_value,units.operation_value as unit_operation_value, categories.code as category_code, categories.name as category_name, subcategories.code as subcategories_code, subcategories.name as subcategories_name, brands.code as brand_code, brands.name as brand_name')
             ->join('units','units.id = products.unit','left')     
             ->join('categories','categories.id = products.category_id','left')
             ->join('categories as subcategories','subcategories.id = products.subcategory_id','left')
             ->join('brands','brands.id = products.brand','left')

             
             ->where_in('products.code',$productBarcode)->get('products')->result();
    $productData = [];
    foreach($productDetails as $items){
        $productvariant =  $this->db->where(['product_id'=> $items->id])->get('product_variants')->result();
         if($this->db->affected_rows()){
             $items->options = $productvariant;
         }else{
             $items->options = False;
         }
         $productData[] = $items;
     }
     
     
     return $productData;
   }


   /**
    * Manage Supplier API Key
    * @param type $supplierName
    * @param type $supplierkey
    * @return type
    */
   public function add_SupplierKey($supplierName, $data){
      
        $this->db->where(['name' => $supplierName,'group_name' => 'supplier'])->update('companies',['notification_supplier' =>$data]);
       return ($this->db->affected_rows())? TRUE : FALSE;
   }
           
}