<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Storeapp_model extends CI_Model {

    public $errors;
    public $messages;
    public $basePath;
    public $apiResponce;
    public $apiUrl;

    public function __construct() {
        parent::__construct();

        //initialize messages and error
        $this->messages = array();
        $this->errors = array();
        $this->basePath = base_url();
        $this->apiResponce = '';
        $this->apiUrl = '';
    }      

    public function postUrl($url, $data = array()) {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $postDataArr[] = $key . "=" . $value;
            }

            $postData = join('&', $postDataArr);
        } else {
            $postData = '';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "postman-token: 3bda5de7-1610-baef-2618-ff16b9dce0da"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "API Error :" . $err;
            exit;
        } else {
            return $response;
        }
    }

    public function isJSON($string) {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    public function JSon2Arr($string) {

        if ($this->isJSon($string)) {
            return (array) json_decode($string, true);
        }

        return $string;
    }

    public function Arr2JSon(array $arr) {

        if (is_array($arr)) {
            return json_encode($arr, true);
        }

        return $arr;
    }

    public function get_store_settings() {
        
        $q =$this->db->select('eshop_order_tax, default_category, default_biller, rounding, default_eshop_warehouse, default_eshop_pay, eshop_cod, eshop_free_delivery_on_order')
                ->get('pos_settings');
         
        
        if ($q->num_rows() > 0) {
           $data = (array)$q->result();
           return $data[0];
        }
        
    }
    
    public function get_categories() {
      
        $q = $this->db->select('id, code, name, image, parent_id')->order_by('name', 'asc')->get('categories');
        
        if ($q->num_rows() > 0) {
            
            foreach ($q->result() as $row) {
                
                if((int)$row->parent_id > 0) {
                    $data[$row->parent_id][$row->id] = $row;
                } else {
                    $data['main'][$row->id] = $row;
                }
            }
             
            return $data;
        }
        return false;
   
    }

    public function getCategoryProducts($category_id, $pageno = 1, $itemsPerPage = 18) {
         
        if (is_numeric($category_id)) {
            $data['count'] = 0;
        }  

        $offset = ( $pageno - 1 ) * $itemsPerPage;

        for ($i = 1; $i <= 2; $i++) {

            if ($i == 1) {
                $this->db->select('p.`id`');
            } else {
                $this->db->select("p.`id`, p.`code`, p.`name`, p.`unit`, p.`price`, p.`quantity`, p.`image`, p.`tax_rate` AS tax_rate_id, t.`rate` AS tax_rate, t.`name` AS tax_name, p.`tax_method`, p.category_id, p.subcategory_id,"
                    . "p.`promotion`, p.`promo_price`, p.`start_date`, p.`end_date`, p.`sale_unit`, u.name AS unit_name, "
                    . "pv.id as option_id, pv.name as option_name, pv.price as option_price , pv.quantity as option_quantity, pv.unit_quantity as option_unit_quantity ");                
            }
            
            $this->db->from('products AS p');
            
            $this->db->join('product_variants AS pv', 'p.id =  pv.product_id', 'left');
            $this->db->join('tax_rates AS t', 'p.tax_rate =  t.id', 'left');
            $this->db->join('units AS u', 'p.`sale_unit` =  u.id', 'left');
            
            $this->db->where(['p.category_id' => $category_id]);

            $this->db->or_where('p.subcategory_id', $category_id);

            if ($i == 2) {

                $offset = ($pageno - 1 ) * $itemsPerPage;

                $this->db->limit($itemsPerPage, $offset);
                //$this->db->limit($itemsPerPage);
            }
            $var = 'q' . $i;
            $$var = $this->db->get();
        }//end for.

        $count = $q1->num_rows();
        $data['count'] = $count;
        $data['totalPages'] = ceil($count / $itemsPerPage);

        if ($count > 0) {
            $data['msg'] = '<div class="alert alert-info">Result: ' . $count . ' products found.</div>';

            foreach (($q2->result()) as $row) {
                $data['items'][] = (array) $row;
            }
        } else {
            $data['msg'] = '<div class="alert alert-info">Products not found in this category</div>';
        }
        return $data;
    }
    
    public function getProductById($product_id) {
        
        $q = $this->db->where(['id' => $product_id])->get('products');
         
         if($q->num_rows() > 0) {
             
            $data = $q->result();
            return $data[0];
        }
            return false;
    }
    
    public function getProductsByCategory($category_id, $field_name=null, $order_id=null) {
        
        $this->db->select("p.`id`, p.`code`, p.`name`, p.`unit`, p.`price`, p.`quantity`, p.`image`, p.`tax_rate` AS tax_rate_id, t.`rate` AS tax_rate,"
                . " t.`name` AS tax_name, p.`tax_method`, p.category_id, p.subcategory_id, p.details, p.product_details, p.type AS product_type, p.brand, "
                . "p.`promotion`, p.`promo_price`, p.`start_date`, p.`end_date`, p.`sale_unit`, p.`weight`, u.name AS unit_name, "
                . "pv.id as option_id, pv.name as option_name, pv.price as option_price , pv.quantity as option_quantity, pv.unit_quantity as option_unit_quantity");     
        
        $this->db->from('product_variants AS pv');
            
            $this->db->join('products AS p', 'p.id =  pv.product_id', 'right');
            $this->db->join('tax_rates AS t', 'p.tax_rate =  t.id', 'left');
            $this->db->join('units AS u', 'p.`sale_unit` =  u.id', 'left');
            
//            if($order_id){                
//                $order_products = $this->get_order_products();
//                
//                $this->db->where_not_in('p.id', $order_products);
//            } 
                       
            if($field_name) {
                $this->db->where("p.$field_name" , $category_id);
            } else {
                $this->db->where(['p.category_id' => $category_id]);
                $this->db->orWhere(['p.subcategory_id' => $category_id]);
            }          
            
            $q = $this->db->get(); 
           
            if ($q->num_rows() > 0) {
                
                foreach ($q->result() as $row) {
                    $data[] = (array) $row;
                }
 
                return $data;
            }

            return false;
         
    }
    
    
    public function getProductsByKeyword($keyword , $order_id=null) {
       
            if($order_id){                
                $order_products = $this->get_order_products($order_id);
                
                $this->db->where_not_in('p.id', (array)$order_products);
            }
        
        $this->db->select("p.`id`, p.`code`, p.`name`, p.`unit`, p.`price`, p.`quantity`, p.`image`, p.`tax_rate` AS tax_rate_id, t.`rate` AS tax_rate,"
                . " t.`name` AS tax_name, p.`tax_method`, p.category_id, p.subcategory_id, p.details, p.product_details, p.type AS product_type, p.brand, "
                . "p.`promotion`, p.`promo_price`, p.`start_date`, p.`end_date`, p.`sale_unit`, p.`weight`, u.name AS unit_name, "
                . "pv.id as option_id, pv.name as option_name, pv.price as option_price , pv.quantity as option_quantity, pv.unit_quantity as option_unit_quantity");     
        
        $this->db->from('product_variants AS pv');
            
            $this->db->join('products AS p', 'p.id =  pv.product_id', 'right');
            $this->db->join('tax_rates AS t', 'p.tax_rate =  t.id', 'left');
            $this->db->join('units AS u', 'p.`sale_unit` =  u.id', 'left');
                                   
            if($keyword) {
                $this->db->like("p.code" , $keyword);
                $this->db->or_like("p.name" , $keyword);
                $this->db->or_like("p.product_details" , $keyword);
                $this->db->or_like("pv.name" , $keyword);
            }
            
            $q = $this->db->get(); 
           
            if ($q->num_rows() > 0) {
                
                foreach ($q->result() as $row) {
                    $data[] = (array) $row;
                }
 
                return $data;
            }

            return false;
         
    }
    
    public function get_order_products($order_id) {
        
            $this->db->select('product_id');
            $this->db->from('order_items');
            $this->db->where(['sale_id'=>$order_id]);
        $q = $this->db->get();
          
            if ($q->num_rows() > 0) {
                
                foreach ($q->result() as $row) {
                    $data[] = $row->product_id;
                }
 
                return $data;
            }
        return false;
    }
    
    public function getProducts($product_id, $variant_id=null) {
        
        $this->db->select("p.`id`, p.`code`, p.`name`, p.`unit`, p.`price`, p.`quantity`, p.`image`, p.`tax_rate` AS tax_rate_id, "
                    . "t.`rate` AS tax_rate, t.`type` AS tax_type, t.`name` AS tax_name, p.`tax_method`, p.category_id, p.subcategory_id, p.details, p.product_details, p.type AS product_type, p.brand, "
                    . "p.`promotion`, p.`promo_price`, p.`start_date`, p.`end_date`, p.`sale_unit`, p.`weight`, u.name AS unit_name, u.base_unit,"
                    . "p.article_code, p.cf1, p.cf2, p.mrp, p.hsn_code,"
                    . "pv.id as option_id, pv.name as option_name, pv.price as option_price , pv.quantity as option_quantity, pv.unit_quantity as option_unit_quantity ");     
        
        $this->db->from('products AS p');
            
            $this->db->join('product_variants AS pv', 'p.id =  pv.product_id', 'left');
            $this->db->join('tax_rates AS t', 'p.tax_rate =  t.id', 'left');
            $this->db->join('units AS u', 'p.`sale_unit` =  u.id', 'left');
            
            $this->db->where(['p.id' => $product_id]);
            if($variant_id){
               $this->db->where(['pv.id' => $variant_id]); 
            }
            $q = $this->db->get(); 

            if ($q->num_rows() > 0) {
                
                foreach ($q->result() as $row) {
                    $data[$row->id] = (array) $row;
                }

                return $data;
            }

            return false;
         
    }
        
    public function getProductVariants($product_id) {
         $q = $this->db->select('id, name, price, quantity, unit_quantity')
                ->where_in('product_id', $product_id)
                ->get('product_variants');

        if ($q->num_rows() > 0) {
                
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }

        return false; 
    }
    
    public function getProductsImages($product_id) {
        $q = $this->db->select('photo')
                ->where_in('product_id', $product_id)
                ->get('product_photos');

        if ($q->num_rows() > 0) {
                
            foreach ($q->result() as $row) {
                $data[] = $row->photo;
            }
            return $data;
        }

        return false;         
    }
        
    public function getOrders($order_id = null, $sale_status = null) {
        
        $select_fields = "id, invoice_no AS order_no, date, customer_id, customer, sale_status, payment_status, total_items, delivery_status,"
                . " total, product_discount, order_discount_id, order_discount, product_tax, order_tax_id, order_tax, shipping, grand_total, cgst, sgst, igst";
        
        $this->db->select($select_fields);
        $this->db->where('eshop_sale',1);
        if(!empty($sale_status)) {
            $this->db->where(['sale_status'=>$sale_status]);
        } else {
            $this->db->where(['sale_status'=>'pending']);
        }
        if($order_id) {
            $this->db->where('id',$order_id);
        }
        $this->db->order_by('date', 'desc');
        $q = $this->db->get('orders');
        
        if ($q->num_rows() > 0) {
                
                foreach ($q->result() as $row) {
                    $data[$row->id] = (array) $row;
                }
                
               if($order_id) {
                    return $data[$order_id];
               } else {
                    return $data;
               }
        }

        return false;
    }
    
    public function getOrderPayment($order_id = null) {
        
        $select_fields = "id, date,reference_no, transaction_id, paid_by, cheque_no, amount, type, pos_paid, pos_balance";
        
        $this->db->select($select_fields);
       
        if($order_id) {
            $this->db->where('order_id',$order_id);
        }
        $q = $this->db->get('payments');
        
        if ($q->num_rows() > 0) {
                
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            return $data;   
        }

        return NULL;
    }
    
    public function getOrderItems($order_id) {
        
        $this->db->select("o.`id`,o.`sale_id` AS order_id,o.`product_id`,o.`product_code`,o.`product_name`,o.`product_type`,o.`option_id`,"
                . "pv.`name` AS option_name, pv.`price` AS option_price,"
                . "o.`unit_price`, o.`quantity`, o.`net_price`, o.`warehouse_id`,o.`item_tax`, o.`tax_method`,"
                . "o.`tax_rate_id`, o.`tax`, o.`discount`, o.`item_discount`, o.`subtotal`, o.`real_unit_price`,  o.`unit_quantity`, o.`product_unit_id`,"
                . "o.`product_unit_code`, o.`note`, o.`gst_rate`, o.`cgst`, o.`sgst`, o.`igst`, o.`cf6_name` AS item_status, o.item_weight");
        
        $this->db->from('order_items AS o');
        $this->db->join('product_variants AS pv', 'o.option_id = pv.id', 'left');
        $this->db->where('o.sale_id', $order_id);  
        
        $q = $this->db->get();
        
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }

            return false;
    }
    
    public function getOrderItem($item_id) {
         
        $this->db->select("id, sale_id AS order_id, product_id, product_code, product_name, quantity, option_id, tax_method, tax_rate_id, real_unit_price, unit_price, product_unit_id,discount,note,unit_quantity,item_weight");
        $this->db->where('id', $item_id);
        $q = $this->db->get('order_items');
         
        if($q->num_rows() > 0) {
             
            $data = $q->result();
            return (array) $data[0];
        }
            return false;
     }
     
    public function getOrderProductIds($order_id) {
         
       $q = $this->db->select("product_id")->where('sale_id', $order_id)->get('orders');
         
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
           return false;
     }
    
    public function add_order_items($orderItems, $order_id) {
         
         $this->db->insert_batch('order_items', $orderItems);
         
         return $this->rewise_order($order_id);         
     }
     
    private function rewise_order($order_id) {
         
         $order_items = $this->getOrderItems($order_id);
         $i=0;
         
         if(is_array($order_items)){
             foreach ($order_items as $key => $item) {
                 
                $item_status = $item['cf6_name'];
                if(!in_array($item_status, ['deleted','out_of_stock'])){

                   $product_tax += $item['item_tax'];
                   $product_discount += $item['item_discount'];
                   $order_total += $item['subtotal'];
                   $i++;
                }//end if.
                 
             }//end foreach
             
             $item_grand_total = $order_total+$product_tax;
         }
         
         $orderData = $this->getOrders($order_id);
         
         $order_discount_id = $orderData['order_discount_id'];
          
         $shipping = $orderData['shipping'] ? $orderData['shipping'] : 0;
         
         if ($order_discount_id['order_discount_id']) {
            $order_discount_id = $order_discount_id['order_discount_id'];
            $opos = strpos($order_discount_id, '%');
            if ($opos !== false) {                
                $ods = explode("%", $order_discount_id);
                $order_discount = $this->sma->formatDecimal((($item_grand_total * (Float) ($ods[0])) / 100), 4);
            } else {
                $order_discount = $this->sma->formatDecimal($order_discount_id);
            }
        } else {
            $order_discount_id = null;
            $order_discount = null;
        }
        
        $total_discount = $this->sma->formatDecimal($order_discount + $product_discount);
        
        $grand_total = $item_grand_total - $total_discount + $shipping;
                
        $order_data = array(            
            "total" => $item_grand_total,
            "product_discount" => $product_discount,
            "order_discount" => $order_discount,
            "total_discount" => $total_discount,
            "product_tax" => $product_tax,
            "total_tax" => ($product_tax + $order['order_tax']),
            "grand_total" => $grand_total,
            "total_items" => $i,
        );
        
        return $this->updateOrders($order_data, $order_id);
     }

    public function setOrderItemStatus($item_id, $new_status){
        
        $data['cf6_name'] = $new_status;

        $this->db->where('id', $item_id);

        $result = $this->db->update('order_items', $data);
        
        if($this->db->affected_rows()){
            
            return TRUE;
        }        
        
        return FALSE;
    }
    
    public function updateOrders($orderData, $order_id , $payment = null) {
       
        $this->db->where('id', $order_id);

        $result = $this->db->update('orders', $orderData);
        
        if($this->db->affected_rows()) {
            
            if($payment) {
                $this->db->insert('payments', $payment);
            }
            return TRUE;
        }        
        
        return false;
    }
    
    public function updateOrderItems($order_item, $item_id) {
       
        $this->db->where('id', $item_id);

        $result = $this->db->update('order_items', $order_item);
        
        if($this->db->affected_rows()){
            
            $this->rewise_order($order_id);
            
            return TRUE;
        }        
        
        return FALSE;
    }
    
    public function getVarients($product_id = null) {
         
         $this->db->select('id,name,price,product_id,unit_quantity');
         $this->db->from('product_variants');
         if($product_id) {
            $this->db->where('product_id',$product_id); 
         }
         $q = $this->db->get();
         
         if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }

            return false;
    }
    
    public function getUnits($reff_unit = null) {
         
         $this->db->select('id,name,code,base_unit,operator,operation_value');
         $this->db->from('units');
         if($reff_unit) {
            $this->db->where('id',$reff_unit);        
            $this->db->or_where('base_unit',$reff_unit); 
         }
         $q = $this->db->get();
         
         if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }

            return false;
    }
    
    public function getTaxes() {
         
         $q = $this->db->get('tax_rates');
         
         if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }

            return false;
    }
    
    public function getCustomer($customer_id=null) {
        
        $this->db->select("c.`id`, c.`name`, c.`company`, c.`address`, c.`city`, c.`state`, c.`state_code`, c.`postal_code`, c.`phone`, c.`email`, c.`award_points`, c.`deposit_amount`, c.`gstn_no`");
        
        $this->db->from("companies AS c");
        
        if($customer_id) {       
           $this->db->where("c.`id`", $customer_id);
        } else {
           $this->db->where("c.group_name",'customer'); 
        }
       
        $this->db->order_by("c.name",'asc');
        
        $q = $this->db->get();
         
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            
           return $data;
        }

            return false;        
    }
    
    public function get_customer_payments($customer_id=null) {
        
        $this->db->select("s.`customer_id`, s.`customer`, count(s.`reference_no`) as sales , sum(s.`grand_total` + s.`rounding`) as sale_total, sum(p.`amount`) paid");
        $this->db->from("sales AS s");
        $this->db->join('payments AS p', 'p.`sale_id` = s.`id`', 'left');
       
        $this->db->group_by('s.customer_id');
        
        $q = $this->db->get();
        
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            
           return $data;
        }

            return false; 
    }
    
    public function get_pending_payments($customer_id=null, $listType = 'customers_list') {
              
        if($listType == 'customers_list'){
            $this->db->select("`customer`,`customer_id`, sum(`grand_total` + `rounding`) sale_total, sum(`paid`) paid, sum((`grand_total` + `rounding`) - `paid`) balance, count(`id`) sales_count");
            $this->db->from("sales");
            $this->db->where(" payment_status != 'paid' ");
            if($customer_id) { $this->db->where("customer_id", $customer_id); }
            $this->db->order_by('customer');        
            $this->db->group_by('customer_id');
        } else {
            $this->db->select("`id`,`invoice_no`,`date`,`customer`,`customer_id`, (`grand_total` + `rounding`) sale_total, `paid`, ((`grand_total` + `rounding`) - `paid`) balance, `payment_status`");
            $this->db->from("sales");
            $this->db->where(" payment_status != 'paid' ");
            if($customer_id) { $this->db->where("customer_id", $customer_id); }
            $this->db->order_by('date', 'desc');  
            $this->db->order_by('customer_id');
        }        
        
        $q = $this->db->get();
        
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            
           return $data;
        }

            return false; 
    }
    
    public function get_paid_payments($customer_id=null, $listType = 'customers_list') {
              
        if($listType == 'customers_list'){
            $this->db->select("`customer`,`customer_id`, sum(`grand_total` + `rounding`) sale_total, sum(`paid`) paid, sum((`grand_total` + `rounding`) - `paid`) balance, count(`id`) sales_count");
            $this->db->from("sales");
            $this->db->where(" payment_status = 'paid' ");
            if($customer_id) { $this->db->where("customer_id", $customer_id); }
            $this->db->order_by('customer');        
            $this->db->group_by('customer_id');
        } else {
            $this->db->select("`id`,`invoice_no`,`date`,`customer`,`customer_id`, (`grand_total` + `rounding`) sale_total, `paid`, ((`grand_total` + `rounding`) - `paid`) balance, `payment_status`");
            $this->db->from("sales");
            $this->db->where(" payment_status = 'paid' ");
            if($customer_id) { $this->db->where("customer_id", $customer_id); }
            $this->db->order_by('date', 'desc');  
            $this->db->order_by('customer_id');
        }        
        
        $q = $this->db->get();
        
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            
           return $data;
        }

            return false; 
    }
    
    public function get_sales($sale_id=null, $params=null) {
        
        $this->db->select("id, invoice_no, date, reference_no, customer_id, customer, biller_id, biller, seller_id, seller, note, staff_note, "
                . "order_discount, order_tax, shipping, grand_total, rounding, sale_status, payment_status, paid, cf1, cf2, delivery_status, order_no, "
                . "return_id, surcharge, return_sale_ref, sale_id, return_sale_total, cgst, sgst, igst");
        
        if($sale_id) {
            $this->db->where('id',$sale_id);
        }
        
        if(isset($params['customer_id']) && $params['customer_id']) {
            $this->db->where('customer_id',$params['customer_id']);
        }
        
        if(isset($params['payment_status'])) {
            if($params['payment_status'] == 'pending') {                
                $this->db->where_not_in('payment_status', ['paid']);
            }
            if($params['payment_status'] == 'paid') {                
                $this->db->where('payment_status', 'paid');
            }            
        }
        
        if(!empty($params['date_start']) && strtotime($params['date_start']) && !empty($params['date_end']) && strtotime($params['date_end'])) {            
            $this->db->where('DATE(date) >=', $params['date_start']);
            $this->db->where('DATE(date) <=', $params['date_end']);
        }
         
        $limit  = (isset($params['limit']) && $params['limit']) ? $params['limit'] : 10;
        $offset = (isset($params['offset']) && $params['offset']) ? $params['offset'] : 0;
        
        $this->db->limit($limit, $offset);
        
        $this->db->order_by('date','desc');
        
        $q = $this->db->get('sales');
         
        if($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            
           return $data;
        }

            return false;        
    }
    
    public function get_sale_items($sale_id=null) {
        
            $this->db->select("s.id, s.sale_id, s.product_id, s.product_code, s.product_name, s.product_type, s.unit_price, s.quantity,"
                    . " s.invoice_unit_price, s.unit_tax, s.item_tax, s.tax_method, s.tax, s.discount, s.unit_discount, s.item_discount, s.subtotal,"
                    . " s.real_unit_price, s.product_unit_id, s.product_unit_code, s.unit_quantity, s.cf1, s.cf2, s.mrp, s.hsn_code, s.note,"
                    . " s.gst_rate, s.cgst, s.sgst, s.igst, s.option_id, pv.name as option_name, pv.price as option_price ");

            $this->db->from('sale_items AS s');
            $this->db->join('product_variants AS pv', 's.option_id = pv.id', 'left');
            $this->db->where('s.sale_id', $sale_id);  
            
        $q = $this->db->get();
        
            if($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $data[$row['id']] = (array)$row;
                }

               return $data;
            }

            return false; 
    }
    
    public function get_payments($sale_id=null, $order_id=null) {
        
        $this->db->select("id, date, sale_id, return_id, reference_no, transaction_id, order_id, paid_by, cheque_no, amount, type");
        
        if($sale_id) {
            $this->db->where('sale_id', $sale_id); 
        }
        if($order_id) {
            $this->db->where('order_id', $order_id); 
        }
            
        $q = $this->db->get();
        
        if($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $data[$row['id']] = (array)$row;
                }

               return $data;
            }

            return false; 
    }
    
    public function addSaleByOrder($order_id) {       
        
        $order = $this->db->get_where('orders',['id'=>$order_id])->result()[0];
        
        $order->date         = date('Y-m-d H:i:s');
        $order->order_no     = $order->invoice_no;
        $order->created_by   = $this->session->userdata('user_id');
        $order->reference_no = $this->site->getReference('so');       
        $order->offline_sale = 0;
        
        unset($order->id);
        unset($order->sale_as_chalan);
        unset($order->sale_invoice_no);
        unset($order->invoice_no);
        unset($order->deliver_later);
        unset($order->time_slotes);
        
        $order_items = $this->db->where(['sale_id'=>$order_id])->where_not_in('cf6_name',['deleted','out_of_stock'])->get('order_items')->result();
        
        if(is_array($order_items)){
            foreach ($order_items as $key => $items) {
                
                unset($items->id);
                unset($items->sale_id);
                unset($items->updated_at);
                
                $products[] = (array)$items;
            }
        } 
        
        $extrasPara = array('order_id'=>$order_id, 'syncQuantity' => $syncQuantity );
        $payment = array();
        
        return $sale_id = $this->addSale((array)$order, $products, $payment, array(), $extrasPara);
        
    }
    
    public function addSale($data = array(), $items = array(), $payment = array(),$si_return = array(), $extrasPara = array() )
    {
        $this->load->model('sales_model');
        $this->load->model('orders_model');
        
        $cost = $this->site->costing($items);
         
        $order_id       = $extrasPara['order_id'] ? $extrasPara['order_id'] : null;
        $syncQuantity   = $extrasPara['syncQuantity'];
        
        $sma_sales = 'sales';
        $sma_sales_items = 'sale_items';
        $sma_sales_items_tax = 'sales_items_tax'; 
        $saleRefKey = 'so';
        $ReturnSaleRefKey = 're';
         
        if ($this->db->insert($sma_sales, $data)) {
            
            $sale_id = $this->db->insert_id();

            //Get formated Invoice No
            $invoice_no = $this->sma->invoice_format($sale_id,date());        
            //Update formated invoice no
            $this->db->where(['id'=>$sale_id])->update($sma_sales, ['invoice_no' => $invoice_no]);

            if ($this->site->getReference($saleRefKey) == $data['reference_no']) {
                $this->site->updateReference($saleRefKey);
            }
            if ($this->site->getReference($ReturnSaleRefKey) == $data['return_sale_ref']) {
               $this->site->updateReference($ReturnSaleRefKey);
            }
	    $Setting = $this->Settings;
            
            foreach ($items as $item) {
		//------------------Change For  Pharma for  saving Exp. date & Batch No ----------------//
                $_prd       =   $Setting->pos_type=='pharma' ?$this->site->getProductByID($item['product_id']):NULL;
                $item['cf1'] = $Setting->pos_type=='pharma' ?$_prd->cf1:'';
                $item['cf2'] = $Setting->pos_type=='pharma' ?$_prd->cf2:'';
                //------------------ End ----------------//
                $item['sale_id'] = $sale_id;
                $this->db->insert($sma_sales_items, $item);
                $sale_item_id = $this->db->insert_id();
                    
                $_taxSaleID =  $sale_id;
                
                $_tax_type =  NULL;
                
                $taxAtrr = $this->sma->taxAtrrClassification($item['tax_rate_id'], $item['net_unit_price'], $item['unit_quantity'], $sale_item_id, $_taxSaleID , $_tax_type);
                
                if($data['sale_status'] == 'completed') {

                    $item_costs = $this->site->item_costing($item);
                    
                    foreach ($item_costs as $item_cost) {
                        if (isset($item_cost['date'])) {                            
                           
                            $item_cost['sale_item_id'] = $sale_item_id;
                            $item_cost['sale_id'] = $sale_id;
                            
                            
                            if(! isset($item_cost['pi_overselling'])) {
                                $this->db->insert('costing', $item_cost);
                            }
                        } else {
                            foreach ($item_cost as $ic) {
                            	if(is_array($ic)):
                                     
                                    $ic['sale_item_id'] = $sale_item_id;
                                    $ic['sale_id']      = $sale_id;
                                    
                                    if(! isset($ic['pi_overselling'])) {
                                        $this->db->insert('costing', $ic);
                                    }
                                endif;
                            }
                        }
                    }
                }                         
            }            

            if ($data['sale_status'] == 'completed' && $syncQuantity) {
                
                $this->site->syncPurchaseItems($cost);
            }

            if (!empty($si_return)) {
                foreach ($si_return as $return_item) {
                    $product = $this->site->getProductByID($return_item['product_id']);
                    if ($product->type == 'combo') {
                        $combo_items = $this->site->getProductComboItems($return_item['product_id'], $return_item['warehouse_id']);
                        foreach ($combo_items as $combo_item) {                            
                             
                             $this->sales_model->updateCostingLine($return_item['id'], $combo_item->id, $return_item['quantity']);
                             $this->sales_model->updatePurchaseItem(NULL,($return_item['quantity']*$combo_item->qty), NULL, $combo_item->id, $return_item['warehouse_id']);
                        }
                    } else {
                       
                         $this->sales_model->updateCostingLine($return_item['id'], $return_item['product_id'], $return_item['quantity']);
                         $this->sales_model->updatePurchaseItem(NULL, $return_item['quantity'], $return_item['id']);
                         
                    }
                }
                $this->db->update($sma_sales, array('return_sale_ref' => $data['return_sale_ref'], 'surcharge' => $data['surcharge'],'return_sale_total' => $data['grand_total'], 'return_id' => $sale_id), array('id' => $data['sale_id']));
            }

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid') {
                               
                $payment['sale_id']  = $sale_id;
                
                $this->db->where(['order_id'=>$order_id])->update('payments', $payment);                 
            }
            
            if($syncQuantity) {                
                $this->site->syncQuantity($sale_id);                   
            }            
            
            if ($this->Settings->synch_reward_points) {
                $this->sma->update_award_points($data['grand_total'], $data['customer_id'], $data['created_by']);
            }
            
            return $sale_id;
        }

        return false;
    }

    public function addOrder($data = array(), $items = array(), $payment = array() )
    {
        if ($this->db->insert('orders', $data)) {
            
            $order_id = $this->db->insert_id();

            //Get formated Invoice No
            $invoice_no = $this->sma->invoice_format($order_id,date());        
            //Update formated invoice no
            $this->db->where(['id'=>$order_id])->update('orders', ['invoice_no' => $invoice_no]);

            if ($this->site->getReference('ordr') == $data['reference_no']) {
                $this->site->updateReference('ordr');
            }
            
	    $Setting =  $this->Settings;
            
            foreach ($items as $item) {
		//------------------Change For  Pharma for  saving Exp. date & Batch No ----------------//
                $_prd        = $Setting->pos_type=='pharma' ?$this->site->getProductByID($item['product_id']):NULL;
                $item['cf1'] = $Setting->pos_type=='pharma' ?$_prd->cf1:'';
                $item['cf2'] = $Setting->pos_type=='pharma' ?$_prd->cf2:'';
                //------------------ End ----------------//
                $item['sale_id'] = $order_id;
                $this->db->insert('order_items', $item);
                $sale_item_id = $this->db->insert_id();                    
            }//end foreach.            
  

            if ($data['payment_status'] == 'partial' || $data['payment_status'] == 'paid') {
                               
                $payment['order_id']  = $order_id;
                
                $this->db->insert('payments', $payment); 
                
                if ($this->site->getReference('pay') == $payment['reference_no']) {
                    $this->site->updateReference('pay');
                }
            }  
            
            return $order_id;
        }

        return false;
    }
    
    
    
    

}
