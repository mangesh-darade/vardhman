<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);

class Storeapp extends MY_Controller {

    public $data = [];   
    public $settings = '';
    public $store_settings = '';
    private $ci;
    private $ajaxPost = [];
   
    public function __construct() {
        
        parent::__construct();
        
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        
        $this->view_path = 'default/views/storeapp/';

        $this->data['user_id'] = $this->session->userdata('user_id');
        $this->data['user_name'] = $this->session->userdata('username');

        $this->data['assets'] = base_url() . "themes/default/assets/";
        $this->data['pimage'] = base_url() . 'assets/uploads/';
        $this->data['thumbs'] = base_url() . 'assets/uploads/thumbs/';

        $this->data['baseurl'] = base_url();
 
        $this->ci = get_instance();

        $this->load->helper('genfun_helper');
        $this->load->model('storeapp_model');
        
        $this->Settings = $this->data['Settings']; 
        
        $this->store_settings = $this->storeapp_model->get_store_settings();
        
            
    }
    
    private function __load_view($page = '', $data = array()) {

        $this->load->view($this->view_path . $page, $data);
    }

    public function index() {
        
        $this->data['pagename'] = "DASHBOARD";
        $this->__load_view( 'dashboard', $this->data);
    }
    
    public function orders() {
        
        $this->data['message'] = $this->session->flashdata('message');
        
        $this->data['pagename'] = "ORDERS";
        
        $this->__load_view( 'orders', $this->data);
    }
    
    public function get_orders($postData) {
        
        $status = $postData['order_status'];
        
        $this->data['orders'] = $this->storeapp_model->getOrders(null, $status); 
        
        $this->__load_view( 'order_list', $this->data);
    }
    
    public function update_order() {
       
        $order = $this->input->post();
        
//        echo '<pre>';
//        print_r($order);
//        echo '</pre>';
//        exit;
        
        $order_id = $order['order_id'];
        $product_tax = $cgst = $sgst = $igst = $product_discount = $grand_total = $total_weight = 0;
            
        if(is_array($order['item_id']) && count($order['item_id'])){
            
            foreach ($order['item_id'] as $item_id) {
                
                $product_tax += $order['item_tax'][$item_id];
                $cgst += $order['cgst'][$item_id];
                $sgst += $order['sgst'][$item_id];
                $igst += $order['igst'][$item_id];
                $product_discount += $order['item_discount'][$item_id];
                $grand_total += $order['subtotal'][$item_id];
                $total_weight += $order['item_weight'][$item_id];
            }//end foreach
        }//end if
        
        $total = $grand_total - $product_tax;
        
        if ($order['order_discount']) {
            $order_discount_id = $order['order_discount'];
            $opos = strpos($order_discount_id, '%');
            if ($opos !== false) {
                $ods = explode("%", $order_discount_id);
                $order_discount = $this->sma->formatDecimal((($grand_total * (Float) ($ods[0])) / 100), 4);
            } else {
                $order_discount = $this->sma->formatDecimal($order_discount_id);
            }
        } else {
            $order_discount_id = null;
            $order_discount = null;
        }
        $total_discount = $this->sma->formatDecimal($order_discount + $product_discount);
        
        $paid_amount = $order['paid_amount'] ? $order['paid_amount'] : 0;
        $now = date('Y-m-d H:i:s');
        
        $rounding = '';

        if ($this->store_settings->rounding > 0) {
            $round_total = $this->sma->roundNumber($grand_total, $this->store_settings->rounding);
            $rounding = $this->sma->formatDecimal($round_total - $grand_total,2);
        }
        
        if($order['payment_action'] == 'paid' && $order['payment_amount'] > 0) {
            $payment = array(
                "date"   => $now,
                "reference_no"   => $this->site->getReference('pay'),
                "transaction_id" => $order['transaction_no'],
                "order_id"       => $order_id,
                "paid_by"        => $order['payment_mode'],
                "cheque_no"      => ($order['payment_mode'] == 'cheque' ? $order['transaction_no'] : ''),
                "amount"         => $order['payment_amount'],
                "type"           => 'received',
            );
            
            $paid_amount += $order['payment_amount'];
        } else {
            $payment = null;
        }
        
        $payment_status = $paid_amount == 0 ? 'due' : ( $order['order_total'] > $paid_amount ? 'partial' : 'paid');
                
        $orderData = array(            
            "staff_note"        => $order['staff_note'],
            "total"             => $total,            
            "product_discount"  => $product_discount,            
            "order_discount_id" => $order_discount_id,
            "order_discount"    => $order_discount,
            "total_discount"    => $total_discount,
            "product_tax"       => $product_tax,
            "total_tax"         => $product_tax,
            "shipping"          => $order['shipping'],
            "grand_total"       => $grand_total,            
            "sale_status"       => $order['order_status'],
            "payment_status"    => $payment_status,
            "rounding"          => $rounding,
            "total_items"       => $order['total_items'],
            "paid"              => $paid_amount,
            "cgst"              => $cgst,
            "sgst"              => $sgst,
            "igst"              => $igst,
            "updated_at"        => $now,
            "total_weight"      => $total_weight,
            "created_by"        => $this->session->userdata('user_id'),
        );
            
        
        if( $this->storeapp_model->updateOrders($orderData, $order_id, $payment) ){
            
            if($order['order_status'] == "completed") {                
                
                if($this->storeapp_model->addSaleByOrder($order_id)){
                    $this->session->set_flashdata('message', 'Order Completed Successfully');
                    redirect('storeapp/orders/completed');
                }
            } else {
                $this->session->set_flashdata('message', 'Order Updates Successfully');
                redirect('storeapp/orders/'.$order['order_status']);
            }
            
        } else {
           $this->session->set_flashdata('error', 'Order Updates Failed');
           redirect('storeapp/order_items/'.$order_id); 
        }
        
    }
    
    public function order_items($order_id) {
        
        if(!$order_id) { redirect('storeapp/orders'); }
        
        $this->data['error'] = $this->session->flashdata('error');
        
        $this->data['order'] = $this->storeapp_model->getOrders($order_id); 
        
        $this->data['payments'] = $this->storeapp_model->getOrderPayment($order_id);
        
        $this->data['categories'] = $this->storeapp_model->get_categories();        
         
        $this->data['pagename'] = "ORDER ITEMS";
        
        $this->__load_view( 'order_items', $this->data);
    }
        
    public function model_edit_order_item($postData) {
        
        $item_id = $postData['item_id'];
        
        $this->data['itemData'] = $item = $this->storeapp_model->getOrderItem($item_id); 
        $this->data['units'] = $this->storeapp_model->getUnits($item['product_unit_id']); 
        $this->data['taxes'] = $this->storeapp_model->getTaxes(); 
        $this->data['varients'] = $this->storeapp_model->getVarients($item['product_id']); 
          
        $this->__load_view('model_edit_order_item', $this->data);  
    }
    
    public function model_edit_order_new_item($postData) {
        
        $this->data['item_id'] = $postData['item_id'];
        $prodids = explode('_', $postData['product']);
        $product_id = $prodids[0];
        $option_id = $prodids[1] ? $prodids[1] : null;

        $products = (array)$this->storeapp_model->getProducts($product_id, $option_id); 
        $this->data['itemData'] = $products[$product_id];
        $this->data['units'] = $this->storeapp_model->getUnits($products[$product_id]['unit']); 
        $this->data['taxes'] = $this->storeapp_model->getTaxes(); 
        $this->data['varients'] = $this->storeapp_model->getVarients($product_id); 
        $this->data['itemData']['option_id'] = $option_id;
        
        $this->__load_view('model_edit_order_new_item', $this->data);  
    }
    
    public function edit_order_items($postData) {
        
//        echo '<pre>';
//        print_r($postData);
//        echo '</pre>';
//        exit;
        
        $item_id            = $postData['item_id'];
        $item_tax_rate      = $postData['tax_id'];
        $item_quantity      = $postData['quantity'];
        $real_unit_price    = $postData['real_unit_price'];
        $tax_method         = $postData['tax_method'];
        $item_unit          = $postData['unit'];
        $item_option        = $postData['varient'];
        $product_id         = $postData['product_id'];
        $order_id           = $postData['order_id'];
        
        $unit_price         = $postData['unit_price'];;
        $item_discount      = $postData['product_discount'];
        $item_note          = $postData['item_note'];
        $item_unit_quantity = $postData['item_unit_quantity'];
        $item_weight        = $postData['item_weight'];
        $customer_id        = $postData['customer_id'];
        
        $variant_unit_quantity  = $postData['variant_unit_quantity'];
            
        if (isset($product_id) && isset($real_unit_price) && isset($unit_price) && isset($item_quantity)) {
            
            $product_details = $item_type != 'manual' ? $this->storeapp_model->getProductById($product_id) : null;
            $item_mrp = $product_details->mrp;
            $pr_discount = 0;
                    
            if (isset($item_discount)) {
                $discount = $item_discount;
                $dpos = strpos($discount, '%');
                if ($dpos !== false) {
                    $pds = explode("%", $discount);
                    $pr_discount = $this->sma->formatDecimal(((($this->sma->formatDecimal($unit_price)) * (Float) ($pds[0])) / 100), 4);
                } else {
                    $pr_discount = $this->sma->formatDecimal($discount);
                }
            }
            
            $unit_discount = $pr_discount;
            $item_unit_price_less_discount = $this->sma->formatDecimal($unit_price - $unit_discount, 6);
            $item_net_price = $net_unit_price = $item_unit_price_less_discount;
            
            $pr_item_discount = $this->sma->formatDecimal($pr_discount * $item_unit_quantity);
            $product_discount += $pr_item_discount;
            $pr_tax = 0;
            $pr_item_tax = 0;
            $item_tax = 0;
            $tax = "";
            $net_unit_price = $item_unit_price_less_discount;
            $unit_price = $item_unit_price_less_discount;
            $invoice_unit_price = $item_unit_price_less_discount;
            $invoice_net_unit_price = ($item_unit_price_less_discount + $unit_discount);
            
            if (isset($item_tax_rate) && $item_tax_rate != 0) {
            
                $pr_tax = $item_tax_rate;
                $tax_details = $this->site->getTaxRateByID($pr_tax);
                if ($tax_details->type == 1 && $tax_details->rate != 0) {

                    if ($tax_method == 1) {
                        $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / 100, 4);
                        $tax = $tax_details->rate . "%";

                        $net_unit_price = $item_unit_price_less_discount;
                        $unit_price = $item_unit_price_less_discount + $item_tax;

                        $invoice_unit_price = $item_unit_price_less_discount;
                        $invoice_net_unit_price = $item_unit_price_less_discount + $unit_discount + $item_tax;
                    } else {
                        $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                        $tax = $tax_details->rate . "%";
                        $item_net_price = $unit_price - $item_tax;

                        $net_unit_price = $item_unit_price_less_discount - $item_tax;
                        $unit_price = $item_unit_price_less_discount;

                        $invoice_unit_price = $item_unit_price_less_discount - $item_tax;
                        $invoice_net_unit_price = $item_unit_price_less_discount + $unit_discount;
                    }

                    $unit_tax = $item_tax;
                } elseif ($tax_details->type == 2) {

                    if ($tax_method == 1) {
                        $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / 100, 4);
                        $tax = $tax_details->rate . "%";

                        $net_unit_price = $item_unit_price_less_discount;
                        $unit_price = $item_unit_price_less_discount + $item_tax;

                        $invoice_unit_price = $item_unit_price_less_discount;
                        $invoice_net_unit_price = $item_unit_price_less_discount + $unit_discount + $item_tax;
                    } else {
                        $item_tax = $this->sma->formatDecimal((($unit_price) * $tax_details->rate) / (100 + $tax_details->rate), 4);
                        $tax = $tax_details->rate . "%";
                        $item_net_price = $unit_price - $item_tax;

                        $net_unit_price = $item_unit_price_less_discount - $item_tax;
                        $unit_price = $item_unit_price_less_discount;

                        $invoice_unit_price = $item_unit_price_less_discount - $item_tax;
                        $invoice_net_unit_price = $item_unit_price_less_discount + $unit_discount;
                    }

                    $item_tax = $this->sma->formatDecimal($tax_details->rate);
                    $tax = $tax_details->rate;
                }
                $pr_item_tax = $this->sma->formatDecimal($item_tax * $item_unit_quantity, 4);
                $unit_tax = $item_tax;
                
                
                $customer_details = $this->site->getCompanyByID($customer_id);
                
                if ((!empty($customer_details->state_code) && !empty($biller_details->state_code)) && $customer_details->state_code != $biller_details->state_code) {
                    $interStateTax = true;
                } else {
                    $interStateTax = false;
                }
            
                if ($interStateTax) {
                    $item_gst = $tax_details->rate;
                    $item_cgst = 0;
                    $item_sgst = 0;
                    $item_igst = $pr_item_tax;
                } else {
                    $item_gst = $this->sma->formatDecimal($tax_details->rate / 2, 4);
                    $item_cgst = $this->sma->formatDecimal($pr_item_tax / 2, 4);
                    $item_sgst = $this->sma->formatDecimal($pr_item_tax / 2, 4);
                    $item_igst = 0;
                }
                
            }
            
                $invoice_unit_price = $this->sma->formatDecimal($invoice_unit_price, 4);
                $invoice_net_unit_price = $this->sma->formatDecimal($invoice_net_unit_price, 4);
                $invoice_total_net_unit_price = $this->sma->formatDecimal(($invoice_net_unit_price * $item_quantity), 4);
                $product_tax += $pr_item_tax;
                $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);
                $unit = $this->site->getUnitByID($item_unit);
                $net_price = $this->sma->formatDecimal(($item_mrp * $item_quantity), 4);
            
                $order_item = array(                        
                        'option_id'                     => $item_option,
                        'net_unit_price'                => $item_net_price,
                        'unit_price'                    => $this->sma->formatDecimal($item_net_price + $item_tax),
                        'quantity'                      => $item_quantity,
                        'product_unit_id'               => $item_unit,
                        'product_unit_code'             => $unit->code,
                        'unit_quantity'                 => $item_unit_quantity,            
                        'item_weight'                   => $item_weight,            
                        'item_tax'                      => $pr_item_tax,
                        'tax_rate_id'                   => $pr_tax,
                        'tax'                           => $tax,
                        'tax_method'                    => $tax_method,
                        'discount'                      => $item_discount,
                        'item_discount'                 => $pr_item_discount,
                        'subtotal'                      => $this->sma->formatDecimal($subtotal),
                        'net_price'                     => $net_price,            
                        'unit_discount'                 => $unit_discount,
                        'unit_tax'                      => $unit_tax,
                        'invoice_unit_price'            => $invoice_unit_price,
                        'invoice_net_unit_price'        => $invoice_net_unit_price,
                        'invoice_total_net_unit_price'  => $invoice_total_net_unit_price,
                        'gst_rate'                      => $item_gst,
                        'cgst'                          => $item_cgst,
                        'sgst'                          => $item_sgst,
                        'igst'                          => $item_igst,
                    );
                
                           
            if($this->storeapp_model->updateOrderItems($order_item, $item_id)){
                $data['status'] = "SUCCESS";
            } else {
                $data['status'] = "ERROR";
            }
            
            echo json_encode($data);
        }
            
        
    }
    
    public function get_order_items($postData) {
        
       $order_id = $postData['order_id'];
            
       $this->data['store_settings_rounding'] = $this->store_settings->rounding;
       $this->data['order_items'] = $this->storeapp_model->getOrderItems($order_id);
            
       $this->__load_view('order_items_list', $this->data);       
    }
    
    public function update_order_item_status($postData) {
        
        $item_id = $postData['item_id'];
        $new_status = $postData['new_status'];
        
       $result = $this->storeapp_model->setOrderItemStatus($item_id, $new_status);
       
       if($result === TRUE){
           $data['status'] = 'SUCCESS';
       } else {
           $data['status'] = 'ERROR';
       }
        
       echo json_encode($data);
    }
    
    public function products($category_id=null, $pageno=1) {
        
        $this->data['categories'] = $this->storeapp_model->get_categories();
        
        $this->data['pagename'] = "PRODUCTS";
        
        $this->__load_view( 'products', $this->data);
    }
    
    public function search_products($postData) {
        
       $search_by   = $postData['search_by'];
       $order_id    = $postData['order_id'];
       
       if($search_by == "CATEGORY"){
           
           $field_name = $postData['searchField'];
           $category_id = $postData['searchValue'];           
            
           $this->data['product_list'] = $this->storeapp_model->getProductsByCategory($category_id, $field_name, $order_id);
       }
       
       if($search_by == "KEYWORDS"){
           $keyword = $postData['searchValue'];
            $this->data['product_list'] = $this->storeapp_model->getProductsByKeyword($keyword, $order_id);  
       }
       
       $this->__load_view('search_products', $this->data); 
       
    }
    
    public function products_list($postData) {
        
       $search_by   = $postData['search_by'];
       
       if($search_by == "CATEGORY"){
           
           $field_name = $postData['searchField'];
           $category_id = $postData['searchValue'];           
            
           $this->data['product_list'] = $this->storeapp_model->getProductsByCategory($category_id, $field_name);
       }
       
       if($search_by == "KEYWORDS"){
           $keyword = $postData['searchValue'];
           $this->data['product_list'] = $this->storeapp_model->getProductsByKeyword($keyword);  
       }
       
       $this->__load_view('products_list', $this->data); 
       
    }
        
    public function add_order_items($postData) {
        
        $incartproducts = $postData['incartproducts'];
        $order_id       = $postData['order_id'] ? $postData['order_id'] : null;
        $customer_id    = $postData['customer_id'] ? $postData['customer_id'] : null;
            
        $itemsArr = explode(',', $incartproducts);
        
        if(is_array($itemsArr)){
            foreach ($itemsArr as $key => $item) {
                
                if($item){
                   $prodids = explode('_', $item);
                   $product_id = $prodids[0];
                   $option_id = $prodids[1] ? $prodids[1] : null;
                   
                   $productData = $this->storeapp_model->getProducts($product_id, $option_id);
                   
                   $product = $productData[$product_id];
            
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = '';
                    $unit_discount = 0;
                    $invoice_net_unit_price = 0;
                    $item_tax_rate  = $product['tax_rate'];
                    $tax_method     = $product['tax_method'];            
                    $option_price   = $product['option_id'] ? $product['option_price'] : 0;
                    $option_unit_quantity   = $product['option_id'] ? $product['option_unit_quantity'] : 0;
                    
                    $unit_weight = $option_unit_quantity ? $option_unit_quantity : $product['weight'];
                    
                    $now = strtotime(date('Y-m-d H:i:s'));
            
                    if($product['promotion'] == 1 && strtotime($product['start_date']) <= $now && strtotime($product['end_date']) >= $now ){
                       $item_price = (float)$product['promo_price'] + (float)$option_price; 
                    } else {
                       $item_price = (float)$product['price'] + (float)$option_price; 
                    } 
                    
                    $net_unit_price = $item_price;
                    $unit_price     = $item_price;
                    $invoice_unit_price     = $item_price;
                    $invoice_net_unit_price = $item_price + $unit_discount;

                    $item_gst  = 0;
                    $item_cgst = 0;
                    $item_sgst = 0;
                    $item_igst = 0;
                                        
                    if ( $item_tax_rate > 0 ) {
                        
                        $tax = $item_tax_rate . "%";
            
                            if ($product['tax_type'] == 1) {
                                //Exclusive tax method calculation
                                if ($tax_method == 1) {
                                    
                                    $item_tax = $this->sma->formatDecimal(($item_price * $item_tax_rate) / 100, 4);

                                    $net_unit_price = $item_price;
                                    $unit_price = $item_price + $item_tax;

                                    $invoice_unit_price = $item_price;
                                    $invoice_net_unit_price = $item_price + $unit_discount + $item_tax;
                                } else {
                                    //Inclusive tax method calculation.
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / (100 + $item_tax_rate), 4);
                                    
                                    $net_unit_price = $item_price - $item_tax;
                                    $unit_price = $item_price;

                                    $invoice_unit_price = $item_price - $item_tax;
                                    $invoice_net_unit_price = $item_price + $unit_discount;
                                }
                            } elseif ($product['tax_type'] == 2) {

                                if ($tax_method == 1) {
                                    
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / 100, 4);

                                    $net_unit_price = $item_price;
                                    $unit_price = $item_price + $item_tax;

                                    $invoice_unit_price = $item_price;
                                    $invoice_net_unit_price = $item_price + $unit_discount + $item_tax;
                                } else {
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / (100 + $item_tax_rate), 4);

                                    $net_unit_price = $item_price - $item_tax;
                                    $unit_price = $item_price;

                                    $invoice_unit_price = $item_price - $item_tax;
                                    $invoice_net_unit_price = $item_price + $unit_discount;
                                }
                            }//end else.
                            
                            $customer_details = $this->site->getCompanyByID($customer_id);
                            
                            if ((!empty($customer_details->state_code) && !empty($biller_details->state_code)) && $customer_details->state_code != $biller_details->state_code) {
                               // $interStateTax = true;            
                                $item_gst = $item_tax_rate;
                                $item_cgst = 0;
                                $item_sgst = 0;
                                $item_igst = $item_tax;
                            } else {
                                // $interStateTax = false;
                                $item_gst = $this->sma->formatDecimal($item_tax_rate / 2, 4);
                                $item_cgst = $this->sma->formatDecimal($item_tax / 2, 4);
                                $item_sgst = $this->sma->formatDecimal($item_tax / 2, 4);
                                $item_igst = 0;
                            }
                    
                        }
                    $unit_tax           = $item_tax;
                    $item_tax           = $this->sma->formatDecimal(($unit_tax * $item_unit_quantity), 4);
                    $item_unit_quantity = 1;
                    $mrp                = $product['mrp'];
                    $invoice_unit_price             = $this->sma->formatDecimal($invoice_unit_price, 4);
                    $invoice_net_unit_price         = $this->sma->formatDecimal($invoice_net_unit_price, 4);
                    $invoice_total_net_unit_price   = $this->sma->formatDecimal(($invoice_net_unit_price * $item_unit_quantity), 4);
                    $net_unit_price                 = $this->sma->formatDecimal($net_unit_price, 4);
                    $unit_price                     = $this->sma->formatDecimal($unit_price, 4);
                    $net_price                      = $this->sma->formatDecimal(($mrp * $item_quantity), 4);
                    $subtotal                       = $this->sma->formatDecimal(($unit_price * $item_unit_quantity), 4);
                   
                    $warehouse = $store_settings ? $this->store_settings->default_eshop_warehouse : 1;
                    
                    $quantity = $option_unit_quantity ? $option_unit_quantity : $item_unit_quantity;
                    $item_weight = $unit_weight * $item_unit_quantity;
                    
                   $orderItems[] = array(
                       "sale_id"        => $order_id,
                       "product_id"     => $product_id,
                       "product_code"   => $product['code'],
                       "product_name"   => $product['name'],
                       "product_type"   => $product['product_type'],
                       "option_id"      => $option_id,
                       "net_unit_price" => $net_unit_price,
                       "unit_discount"  => 0,
                       "unit_tax"       => $unit_tax,
                       "invoice_unit_price"     => $invoice_unit_price,
                       "invoice_net_unit_price" => $invoice_net_unit_price,
                       "unit_price"             => $unit_price,
                       "quantity"               => $quantity,
                       "net_price"              => $net_price,
                       "invoice_total_net_unit_price" => $invoice_total_net_unit_price,
                       "warehouse_id"       => $warehouse,
                       "item_tax"           => $item_tax,
                       "tax_method"         => $product['tax_method'],
                       "tax_rate_id"        => $product['tax_rate_id'],
                       "tax"                => $tax,
                       "discount"           => 0,
                       "item_discount"      => 0,
                       "subtotal"           => $subtotal,
                       "serial_no"          => '',
                       "real_unit_price"    => $product['price'],
                       "product_unit_id"    => $product['sale_unit'],
                       "product_unit_code"  => $product['unit_name'],
                       "unit_quantity"      => $item_unit_quantity,
                       "cf1"                => $product['cf1'],
                       "cf2"                => $product['cf2'],
                       "mrp"                => $product['mrp'],
                       "hsn_code"           => $product['hsn_code'],
                       "delivery_status"    => 'pending',
                       "pending_quantity"   => 1,
                       "delivered_quantity" => 0,
                       "gst_rate"           => $item_gst,
                       "cgst"               => $item_cgst,
                       "sgst"               => $item_sgst,
                       "igst"               => $item_igst,                       
                       "cf6_name"           => 'pending',                       
                       "item_weight"        => $item_weight,                       
                   );                   
                }
                
            }//end foreach
            
            if($this->storeapp_model->add_order_items($orderItems, $order_id)){
                $data['status'] = "SUCCESS";
            } else {
                $data['status'] = "ERROR";
            }
            
            echo json_encode($data);
            
        }//end if.        
    }
    
    public function add_order($postData) {
               
        $customer_id = $postData['customer'] ? $postData['customer'] : null;
        $customer_details = $this->site->getCompanyByID($customer_id);
        $biller_details   = $this->site->getCompanyByID($this->store_settings->default_biller);
        $total = 0;
            
        if(is_array($postData['item_id'])){
            
            foreach ($postData['item_id'] as $key => $item_id) {
                
                if($item_id){
            
                   $product_id = $item_id;
                   $option_id = $postData['item_option_id'][$key];
                   
                   $productData = $this->storeapp_model->getProducts($product_id, $option_id);
                   
                   $product = $productData[$product_id];
            
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = $unit_tax = 0;
                    $tax = '';
                    $item_discount = $unit_discount = 0;
                    $invoice_net_unit_price = 0;
                    $item_tax_rate  = $postData['item_tax'][$key];
                    $tax_method     = $postData['tax_method'][$key];            
                                
                    $now = strtotime(date('Y-m-d H:i:s'));
            
                    $item_price = $unit_price = $postData['unit_price'][$key];
                    
                    $net_unit_price         = $item_price;            
                    $invoice_unit_price     = $item_price;
                    $invoice_net_unit_price = $item_price + $unit_discount;

                    $item_gst = 0;
                    $item_cgst = 0;
                    $item_sgst = 0;
                    $item_igst = 0;
                                        
                    if ( $item_tax_rate > 0 ) {
                        
                        $tax = $item_tax_rate . "%";
            
                        $unit_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / (100 + $item_tax_rate), 4);

                        $net_unit_price = $item_price - $unit_tax;
                        $invoice_unit_price = $item_price - $unit_tax;
                        $invoice_net_unit_price = $item_price + $unit_discount; 
                                                     
                        if ((!empty($customer_details->state_code) && !empty($biller_details->state_code)) && $customer_details->state_code != $biller_details->state_code) {
                           // $interStateTax = true;            
                            $item_gst = $item_tax_rate;
                            $item_cgst = 0;
                            $item_sgst = 0;
                            $item_igst = $unit_tax;
                        } else {
                            // $interStateTax = false;
                            $item_gst = $this->sma->formatDecimal($item_tax_rate / 2, 4);
                            $item_cgst = $this->sma->formatDecimal($unit_tax / 2, 4);
                            $item_sgst = $this->sma->formatDecimal($unit_tax / 2, 4);
                            $item_igst = 0;
                        }
                    }
                    
                    $item_unit_quantity = $postData['unit_quantity'][$key];
                    $quantity = $postData['quantity'][$key];
                    
                    $item_tax = $this->sma->formatDecimal(($unit_tax * $item_unit_quantity), 4);
                    $mrp = (float)$product['mrp'] ? $product['mrp'] : $product['price'];
                    $invoice_unit_price = $this->sma->formatDecimal($invoice_unit_price, 4);
                    $invoice_net_unit_price = $this->sma->formatDecimal($invoice_net_unit_price, 4);
                    $invoice_total_net_unit_price = $this->sma->formatDecimal(($invoice_net_unit_price * $item_unit_quantity), 4);
                    $net_unit_price = $this->sma->formatDecimal($net_unit_price, 4);
                    $unit_price = $this->sma->formatDecimal($unit_price, 4);
                    $net_price = $this->sma->formatDecimal(($mrp * $quantity), 4);
                    $subtotal = $this->sma->formatDecimal(($unit_price * $item_unit_quantity), 4);
                   
                    $warehouse = $store_settings ? $this->store_settings->default_eshop_warehouse : 1;
                    
                    $option_unit_weight = $option_id && (float)$product['option_unit_quantity'] ? $product['option_unit_quantity'] : 0;
                    
                    $unit_weight = (float)$option_unit_weight ? $option_unit_weight : ((float)$product['weight'] ? $product['weight'] : 1);
                    
                    $item_weight = $this->sma->formatDecimal((float)$unit_weight * (float)$item_unit_quantity, 4);
                    
                   $orderItems[] = array(                       
                       "product_id" => $product_id,
                       "product_code" => $product['code'],
                       "product_name" => $product['name'],
                       "product_type" => $product['product_type'],
                       "option_id" => $option_id,
                       "net_unit_price" => $net_unit_price,
                       "unit_discount" => 0,
                       "unit_tax" => $unit_tax,
                       "invoice_unit_price" => $invoice_unit_price,
                       "invoice_net_unit_price" => $invoice_net_unit_price,
                       "unit_price" => $unit_price,
                       "quantity" => $quantity,
                       "net_price" => $net_price,
                       "invoice_total_net_unit_price" => $invoice_total_net_unit_price,
                       "warehouse_id" => $warehouse,
                       "item_tax" => $item_tax,
                       "tax_method" => $product['tax_method'],
                       "tax_rate_id" => $product['tax_rate_id'],
                       "tax" => $tax,
                       "discount" => 0,
                       "item_discount" => 0,
                       "subtotal" => $subtotal,
                       "serial_no" => '',
                       "real_unit_price" => $product['price'],
                       "product_unit_id" => $product['sale_unit'],
                       "product_unit_code" => $product['unit_name'],
                       "unit_quantity" => $item_unit_quantity,
                       "cf1" => $product['cf1'],
                       "cf2" => $product['cf2'],
                       "mrp" => $product['mrp'],
                       "hsn_code" => $product['hsn_code'],
                       "delivery_status" => 'pending',
                       "pending_quantity" => $item_unit_quantity,
                       "delivered_quantity" => 0,
                       "gst_rate" => $item_gst,
                       "cgst" => $item_cgst,
                       "sgst" => $item_sgst,
                       "igst" => $item_igst,                       
                       "cf6_name" => 'pending',                       
                       "item_weight" => $item_weight,                       
                   ); 
                   
                   $total += $this->sma->formatDecimal(($net_unit_price * $item_unit_quantity), 4);
                    
                   $product_tax += $item_tax;
                   $product_discount += 0;
                   $total_weight += $item_weight;
                   
                   $total_cgst += $item_cgst;
                   $total_sgst += $item_sgst;
                   $total_igst += $item_igst;
                }
                
            }//end foreach
            
            $order_discount_id = null;
            $order_discount = 0;
            
            if ($postData['order_discount']) {
                $order_discount_id = $postData['order_discount'];
                $opos = strpos($order_discount_id, '%');
                if ($opos !== false) {
                    $ods = explode("%", $order_discount_id);
                    $order_discount = $this->sma->formatDecimal(((($total + $product_tax) * (Float) ($ods[0])) / 100), 4);
                } else {
                    $order_discount = $this->sma->formatDecimal($order_discount_id);
                }
            }
            
            $total_discount = $this->sma->formatDecimal($order_discount + $product_discount , 4);
                        
            $order_tax_id = null;
            $order_tax = 0;
            $total_tax = (float)$product_tax + (float)$order_tax;
                    
            $shipping = (float)$postData['shipping'] ? (float)$postData['shipping'] : 0;
            
            $grand_total = $this->sma->formatDecimal(($total + $total_tax + $shipping) - $order_discount , 4);
            $rounding = 0;
            if ($postData['settings_rounding'] > 0) {
                $round_total = $this->sma->roundNumber(((float)$grand_total+(float)$shipping), $postData['settings_rounding']);
                $rounding = ($round_total - $grand_total);
            }
            
           
            $payment_status = 'pending';
            $paid_amount = 0;
            $payment = [];
            if($postData['payment_action']=='paid' && (float)$postData['payment_amount']){
                
                $balance = ($grand_total+$shipping+$rounding) - (float)$postData['payment_amount'];
                
                $payment_status = (float)$balance > 0 ? 'partial' : 'paid';
                $payment_mode = $postData['payment_mode'];
                $paid_amount  = $postData['payment_amount'];
                $transaction_no = $postData['transaction_no'];
                $cheque_no = $postData['payment_mode'] == 'cheque' ? $transaction_no : '';
                $pay_reference_no = $this->site->getReference('pay');
                
                $payment = array(
                    "date" => $now,
                    "reference_no" => $pay_reference_no,
                    "transaction_id" => $transaction_no,
                    "paid_by" => $payment_mode,
                    "cheque_no" => $cheque_no,
                    "amount" => $paid_amount,
                    "type" => 'received',
                    "created_by" => $this->session->userdata('user_id'),
                );
            }            
            
            $order_reference = $this->site->getReference('ordr');
            $now = date('Y-m-d H:i:s');
            
            $orderData = array(
                "date" => $now,
                "reference_no"      => $order_reference,
                "customer_id"       => $customer_id,
                "customer"          => $customer_details->name,
                "biller_id"         => $this->store_settings->default_biller,
                "biller"            => $biller_details->name,
                "warehouse_id"      => $this->store_settings->default_eshop_warehouse,
                "staff_note"        => $postData['staff_note'],
                "total"             => $total,
                "product_discount"  => $product_discount,
                "order_discount_id" => $order_discount_id,
                "total_discount"    => $total_discount,
                "order_discount"    => $order_discount,
                "product_tax"       => $product_tax,
                "order_tax_id"      => $order_tax_id,
                "order_tax"         => $order_tax,
                "total_tax"         => $total_tax,
                "shipping"          => $shipping,
                "grand_total"       => $grand_total,
                "sale_status"       => 'pending',
                "payment_status"    => $payment_status,
                "total_items"       => $postData['total_items'],
                "pos"               => 0,
                "paid"              => $paid_amount,
                "rounding"          => $rounding,
                "cgst"              => $total_cgst,
                "sgst"              => $total_sgst,
                "igst"              => $total_igst,
                "sale_as_chalan"    => 0,
                "eshop_sale"        => 1,
                "total_weight"      => $total_weight,
                "created_by"        => $this->session->userdata('user_id'),
            );           
            
            
            if($this->storeapp_model->addOrder($orderData , $orderItems , $payment )){
                $data['status'] = "SUCCESS";
            } else {
                $data['status'] = "ERROR";
            } 
            
            $this->session->set_flashdata('message', 'New Order Added Successfully');
            redirect('storeapp/orders/'.$order['order_status']);
            
        }//end if.        
    }
    
    public function select_order_items($postData) {
        
        $incartproducts = $postData['incartproducts'];        
            
        //$itemsArr = explode(',', $incartproducts);
        $itemsArr = json_decode($postData['cartItems'], TRUE);
        
         if(is_array($itemsArr)){
         /*
             foreach ($itemsArr as $key => $item) {
                
                if($item){
                   $prodids = explode('_', $item);
                   $product_id = $prodids[0];
                   $option_id = $prodids[1] ? $prodids[1] : null;
                   
                   $productData = $this->storeapp_model->getProducts($product_id, $option_id);
                   
                   $product = $productData[$product_id];
            
                    $pr_tax = 0;
                    $pr_item_tax = 0;
                    $item_tax = 0;
                    $tax = '';
                    $unit_discount = 0;                    
                    $item_tax_rate  =  $product['tax_rate'];
                    $tax_method     = $product['tax_method'];            
                    $option_price   = $product['option_id'] ? $product['option_price'] : 0;
                    
                    if($product['promotion'] == 1 && strtotime($product['start_date']) <= $now && strtotime($product['end_date']) >= $now ){
                       $item_price = (float)$product['promo_price'] + (float)$option_price; 
                    } else {
                       $item_price = (float)$product['price'] + (float)$option_price; 
                    } 
                    $unit_price = $item_price;

                    $item_gst = 0;
                    $item_cgst = 0;
                    $item_sgst = 0;
                    $item_igst = 0;
                                        
                    if ( $item_tax_rate > 0 ) {
                        
                        $tax = $item_tax_rate . "%";
            
                            if ($product['tax_type'] == 1) {
                                //Exclusive tax method calculation
                                if ($tax_method == 1) {
                                    
                                    $item_tax = $this->sma->formatDecimal(($item_price * $item_tax_rate) / 100, 4);
                                    $unit_price = $item_price + $item_tax;

                                } else {
                                    //Inclusive tax method calculation.
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / (100 + $item_tax_rate), 4);
                                    $unit_price = $item_price;
                                }
                            } elseif ($product['tax_type'] == 2) {

                                if ($tax_method == 1) {
                                    
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / 100, 4);

                                    $unit_price = $item_price + $item_tax;
                                } else {
                                    $item_tax = $this->sma->formatDecimal((($item_price) * $item_tax_rate) / (100 + $item_tax_rate), 4);

                                    $unit_price = $item_price;
                                }
                            }//end else.
                            
                            $customer_details = $this->site->getCompanyByID($customer_id);
                            
                            if ((!empty($customer_details->state_code) && !empty($biller_details->state_code)) && $customer_details->state_code != $biller_details->state_code) {
                               // $interStateTax = true;            
                                $item_gst = $item_tax_rate;
                                $item_cgst = 0;
                                $item_sgst = 0;
                                $item_igst = $item_tax;
                            } else {
                                // $interStateTax = false;
                                $item_gst = $this->sma->formatDecimal($item_tax_rate / 2, 4);
                                $item_cgst = $this->sma->formatDecimal($item_tax / 2, 4);
                                $item_sgst = $this->sma->formatDecimal($item_tax / 2, 4);
                                $item_igst = 0;
                            }
                        }
                        
                    $unit_tax = $item_tax;
                    $item_tax = $this->sma->formatDecimal(($unit_tax * $item_unit_quantity), 4);
                    $item_unit_quantity = 1;
                    $mrp = $product['mrp'];
                    
                    $unit_price = $this->sma->formatDecimal($unit_price, 4);                    
                    $subtotal = $this->sma->formatDecimal(($unit_price * $item_unit_quantity), 4);
                   
                    $warehouse = $store_settings ? $this->store_settings->default_eshop_warehouse : 1;
                    
                   $this->data['order_items'][] = array(
                        "product_id" => $product_id,
                        "product_code" => $product['code'],
                        "product_name" => $product['name'],
                        "product_type" => $product['product_type'],
                        "option_name" => $product['option_name'],
                        "option_id" => $option_id,
                        "net_unit_price" => $net_unit_price,
                        "unit_discount"  => 0,
                        "unit_tax"   => $unit_tax,                       
                        "unit_price" => $unit_price,
                        "quantity"   => 1,                       
                        "warehouse_id" => $warehouse,
                        "item_tax"    => $item_tax,
                        "tax_method"  => $product['tax_method'],
                        "tax_rate_id" => $product['tax_rate_id'],
                        "tax" => $tax,
                        "discount" => 0,
                        "item_discount" => 0,
                        "subtotal" => $subtotal,                       
                        "real_unit_price" => $product['price'],
                        "product_unit_id" => $product['sale_unit'],
                        "product_unit_code" => $product['unit_name'],
                        "unit_quantity" => $item_unit_quantity,                       
                        "mrp" => $product['mrp'],                      
                        "gst_rate" => $item_gst,
                        "cgst" => $item_cgst,
                        "sgst" => $item_sgst,
                        "igst" => $item_igst,                       
                        "item_status" => 'pending',                       
                    );                   
                }                
            }//end foreach
              */      
                       
            $this->data['order_items'] = $itemsArr;
             
            $this->data['store_settings_rounding'] = $this->store_settings->rounding;
        
            $this->__load_view( 'order_new_items_list', $this->data);
            
        }//end if.          
    }
        
    public function order_new() {
        
        $this->data['error'] = $this->session->flashdata('error');
        
        $this->data['categories'] = $this->storeapp_model->get_categories(); 
        
        $this->data['customers'] = $this->storeapp_model->getCustomer();
         
        $this->data['pagename'] = "ORDER Add";
        
        $this->__load_view( 'order_new', $this->data);
    }
    
    public function payments() {
        
        $this->data['customers'] = $this->storeapp_model->getCustomer();
        
        $this->data['pagename'] = "PAYMENTS";
        
        $this->__load_view( 'payments', $this->data);
    }
    
    public function payments_report($postData) {
        
        $payment_status = $postData['payment_status'];
        $listType       = $postData['list_type'];
        $customer       = $postData['customer'];
        
        $this->data['postData'] = $postData;
        
        if($payment_status == 'pending') {
            $this->data['payments'] = $this->storeapp_model->get_pending_payments($customer, $listType);
        }
        if($payment_status == 'paid') {
            $this->data['payments'] = $this->storeapp_model->get_paid_payments($customer, $listType);
        }
        
        $this->__load_view( 'payments_report', $this->data);
    }
    
    public function customers() {
        
         $this->data['customers'] = $this->storeapp_model->getCustomer();
         
         $this->data['pagename'] = "CUSTOMERS";
         
         $this->__load_view( 'customers' , $this->data);
    }
    
    public function sales() {
        
        $this->data['customers'] = $this->storeapp_model->getCustomer();
        
        $this->data['pagename'] = "SALES";
        
        $this->__load_view( 'sales', $this->data);
    }
    
    public function get_sales($postData=null) { 
            
        $date_range = explode( ' - ', str_replace('/', '-', $postData['sale_date']));
       
        $params['date_start']    = $date_range[0];
        $params['date_end']      = $date_range[1];
        $params['payment_status']= $postData['payment_status'];
        $params['customer_id']   = $postData['customer'];
        $params['limit']         = $postData['limit'];
        $params['offset']        = $postData['offset'];
            
        $this->data['sales'] = $this->storeapp_model->get_sales(null, $params);
            
        $this->__load_view( 'sales_list', $this->data);
    }
    
//    public function sale_invoice($sale_id) {
//        
//        $this->data['sale']     = $this->storeapp_model->get_sales($sale_id); 
//        $this->data['items']    = $this->storeapp_model->get_sale_items($sale_id);        
//        $this->data['payments'] = $this->storeapp_model->get_payments($sale_id);
//        
//        $this->__load_view( 'sale_invoice', $this->data);
//    }
    
    
    public function ajaxActions() {
        
        $this->ajaxPost = $this->input->post();
        
        switch ($this->ajaxPost['action']) {
            
            case 'get_orders':
                $this->get_orders($this->ajaxPost);
                break;
            
            case 'get_order_items':
                $this->get_order_items($this->ajaxPost);
                break;
            
            case 'update_order_item_status':
                $this->update_order_item_status($this->ajaxPost);
                break;
            
            case 'update_order':
                $this->update_order($this->ajaxPost);
                break;
            
            case 'model_edit_order_item':
                $this->model_edit_order_item($this->ajaxPost);
                break;
            
            case 'model_edit_order_new_item':
                $this->model_edit_order_new_item($this->ajaxPost);
                break;
            
            case 'edit_order_items':
                $this->edit_order_items($this->ajaxPost);
                break;
            
            case 'search_products':
                $this->search_products($this->ajaxPost);
                break;
            
            case 'products_list':
                $this->products_list($this->ajaxPost);
                break;
                        
            case 'add_order':
                $this->add_order($this->ajaxPost);
                break;
            
            case 'add_order_items':
                $this->add_order_items($this->ajaxPost);
                break;
            
            case 'select_order_items':
                $this->select_order_items($this->ajaxPost);
                break;
            
            case 'payments_report':
                $this->payments_report($this->ajaxPost);
                break;
            
            case 'get_sales':
                $this->get_sales($this->ajaxPost);
                break;
            

            default:
                break;
            
        }
        
    }

    
}
