<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getProductNames($term, $limit = 20) {
        $this->db->select('id, code, name')
                ->like('name', $term, 'both')->or_like('code', $term, 'both');
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffById($user_id) {
        /* if ($this->Admin) {
          $this->db->where('group_id !=', 1);
          } */
        $this->db->where('id', $user_id);
        //$this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaff() {
        if ($this->Admin) {
            $this->db->where('group_id !=', 1);
        }
        $this->db->where('group_id !=', 3)->where('group_id !=', 4);
        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSalesTotals($customer_id) {

        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
                ->where('customer_id', $customer_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerSales($customer_id) {
        $this->db->from('sales')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getCustomerQuotes($customer_id) {
        $this->db->from('quotes')->where('customer_id', $customer_id);
        return $this->db->count_all_results();
    }

    public function getCustomerReturns($customer_id) {
        $this->db->from('sales')->where('customer_id', $customer_id)->where('sale_status', 'returned');
        return $this->db->count_all_results();
    }

    public function getStockValue() {
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*price as by_price, COALESCE(sum(" . $this->db->dbprefix('warehouses_products') . ".quantity), 0)*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id GROUP BY " . $this->db->dbprefix('products') . ".id )a");
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWarehouseStockValue($id) {
        $q = $this->db->query("SELECT SUM(by_price) as stock_by_price, SUM(by_cost) as stock_by_cost FROM ( Select sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*price as by_price, sum(COALESCE(" . $this->db->dbprefix('warehouses_products') . ".quantity, 0))*cost as by_cost FROM " . $this->db->dbprefix('products') . " JOIN " . $this->db->dbprefix('warehouses_products') . " ON " . $this->db->dbprefix('warehouses_products') . ".product_id=" . $this->db->dbprefix('products') . ".id WHERE " . $this->db->dbprefix('warehouses_products') . ".warehouse_id = ? GROUP BY " . $this->db->dbprefix('products') . ".id )a", array($id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    // public function getmonthlyPurchases()
    // {
    //     $myQuery = "SELECT (CASE WHEN date_format( date, '%b' ) Is Null THEN 0 ELSE date_format( date, '%b' ) END) as month, SUM( COALESCE( total, 0 ) ) AS purchases FROM purchases WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH ) GROUP BY date_format( date, '%b' ) ORDER BY date_format( date, '%m' ) ASC";
    //     $q = $this->db->query($myQuery);
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as $row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return FALSE;
    // }

    public function getChartData() {
        $myQuery = "SELECT S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT  date_format(date, '%Y-%m') Month,
                SUM(total) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 12 MONTH )
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT  date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(total) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month
            ORDER BY S.Month";
        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailySales($year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);

        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping, SUM(CASE WHEN up_sales = 1 THEN grand_total ELSE 0 END ) AS urban_piper
			FROM " . $this->db->dbprefix('sales') . " WHERE ";
        /* if ($warehouse_id) {
          $myQuery .= " warehouse_id = {$warehouse_id} AND ";
          } */

        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " created_by = {$user_id} AND  ";
        }

        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
			GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailySalesItems($date, $warehouse_id = 0) {
        $query = "SELECT  si.product_id ,si.product_code ,  si.product_name ,  si.net_unit_price, si.product_unit_code as unit,
                    SUM(  si.quantity ) as qty, SUM(  si.item_tax ) as tax, si.tax as tax_rate, SUM(  si.item_discount ) as discount, SUM(  si.subtotal ) as total, c.id as category_id, c.name as category_name
                FROM  " . $this->db->dbprefix('sale_items') . " si  left join " . $this->db->dbprefix('products') . " p on p.id=si.product_id left join  " . $this->db->dbprefix('categories') . " c on c.id=p.category_id
                WHERE  si.sale_id IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('sales') . "  WHERE DATE( `date` ) =  '$date' )";
        if ($warehouse_id != 0) {
            $query .= " and si.warehouse_id='$warehouse_id'  ";
        }
        $query .= " GROUP BY  si.product_code 
                ORDER BY  si.product_name ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailySalesItemsTaxes($date, $warehouse_id = 0) {
        $select_warehouse = '';
        if ($warehouse_id != 0) {
            $select_warehouse = " and warehouse_id='$warehouse_id'  ";
        }
        $query = "SELECT sum(`tax_amount`) amount, ( `attr_per` * 2) as rate,item_id
            FROM  " . $this->db->dbprefix('sales_items_tax') . " 
                WHERE `sale_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('sales') . "  WHERE DATE( `date` ) =  '$date' " . $select_warehouse . " ) 
                    AND `attr_per` > 0 GROUP BY `attr_per` ORDER BY `attr_per` ASC ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthSalesItemsTaxes($month, $year) {
        $query = "SELECT sum(`tax_amount`) amount, ( `attr_per` * 2) as rate,item_id
            FROM  " . $this->db->dbprefix('sales_items_tax') . " 
                WHERE `sale_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('sales') . "  WHERE  DATE_FORMAT( date,  '%c' ) =  '{$month}' AND  DATE_FORMAT( date,  '%Y' ) =  '{$year}' ) 
                    AND `attr_per` > 0 GROUP BY `attr_per` ORDER BY `attr_per` ASC ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthlySales($year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT  DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
			FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ({$getwarehouse}) AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
			GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffDailySales($user_id, $year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM(IF(sale_status='returned',abs(grand_total) + abs(rounding)  + abs(total_discount) ,0)) as return_amt,SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping, SUM(CASE WHEN up_sales = 1 THEN grand_total ELSE 0 END ) AS urban_piper
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlySales($user_id, $year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ',', $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM(IF(sale_status='returned',abs(grand_total) + abs(rounding) + abs(total_discount) ,0)) as return_amt ,SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ({$getwarehouse}) AND ";
        }

        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        }

        $myQuery .= "  DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getPurchasesTotals($supplier_id) {
        $this->db->select('SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
                ->where('supplier_id', $supplier_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getSupplierPurchases($supplier_id) {
        $this->db->from('purchases')->where('supplier_id', $supplier_id);
        return $this->db->count_all_results();
    }

    public function getStaffPurchases($user_id) {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
                ->where('created_by', $user_id);
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getStaffSales($user_id) {
        $this->db->select('count(id) as total, SUM(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid', FALSE)
                ->where('created_by', $user_id);
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalSales($start, $end, $warehouse_id = NULL) {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
                ->where('sale_status !=', 'pending')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalPurchases($start, $end, $warehouse_id = NULL) {
        $this->db->select('count(id) as total, sum(COALESCE(grand_total, 0)) as total_amount, SUM(COALESCE(paid, 0)) as paid, SUM(COALESCE(total_tax, 0)) as tax', FALSE)
                ->where('status !=', 'pending')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('purchases');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalExpenses($start, $end, $warehouse_id = NULL) {
        $this->db->select('count(id) as total, sum(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalPaidAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'sent')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCashAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')->where('paid_by', 'cash')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedCCAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')->where('paid_by', 'CC')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedChequeAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')->where('paid_by', 'Cheque')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedPPPAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')->where('paid_by', 'ppp')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReceivedStripeAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'received')->where('paid_by', 'stripe')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getTotalReturnedAmount($start, $end) {
        $this->db->select('count(id) as total, SUM(COALESCE(amount, 0)) as total_amount', FALSE)
                ->where('type', 'returned')
                ->where('date BETWEEN ' . $start . ' and ' . $end);
        $q = $this->db->get('payments');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getWarehouseTotals($warehouse_id = NULL) {
        $this->db->select('sum(quantity) as total_quantity, count(id) as total_items', FALSE);
        $this->db->where('quantity !=', 0);
        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('warehouses_products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCosting($date, $warehouse_id = NULL, $year = NULL, $month = NULL) {
        $this->db->select('SUM( COALESCE( purchase_unit_cost, 0 ) * quantity ) AS cost, SUM( COALESCE( sale_unit_price, 0 ) * quantity ) AS sales, SUM( COALESCE( purchase_net_unit_cost, 0 ) * quantity ) AS net_cost, SUM( COALESCE( sale_net_unit_price, 0 ) * quantity ) AS net_sales', FALSE);
        if ($date) {
            $this->db->where('costing.date', $date);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('costing.date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('costing.date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->join('sales', 'sales.id=costing.sale_id')
                    ->where('sales.warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('costing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenses($date, $warehouse_id = NULL, $year = NULL, $month = NULL) {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( amount, 0 ) ) AS total', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
        }


        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('expenses');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getReturns($date, $warehouse_id = NULL, $year = NULL, $month = NULL) {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_tax, 0 ) ) AS total_tax', FALSE)
                ->where('sale_status', 'returned');
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getOrderDiscount($date, $warehouse_id = NULL, $year = NULL, $month = NULL) {
        $sdate = $date . ' 00:00:00';
        $edate = $date . ' 23:59:59';
        $this->db->select('SUM( COALESCE( order_discount, 0 ) ) AS order_discount', FALSE);
        if ($date) {
            $this->db->where('date >=', $sdate)->where('date <=', $edate);
        } elseif ($month) {
            $this->load->helper('date');
            $last_day = days_in_month($month, $year);
            $this->db->where('date >=', $year . '-' . $month . '-01 00:00:00');
            $this->db->where('date <=', $year . '-' . $month . '-' . $last_day . ' 23:59:59');
        }

        if ($warehouse_id) {
            $this->db->where('warehouse_id', $warehouse_id);
        }

        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getExpenseCategories() {
        $q = $this->db->get('expense_categories');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailyPurchases($year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ({$getwarehouse}) AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthlyPurchases($year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ({$getwarehouse}) AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffDailyPurchases($user_id, $year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ( {$getwarehouse} ) AND ";
        }

        // 03/04/19
        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " created_by = {$user_id} AND ";
        }
        // End  03/04/19


        $myQuery .= "  DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlyPurchases($user_id, $year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%c' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('purchases') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN ( {$getwarehouse}) AND ";
        }

        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " created_by = {$user_id} AND ";
        }

        $myQuery .= " created_by = {$user_id} AND DATE_FORMAT( date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( date, '%c' ) ORDER BY date_format( date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBestSeller($start_date, $end_date, $warehouse_id = NULL) {
        $this->db
                ->select("product_name, product_code")->select_sum('quantity')
                ->join('sales', 'sales.id = sale_items.sale_id', 'left')
                ->where('date >=', $start_date)->where('date <=', $end_date)
                ->group_by('product_name, product_code')->order_by('sum(quantity)', 'desc')->limit(10);
        if ($warehouse_id) {
            $this->db->where('sale_items.warehouse_id', $warehouse_id);
        }
        $q = $this->db->get('sale_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->quantity = number_format($row->quantity, 2, '.', '');
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function salesTaxReport($param = NULL) {
        $user = isset($param['user']) ? $param['user'] : NULL;
        $biller = isset($param['biller']) ? $param['biller'] : NULL;
        $customer = isset($param['customer']) ? $param['customer'] : NULL;
        $warehouse = isset($param['warehouse']) ? $param['warehouse'] : NULL;
        $reference_no = isset($param['reference_no']) ? $param['reference_no'] : NULL;
        $start_date = isset($param['start_date']) ? $param['start_date'] : NULL;
        $end_date = isset($param['end_date']) ? $param['end_date'] : NULL;
        $gstn_opt = isset($param['gstn_opt']) ? $param['gstn_opt'] : NULL;
        $gstn_no = isset($param['gstn_no']) ? $param['gstn_no'] : NULL;
        $hsn_code = isset($param['hsn_code']) ? $param['hsn_code'] : NULL;
        if (!empty($hsn_code)) {
            $SalesIds = $this->getSaleIdByHsn($hsn_code);
        }
        $this->db
                ->select_sum('order_tax')
                ->select_sum('product_tax')
                ->join('companies comp', 'sales.customer_id=comp.id', 'left')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left');


        if ($user) {
            $this->db->where('sales.created_by', $user);
        }

        if ($biller) {
            $this->db->where('sales.biller_id', $biller);
        }
        if ($customer) {
            $this->db->where('sales.customer_id', $customer);
        }
        if ($warehouse) {
            $this->db->where('sales.warehouse_id', $warehouse);
        }
        if ($reference_no) {
            $this->db->like('sales.reference_no', $reference_no, 'both');
        }
        if ($start_date) {
            $this->db->where($this->db->dbprefix('sales') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($gstn_opt) {
            switch ($gstn_opt) {
                case '-1':
                    $this->db->where("comp.gstn_no IS NULL OR comp.gstn_no = '' ");
                    break;

                case '1':
                    $this->db->where("comp.gstn_no IS NOT NULL and comp.gstn_no != '' ");
                    break;

                default:

                    break;
            }
        }
        if ($gstn_no) {
            $this->db->where("comp.gstn_no = '" . $gstn_no . "' ");
        }
        if (!empty($hsn_code)) {
            $this->db->where('sales.id in (' . $SalesIds . ')');
        }
        $q = $this->db->get('sales');

        if ($q->num_rows() > 0) {
            $res = $q->row();
            if ($res) {

                $res->CGST = $this->getSumOfSalesTaxAttr('CGST', $param);
                $res->SGST = $this->getSumOfSalesTaxAttr('SGST', $param);
                $res->IGST = $this->getSumOfSalesTaxAttr('IGST', $param);
            }
            return $res;
        }
        return FALSE;
    }

    public function purchaseTaxReport($param = NULL) {
        $user = isset($param['user']) ? $param['user'] : NULL;
        $supplier = isset($param['supplier']) ? $param['supplier'] : NULL;
        $warehouse = isset($param['warehouse']) ? $param['warehouse'] : NULL;
        $reference_no = isset($param['reference_no']) ? $param['reference_no'] : NULL;
        $start_date = isset($param['start_date']) ? $param['start_date'] : NULL;
        $end_date = isset($param['end_date']) ? $param['end_date'] : NULL;
        $gstn_opt = isset($param['gstn_opt']) ? $param['gstn_opt'] : NULL;
        $gstn_no = isset($param['gstn_no']) ? $param['gstn_no'] : NULL;
        $hsn_code = isset($param['hsn_code']) ? $param['hsn_code'] : NULL;
        if (!empty($hsn_code)) {
            $PurchaseIds = $this->getPurchaseIdByHsn($hsn_code);
        }

        $this->db
                ->select_sum('order_tax')
                ->select_sum('product_tax')
                ->join('companies comp', 'purchases.supplier_id=comp.id', 'left')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left');

        if ($user) {
            $this->db->where('purchases.created_by', $user);
        }

        if ($supplier) {
            $this->db->where('purchases.supplier_id', $supplier);
        }
        if ($warehouse) {
            $this->db->where('purchases.warehouse_id', $warehouse);
        }
        if ($reference_no) {
            $this->db->like('purchases.reference_no', $reference_no, 'both');
        }
        if ($start_date) {
            $this->db->where($this->db->dbprefix('purchases') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($gstn_opt) {
            switch ($gstn_opt) {
                case '-1':
                    $this->db->where("comp.gstn_no IS NULL OR comp.gstn_no = '' ");
                    break;

                case '1':
                    $this->db->where("comp.gstn_no IS NOT NULL and comp.gstn_no != '' ");
                    break;

                default:

                    break;
            }
        }

        if ($gstn_no) {
            $this->db->where("comp.gstn_no = '" . $gstn_no . "' ");
        }

        if ($PurchaseIds) {
            $this->db->where('purchases.id in (' . $PurchaseIds . ')');
        }

        $q = $this->db->get('purchases');

        if ($q->num_rows() > 0) {
            $res = $q->row();
            if ($res) {

                $res->CGST = $this->getSumOfPurchaseTaxAttr('CGST', $param);
                $res->SGST = $this->getSumOfPurchaseTaxAttr('SGST', $param);
                $res->IGST = $this->getSumOfPurchaseTaxAttr('IGST', $param);
            }
            return $res;
        }
        return FALSE;
    }

    public function getSaleIdByHsn($hsn) {
        if (empty($hsn)):
            return -1;
        endif;

        $this->db
                ->select('sale_id')
                ->where('hsn_code', $hsn);
        $q = $this->db->get('sale_items');

        if ($q->num_rows() > 0) {
            $resultArr = array();
            foreach (($q->result()) as $row) {
                $resultArr[] = $row->sale_id;
            }
            return implode(',', $resultArr);
        }
        return -1;
    }

    public function getPurchaseIdByHsn($hsn) {
        if (empty($hsn)):
            return -1;
        endif;
        $this->db
                ->select('purchase_items.purchase_id')
                ->group_by('purchase_items.purchase_id')
                ->where('purchase_items.hsn_code', $hsn);
        $q = $this->db->get('purchase_items');
        if ($q->num_rows() > 0) {
            $resultArr = array();
            foreach (($q->result()) as $row) {
                $resultArr[] = $row->purchase_id;
            }
            return implode(',', $resultArr);
        }
        return -1;
    }

    public function getSumOfSalesTaxAttr($code, $param) {

        $user = isset($param['user']) ? $param['user'] : NULL;
        $biller = isset($param['biller']) ? $param['biller'] : NULL;
        $customer = isset($param['customer']) ? $param['customer'] : NULL;
        $warehouse = isset($param['warehouse']) ? $param['warehouse'] : NULL;
        $reference_no = isset($param['reference_no']) ? $param['reference_no'] : NULL;
        $start_date = isset($param['start_date']) ? $param['start_date'] : NULL;
        $end_date = isset($param['end_date']) ? $param['end_date'] : NULL;
        $gstn_opt = isset($param['gstn_opt']) ? $param['gstn_opt'] : NULL;
        $gstn_no = isset($param['gstn_no']) ? $param['gstn_no'] : NULL;
        $hsn_code = isset($param['hsn_code']) ? $param['hsn_code'] : NULL;
        if (!empty($hsn_code)) {
            $SalesIds = $this->getSaleIdByHsn($hsn_code);
        }
        $whereCnd = "1=1";

        if ($user) {
            $whereCnd .= " and sma_sales.created_by = $user";
        }

        if ($biller) {
            $whereCnd .= " and sma_sales.biller_id = $biller";
        }
        if ($customer) {
            $whereCnd .= " and sma_sales.customer_id = $customer";
        }
        if ($warehouse) {
            $whereCnd .= " and sma_sales.warehouse_id = $warehouse";
        }
        if ($reference_no) {
            $whereCnd .= " and sma_sales.reference_no like '%$reference_no%' ";
        }
        if ($start_date) {
            $whereCnd .= " and sma_sales.date BETWEEN '$start_date' and   '$end_date' ";
        }

        if ($gstn_opt) {
            switch ($gstn_opt) {
                case '-1':
                    $whereCnd .= " and (comp.gstn_no IS NULL OR comp.gstn_no = '' ) ";
                    break;

                case '1':
                    $whereCnd .= " and (comp.gstn_no IS NOT NULL and comp.gstn_no != '' ) ";
                    break;

                default:

                    break;
            }
        }
        if ($gstn_no) {
            $whereCnd .= " and (comp.gstn_no ='$gstn_no' ) ";
        }
        if (!empty($hsn_code)) {

            $whereCnd .= " and (sales.id in  != '$SalesIds' ) ";
        }
        $cnd = '';
        if ($whereCnd != '1=1') {

            $subsql = "SELECT sma_sales.id FROM `sma_sales` LEFT JOIN `sma_companies` `comp` ON `sma_sales`.`customer_id`=`comp`.`id` LEFT JOIN `sma_warehouses` ON `sma_sales`.`warehouse_id`= `sma_warehouses`.`id` where " . $whereCnd;

            $cnd = ' and sale_id IN (' . $subsql . ') ';
        }
        $q = $this->db->query("SELECT SUM(`tax_amount`) as amt FROM  `sma_sales_items_tax` WHERE   `attr_code` =  '$code' " . $cnd);

        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->amt;
        }
        return FALSE;
    }

    public function getSalesTaxAttrBySalesIds(array $saleIds) {

        $salesIn = join(',', $saleIds);

        $q = $this->db->query("SELECT * FROM  `sma_sales_items_tax` WHERE sale_id IN ($salesIn)");

        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }

    public function getSalesItemsBySaleIds(array $saleIds, $products) {
        $salesIn = join(',', $saleIds);

        /* $query = "SELECT id as items_id, sale_id, item_tax, subtotal, tax as gst, hsn_code as hsn_code, quantity as quantity, 
          product_unit_code as unit , product_code, product_name, product_id
          FROM  " . $this->db->dbprefix('sale_items') . "
          WHERE `sale_id` IN ($salesIn) "; */

        $query = "SELECT {$this->db->dbprefix('sale_items')}.id as items_id, {$this->db->dbprefix('sale_items')}.sale_id, {$this->db->dbprefix('sale_items')}.item_tax, {$this->db->dbprefix('sale_items')}.subtotal, {$this->db->dbprefix('sale_items')}.tax as gst, {$this->db->dbprefix('sale_items')}.hsn_code as hsn_code, {$this->db->dbprefix('sale_items')}.quantity as quantity, 
                    {$this->db->dbprefix('sale_items')}.product_unit_code as unit , {$this->db->dbprefix('sale_items')}.product_code, {$this->db->dbprefix('sale_items')}.product_name, {$this->db->dbprefix('sale_items')}.product_id ,{$this->db->dbprefix('product_variants')}.name as variant_name
                FROM  {$this->db->dbprefix('sale_items')}  lEFT JOIN {$this->db->dbprefix('product_variants')} ON {$this->db->dbprefix('product_variants')}.id = {$this->db->dbprefix('sale_items')}.option_id
                ";  // WHERE `sale_id` IN ($salesIn)
        if ($products) {
            $query .= " WHERE {$this->db->dbprefix('sale_items')}.product_id= $products";
        } else {
            $query .= " WHERE `sale_id` IN ($salesIn)";
        }

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getSumOfPurchaseTaxAttr($code, $param) {
        $user = isset($param['user']) ? $param['user'] : NULL;
        $supplier = isset($param['supplier']) ? $param['supplier'] : NULL;
        $warehouse = isset($param['warehouse']) ? $param['warehouse'] : NULL;
        $reference_no = isset($param['reference_no']) ? $param['reference_no'] : NULL;
        $start_date = isset($param['start_date']) ? $param['start_date'] : NULL;
        $end_date = isset($param['end_date']) ? $param['end_date'] : NULL;
        $gstn_opt = isset($param['gstn_opt']) ? $param['gstn_opt'] : NULL;
        $gstn_no = isset($param['gstn_no']) ? $param['gstn_no'] : NULL;
        $hsn_code = isset($param['hsn_code']) ? $param['hsn_code'] : NULL;
        if (!empty($hsn_code)) {
            $PurchaseIds = $this->getPurchaseIdByHsn($hsn_code);
        }
        $whereCnd = "1=1";
        if ($user) {
            $whereCnd .= " and sma_purchases.created_by = $user";
        }
        if ($supplier) {
            $whereCnd .= " and sma_purchases.supplier_id = $supplier";
        }
        if ($warehouse) {
            $whereCnd .= " and sma_purchases.warehouse_id = $warehouse";
        }

        if ($reference_no) {
            $whereCnd .= " and sma_purchases.reference_no like '%$reference_no%' ";
        }
        if ($start_date) {
            $whereCnd .= " and sma_purchases.date BETWEEN '$start_date' and   '$end_date' ";
        }

        if ($gstn_opt) {
            switch ($gstn_opt) {
                case '-1':
                    $whereCnd .= " and (comp.gstn_no IS NULL OR comp.gstn_no = '' ) ";
                    break;

                case '1':
                    $this->db->where(" ");
                    $whereCnd .= " and (comp.gstn_no IS NOT NULL and comp.gstn_no != '' ) ";
                    break;

                default:

                    break;
            }
        }

        if ($gstn_no) {
            $whereCnd .= " and (comp.gstn_no ='$gstn_no' ) ";
        }

        if ($PurchaseIds) {
            $whereCnd .= " and (sma_purchases.id in  != '$PurchaseIds' ) ";
        }

        $cnd = '';
        if ($whereCnd != '1=1') {
            $subsql = "  SELECT `sma_purchases`.id FROM `sma_purchases` LEFT JOIN `sma_companies` `comp` ON `sma_purchases`.`supplier_id`=`comp`.`id`LEFT JOIN `sma_warehouses` ON `sma_warehouses`.`id`=`sma_purchases`.`warehouse_id` where " . $whereCnd;
            $cnd = ' and purchase_id IN (' . $subsql . ') ';
        }
        $q = $this->db->query("SELECT SUM(`tax_amount`) as amt FROM  `sma_purchase_items_tax` WHERE   `attr_code` =  '$code' " . $cnd);

        if ($q->num_rows() > 0) {
            $res = $q->row();
            return $res->amt;
        }
        return FALSE;
    }

    public function warehouseSalesItems($start_date = NULL, $end_date = NULL, $warehouse = NULL) {


        if ($start_date != NULL) {

            $where = " WHERE s.`date` BETWEEN '$start_date' AND '$end_date' ";
        }

        if (!$warehouse == '') {
            $getwarehouse = str_replace("_", ",", $warehouse);
            $where .= 'AND si.`warehouse_id` IN(' . $getwarehouse . ')';
        }

        $sql = "SELECT si.`product_id`, si.`product_code`, si.`product_name`, si.`warehouse_id`, sum(si.`quantity`) quantity "
                . "FROM `sma_sale_items` si right JOIN `sma_sales` s ON si.`sale_id` = s.`id` "
                . $where
                . "GROUP BY si.`warehouse_id`, si.`product_id` "
                . "ORDER BY si.`warehouse_id`, si.`product_id` ";

        $q = $this->db->query($sql);

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {

                $data[$row->product_id]['code'] = $row->product_code;
                $data[$row->product_id]['name'] = $row->product_name;
                $data[$row->product_id]['wh'][$row->warehouse_id] = $row->quantity;
            }//end foreach.
            return $data;
        }//end if.

        return false;
    }

    public function getSalesItems($start_date = NULL, $end_date = NULL, $warehouse_id) {
        $query = "SELECT  `product_id` ,`product_code` , `product_name` ,  `net_unit_price` , `product_unit_code` unit,
                    SUM(  `quantity` ) qty, SUM(  `item_tax` ) tax, tax as tax_rate, SUM(  `item_discount` ) discount, SUM(  `subtotal` ) total
                FROM  " . $this->db->dbprefix('sale_items') . "  
                WHERE  `sale_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('sales') . "  WHERE DATE(`date`) BETWEEN '$start_date' AND '$end_date'  ) AND 
                    `warehouse_id` = '$warehouse_id' 
                GROUP BY `product_code` 
                ORDER BY `product_name` ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function warehouseProductsStock($warehouse = NULL) {

        if ($warehouse) {
            $getwarehouse = str_replace("_", ",", $warehouse);
            $where = " WHERE  wp.`warehouse_id` IN ({$getwarehouse})"; //$warehouse' ";
        } else {
            $where = '';
        }

        $sql2 = "SELECT p.`name`, p.`code`, wp.`product_id`, wp.`warehouse_id`, w.`name` as warehouse, wp.`quantity` "
                . "FROM `sma_warehouses_products` wp "
                . "RIGHT JOIN `sma_products` p ON wp.`product_id` = p.`id` "
                . "RIGHT JOIN `sma_warehouses` w ON wp.`warehouse_id` = w.`id` "
                . $where
                . "GROUP BY wp.`warehouse_id`, wp.`product_id` "
                . "ORDER BY p.`name`, wp.`warehouse_id`";

        $qp = $this->db->query($sql2);

        $nump = $qp->num_rows();

        if ($nump > 0) {
            $ws = $wps = [];
            foreach ($qp->result() as $wp) {

                $wps[$wp->product_id]['wpq'][$wp->warehouse_id] = $wp->quantity;
                $wps[$wp->product_id]['name'] = $wp->name;
                $wps[$wp->product_id]['code'] = $wp->code;

                if (!in_array($wp->warehouse, $ws)) {
                    $ws[$wp->warehouse_id] = $wp->warehouse;
                }
            }//end foreach.
            $data['products'] = $wps;
            $data['warehouse'] = $ws;
            return $data;
        }//end num
        return false;
    }

    /* --- 13-03-19  --- */

    public function getreport($start_date, $end_date, $condition, $warehouse) {

        /*      $sql = "SELECT w.`id` as warehouse_id,  w.`name` as warehouse ,sum(s.`grand_total`) as total, sum(s.`total_discount`) as total_discount, sum(s.`rounding`) as rounding 
          FROM `sma_sales` s
          LEFT JOIN `sma_warehouses` w on s.`warehouse_id` = w.`id`
          ";
          //
          //                    LEFT JOIN `sma_sale_items` si ON si.sale_id = s.id
          $where = '';

          if ($start_date) {

          /*
          $gettime = substr($end_date,-5);

          $end_date = str_replace($gettime,"23.59",$end_date);
          $where = "  WHERE date BETWEEN '$start_date' AND '$end_date' ";
         * *
          $where = "  WHERE DATE(date) BETWEEN '$start_date' AND '$end_date' ";
          }
          if ($condition == 'due') {
          $where .= " AND payment_status = 'due'";
          } elseif ($condition == 'return') {
          $where .= " AND sale_status = 'returned' ";
          }

          if ($warehouse) {
          $where .= " AND s.`warehouse_id` = " . $warehouse;
          }
         */
        $sql = "SELECT w.`id` as warehouse_id,  w.`name` as warehouse ,sum(s.`grand_total`) as total, sum(s.`total_discount`) as total_discount,sum(s.`rounding`) as rounding, sum(s.`total`) as net_sale, sum(s.`total_tax`) as tax 
                    FROM `sma_sales` s 
                    LEFT JOIN `sma_warehouses` w on s.`warehouse_id` = w.`id`
                    ";
        $where = '';

        if ($start_date) {
            $where = "  WHERE DATE(date) BETWEEN '$start_date' AND '$end_date' ";
        }
        if ($condition == 'due') {
            $where .= " AND payment_status = 'due'";
        } elseif ($condition == 'return') {
            $where .= " AND sale_status = 'returned' ";
        } elseif ($condition == 'pending') {
            $where .= " AND payment_status = 'pending' ";
        }

        if ($warehouse) {
            $where .= " AND s.`warehouse_id` = " . $warehouse;
        }
        $sql .= $where;

        $q = $this->db->query($sql);
        return $q->row();
    }

    public function getSaleBySalesPerson($Customer) {
        $Sql = "SELECT s.id, DATE_FORMAT(s.date, '%Y-%m-%d %T') as date, s.reference_no, s.biller, c.name as seller, s.customer, s.sale_status, (s.grand_total+s.rounding) as grand_total, s.paid, (s.grand_total+s.rounding-s.paid) as balance, s.payment_status, s.attachment, s.return_id, s.delivery_status, c.email as cemail FROM sma_sales as s inner join sma_companies c on c.id=s.seller_id  WHERE 1 and s.seller_id=$Customer ";
        $Res = $this->db->query($Sql);
        return $Res->result_array();
    }

    public function getSaleItemsBySalesPerson($Customer) {
        /* $Sql = "SELECT c.name as seller, si.product_code, si.product_name, sum(si.quantity) as tot_qty,  sum(si.net_price) as tot_net_price FROM sma_companies c inner join sma_sales s on c.id=s.seller_id inner join `sma_sale_items` si on s.id=si.`sale_id` WHERE s.seller_id=$Customer group by si.product_id"; */

        $Sql = "SELECT c.name as seller, si.product_code, si.product_name, sum(si.quantity) as tot_qty,  sum(si.unit_price) as tot_net_price FROM sma_companies c inner join sma_sales s on c.id=s.seller_id inner join `sma_sale_items` si on s.id=si.`sale_id` WHERE s.seller_id=$Customer group by si.product_id";
        $Res = $this->db->query($Sql);
        return $Res->result_array();
    }

    public function getDailyPurchaseItems($date) {
        $query = "SELECT  `product_id` ,`product_code` ,  `product_name` ,  `net_unit_cost` , `product_unit_code` unit,
                    SUM(  `quantity` ) qty, SUM(  `item_tax` ) tax, tax as tax_rate, SUM(  `item_discount` ) discount, SUM(  `subtotal` ) total
                FROM  " . $this->db->dbprefix('purchase_items') . "  
                WHERE  `purchase_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('purchases') . "  WHERE DATE( `date` ) =  '$date' )
                GROUP BY  `product_code` 
                ORDER BY  `product_name` ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function count_product_varient_data($Data, $search = '') {
        //inner join sma_warehouses_products_variants wpv on p.id=wpv.product_id
        $Sql = "select count(Distinct spv.product_id) AS num from sma_products p inner join sma_product_variants spv on p.id = spv.product_id  ";
        if ($Data['warehouse'])
            $Sql .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";
        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $Sql .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $Sql .= "  and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        if ($Data['warehouse'])
            $Sql .= " and swp.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $Sql .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $Sql .= " and p.brand=" . $Data['brand'];
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $Sql .= " and spv.name in (" . $Variant . ")";
        $Sql .= " order by p.name desc ";
        $Query = $this->db->query($Sql);
        $result = $Query->result_array();
        return $result[0]['num'];
    }

    function load_product_varient_data($Data, $startpoint = '', $per_page = '', $search = '') {
        //select p.id as product_id, p.name, p.code, c.name as cat_name, b.name as brand_name, p.quantity as qty, (p.quantity * p.cost) as product_cost, swp.quantity from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_warehouses_products_variants wpv on p.id=wpv.product_id left join sma_warehouses_products swp on swp.product_id=p.id left join sma_categories c on p.category_id=c.id left join sma_brands b on b.id=p.brand where swp.warehouse_id=1 and p.id='682' group by wpv.product_id order by p.name desc
        // $query = "select p.id as product_id, p.name, p.code, c.name as cat_name, b.name as brand_name, p.quantity as qty, (p.quantity * p.cost) as product_cost from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_warehouses_products_variants wpv on p.id=wpv.product_id left join sma_categories c on p.category_id=c.id left join sma_brands b on b.id=p.brand ";
        $query = "select p.id as product_id, p.name, p.code, c.name as cat_name, b.name as brand_name, p.quantity as qty ";
        if ($Data['warehouse'])
            $query .= " ,swp.quantity as wh_qty, (swp.quantity * p.cost) as product_cost ";
        else
            $query .= " ,(p.quantity * p.cost) as product_cost ";
        $query .= " from sma_products p inner join sma_product_variants spv on p.id = spv.product_id ";
        if ($Data['warehouse'])
            $query .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";

        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $query .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if ($Data['warehouse'])
            $query .= " and swp.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $query .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $query .= " and p.brand=" . $Data['brand'];
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $query .= " and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $query .= " and spv.name in (" . $Variant . ")";
        $query .= " group by spv.product_id order by p.name desc ";
        if ($Data['v'] == 'export') {
            $startpoint = $Data['start'];
            $per_page = $Data['limit'];
            $query .= " LIMIT {$startpoint} , {$per_page}";
        } else {
            if ($startpoint != '') {
                $query .= " LIMIT {$startpoint} , {$per_page}";
            } else {
                $query .= " ";
            }
        }
        //echo $query; exit;
        return $result = $this->db->query($query);
    }

    function max_varient_count($Type = '') {
        $Variant = $this->site->showVariantFilter();
        if ($Type != '')
            $whr = " and name in (" . $Variant . ")";
        $Sql = "SELECT MAX(count_product_id) as max_varient_count FROM (SELECT product_id, COUNT(*) AS count_product_id FROM sma_product_variants where 1 $whr GROUP BY product_id) AS Results";
        $Query = $this->db->query($Sql);
        $result = $Query->result_array();
        return $result[0]['max_varient_count'];
    }

    public function count_product_varient_sale_data($Data, $search = '') {
        //inner join sma_warehouses_products_variants wpv on p.id=wpv.product_id
        $Sql = "select count(Distinct spv.product_id) AS num from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_sale_items ssi on spv.id=ssi.option_id inner join sma_sales s on s.id=ssi.sale_id ";
        //if($Data['warehouse'])
        //$Sql .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";
        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $Sql .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $Sql .= "  and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        if ($Data['warehouse'])
            $Sql .= " and ssi.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $Sql .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $Sql .= " and p.brand=" . $Data['brand'];

        if ($Data['start_date']) {
            $Sql .= " and DATE(s.date) BETWEEN '" . $Data['start_date'] . "' and '" . $Data['end_date'] . "'";
        }
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $Sql .= " and spv.name in (" . $Variant . ")";
        $Sql .= " order by p.name desc ";
        $Query = $this->db->query($Sql);
        $result = $Query->result_array();
        return $result[0]['num'];
    }

    function load_product_varient_sale_data($Data, $startpoint = '', $per_page = '', $search = '') {
        $query = "select p.id as product_id, p.name, p.code, c.name as cat_name, b.name as brand_name, p.quantity as qty, (p.quantity * p.cost) as product_cost ";
        $query .= " from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_sale_items ssi on spv.id=ssi.option_id inner join sma_sales s on s.id=ssi.sale_id ";
        //if($Data['warehouse'])
        //$query .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";
        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $query .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if ($Data['warehouse'])
            $query .= " and ssi.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $query .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $query .= " and p.brand=" . $Data['brand'];
        if ($Data['start_date']) {
            $query .= " and DATE(s.date) BETWEEN '" . $Data['start_date'] . "' and '" . $Data['end_date'] . "'";
        }
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $query .= " and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $query .= " and spv.name in (" . $Variant . ")";
        $query .= " group by spv.product_id order by p.name desc ";
        if ($Data['v'] == 'export') {
            $startpoint = $Data['start'];
            $per_page = $Data['limit'];
            $query .= " LIMIT {$startpoint} , {$per_page}";
        } else {
            if ($startpoint != '') {
                $query .= " LIMIT {$startpoint} , {$per_page}";
            } else {
                $query .= " ";
            }
        }
        //echo $query; exit;
        return $result = $this->db->query($query);
    }

    function getVarientName($Type = '') {
        $this->db->select('id, name');
        $this->db->order_by('ABS(name)', 'asc');
        $this->db->group_by('name');
        if ($Type != '')
            $this->db->where_in('name', ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL']);
        $q = $this->db->get('sma_product_variants');
        return $q->result_array();
        //SELECT * FROM `sma_product_variants` WHERE 1 group by name ORDER BY ABS(name) asc
    }

    /*     * * Report payment Summary  * */

    /**
     * 
     * @param type $start_date
     * @param type $end_date
     * @param type $type
     * @param type $user
     * @return type
     */
    public function payment_summary($start_date, $end_date, $type, $user, $warehouse) {
        $this->db->select(' DATE_FORMAT(sma_payments.date, "%Y-%m-%d") as date, sum(sma_payments.amount) as Total, sma_payments.type');
        if ($start_date && $end_date) {
            $this->db->where('sma_payments.date ' . ' BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if (isset($type)) {
            $this->db->where('sma_payments.type', $type);
        }

        if (isset($user)) {
            $this->db->where('sma_payments.created_by', $user);
        }


        if (isset($warehouse)) {
            $this->db->join('sma_sales', 'sma_sales.id = sma_payments.sale_id');
            $this->db->where('sma_sales.warehouse_id', $warehouse);
        }
        $payment_summary = $this->db->group_by('DATE_FORMAT(sma_payments.date, "%Y-%m-%d"),sma_payments.type')->get('sma_payments')->result();

        return $payment_summary;
    }

    /**
     * 
     * @param type $date
     * @param type $type
     * @return type
     */
    public function payment_type($date, $type) {

        $payment_type = $this->db->select('sum(amount) as ' . $type)
                        ->where('type', $type)
                        ->where('Date(date)', $date)
                        ->group_by('DATE(date)')->get('sma_payments')->row();

        return $payment_type;
    }

    /**
     * 
     * @param type $option
     * @param type $date
     * @param type $type
     * @return type
     */
    public function getoptionpayment($option, $date, $type, $user, $warehouse) {
        $this->db->select('sum(sma_payments.amount) as ' . $option . ' ');
        if (isset($date)) {
            $this->db->where('Date(sma_payments.date)', $date . '%');
        }

        if (isset($option)) {
            $this->db->where('sma_payments.paid_by', $option);
        }

        if (isset($type)) {
            $this->db->where('sma_payments.type', $type);
        }

        if (isset($user)) {
            $this->db->where('sma_payments.created_by', $user);
        }


        if (isset($warehouse)) {
            $this->db->join('sma_sales', 'sma_sales.id = sma_payments.sale_id');
            $this->db->where('sma_sales.warehouse_id', $warehouse);
        }

        $data = $this->db->get('sma_payments')->row();

        return $data;
    }

    /**
     * 
     * @param type $option
     * @param type $type
     * @param type $start_date
     * @param type $end_date
     * @param type $users
     * @param type $warehouse
     * @return type
     */
    public function getTotal($option, $type, $start_date, $end_date, $users, $warehouse) {
        $this->db->select('sum(sma_payments.amount) as ' . $option . ' ');
        if (isset($option)) {
            $this->db->where('sma_payments.paid_by', $option);
        }

        if (isset($type)) {
            $this->db->where('sma_payments.type', $type);
        }

        if ($start_date && $end_date) {
            $this->db->where('sma_payments.date ' . ' BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if (isset($users)) {
            $this->db->where('sma_payments.created_by', $users);
        }

        if (isset($warehouse)) {
            $this->db->join('sma_sales', 'sma_sales.id = sma_payments.sale_id');
            $this->db->where('sma_sales.warehouse_id', $warehouse);
        }


        $data = $this->db->get('sma_payments')->row();
//     
//     print_r($this->db->last_query());
        return $data;
    }

    /**
     * 
     * @return type
     */
    public function payment_option() {

        $getpayment_option = $this->db->select('authorize,instamojo,ccavenue,credit_card as CC,debit_card as DC,gift_card,neft as NEFT,paytm_opt as Paytm,UPI_QRCODE,google_pay as Googlepay,swiggy,zomato,ubereats,magicpin,complimentary as complimentry,paynear as paynear,payumoney,stripe')->get('sma_pos_settings')->row_array();
        $optionvalue = 'cash,Cheque,deposit,other,credit_note,award_point,';
        foreach ($getpayment_option as $key => $option) {

            if ($option) {
                $optionvalue .= $key . ',';
            }
        }

        $payment_option = explode(",", $optionvalue);

        return array_filter($payment_option);
    }

    /*     * * End Report payment Summary  * */

    /**
     * This method using get payment option
     * @param type $sales_id
     * @return type
     */
    public function getpaymentmode($sales_id = null) {
        $getoption = $this->db->select(' GROUP_CONCAT(DISTINCT  paid_by) as paid_by')
                        ->where(['sale_id' => $sales_id])
                        ->get('sma_payments')->row();

        return $getoption->paid_by;
    }

    /** End get payment option * */
    /* Tax CGST SGST IGST */
    public function gettaxitemid($item_id) {

        $qry = "SELECT (SELECT attr_per FROM  " . $this->db->dbprefix('sales_items_tax') . "  WHERE `attr_code` = 'CGST' AND  item_id ='$item_id' ) AS CGST ,(SELECT attr_per FROM  " . $this->db->dbprefix('sales_items_tax') . "  WHERE `attr_code` = 'SGST' AND item_id ='$item_id') AS SGST ,(SELECT attr_per FROM  " . $this->db->dbprefix('sales_items_tax') . "  WHERE `attr_code` = 'IGST' AND  item_id ='$item_id' ) AS IGST FROM  " . $this->db->dbprefix('sales_items_tax') . "  WHERE   item_id ='$item_id' Group By item_id";
        $sqlrs = $this->db->query($qry, false);
        if ($sqlrs->num_rows() > 0) {
            foreach (($sqlrs->result()) as $row_rs) {
                $data[] = $row_rs;
            }
            return $data;
        }
        return FALSE;
    }

    /* 11-23-2019 Purchase Item Teax */

    public function getDailyPurchaseItemsTaxes($date) {

//        $query = "SELECT sum(`tax_amount`) amount, ( `attr_per` * 2) as rate,item_id
//            FROM  " . $this->db->dbprefix('purchase_items_tax') . " 
//            WHERE `purchase_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('purchases') . "  WHERE DATE( `date` ) =  '$date' ) 
//            AND `attr_per` > 0 GROUP BY `attr_per` ORDER BY `attr_per` ASC ";
        
        $query = "SELECT gst_rate, cgst, sgst, igst, id
            FROM  " . $this->db->dbprefix('purchase_items') . " 
            WHERE `purchase_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('purchases') . "  WHERE  DATE( `date` ) =  '$date' ) 
            AND `gst_rate` > 0 ORDER BY `gst_rate` ASC ";  

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
        return FALSE;
    }

    public function getMonthPurchaseItemsTaxes($month, $year) {
//        $query = "SELECT sum(`tax_amount`) amount, ( `attr_per` * 2) as rate,item_id 
//            FROM  " . $this->db->dbprefix('purchase_items_tax') . " 
//            WHERE `purchase_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('purchases') . "  WHERE  DATE_FORMAT( date,  '%c' ) =  '{$month}' AND  DATE_FORMAT( date,  '%Y' ) =  '{$year}' ) 
//            AND `attr_per` > 0 GROUP BY `attr_per` ORDER BY `attr_per` ASC ";

        $query = "SELECT gst_rate, cgst, sgst, igst, id
            FROM  " . $this->db->dbprefix('purchase_items') . " 
            WHERE `purchase_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('purchases') . "  WHERE  DATE_FORMAT( date,  '%c' ) =  '{$month}' AND  DATE_FORMAT( date,  '%Y' ) =  '{$year}' ) 
            AND `gst_rate` > 0 ORDER BY `gst_rate` ASC ";   
            
        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /* Tax Purchase CGST SGST IGST */

    public function getpurchasetaxitemid($item_id) {
        $qry = "SELECT (SELECT attr_per FROM  " . $this->db->dbprefix('purchase_items_tax') . "  WHERE `attr_code` = 'CGST' AND  item_id ='$item_id' ) AS CGST ,(SELECT attr_per FROM  " . $this->db->dbprefix('purchase_items_tax') . "  WHERE `attr_code` = 'SGST' AND item_id ='$item_id') AS SGST ,(SELECT attr_per FROM  " . $this->db->dbprefix('purchase_items_tax') . "  WHERE `attr_code` = 'IGST' AND  item_id ='$item_id' ) AS IGST FROM  " . $this->db->dbprefix('purchase_items_tax') . "  WHERE   item_id ='$item_id' Group By item_id";
        $sqlrs = $this->db->query($qry, false);
        if ($sqlrs->num_rows() > 0) {
            foreach (($sqlrs->result()) as $row_rs) {
                $data[] = $row_rs;
            }
            return $data;
        }
        return FALSE;
    }

    /* 12-28-2019 It show to warehouse */

    public function getStaffDailySales_w($user_id, $year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( s.date,  '%e' ) AS date, SUM( COALESCE( s.product_tax, 0 ) ) AS tax1, SUM( COALESCE( s.order_tax, 0 ) ) AS tax2, SUM( COALESCE( s.grand_total, 0 ) ) AS total, SUM( COALESCE( s.total_discount, 0 ) ) AS discount, SUM( COALESCE( s.shipping, 0 ) ) AS shipping,SUM(CASE WHEN up_sales = 1 THEN grand_total ELSE 0 END ) AS urban_piper, w.name as warehouse  FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        }
        $myQuery .= " DATE_FORMAT( s.date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( s.date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getDailySales_w($year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);

        $myQuery = "SELECT DATE_FORMAT( s.date,  '%e' ) AS date, SUM( COALESCE( s.product_tax, 0 ) ) AS tax1, SUM( COALESCE( s.order_tax, 0 ) ) AS tax2, SUM( COALESCE( s.grand_total, 0 ) ) AS total,  SUM(IF(sale_status='returned',abs(grand_total) + abs(rounding) + abs(total_discount),0)) as return_amt,SUM( COALESCE( s.total_discount, 0 ) ) AS discount, SUM( COALESCE( s.shipping, 0 ) ) AS shipping,SUM(CASE WHEN up_sales = 1 THEN grand_total ELSE 0 END ) AS urban_piper, w.name as warehouse 	FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        /* if ($warehouse_id) {
          $myQuery .= " warehouse_id = {$warehouse_id} AND ";
          } */

        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " s.created_by = {$user_id} AND  ";
        }

        $myQuery .= " DATE_FORMAT( s.date,  '%Y-%m' ) =  '{$year}-{$month}'
			GROUP BY DATE_FORMAT( s.date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getStaffMonthlySales_w($user_id, $year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ',', $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT(  s.date,  '%c' ) AS date, SUM( COALESCE(  s.product_tax, 0 ) ) AS tax1, SUM( COALESCE(  s.order_tax, 0 ) ) AS tax2, SUM( COALESCE(  s.grand_total, 0 ) ) AS total, SUM( COALESCE(  s.total_discount, 0 ) ) AS discount, SUM( COALESCE(  s.shipping, 0 ) ) AS shipping, w.name as warehouse   FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN ({$getwarehouse}) AND ";
        }

        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        }

        $myQuery .= "  DATE_FORMAT( s.date,  '%Y' ) =  '{$year}'
            GROUP BY date_format( s.date, '%c' ) ORDER BY date_format( s.date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getMonthlySales_w($year, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT  DATE_FORMAT(  s.date,  '%c' ) AS date, SUM( COALESCE(  s.product_tax, 0 ) ) AS tax1, SUM( COALESCE(  s.order_tax, 0 ) ) AS tax2, SUM( COALESCE(  s.grand_total, 0 ) ) AS total,SUM(IF(sale_status='returned',abs(grand_total) + abs(rounding) + abs(total_discount) ,0)) as return_amt ,SUM( COALESCE(  s.total_discount, 0 ) ) AS discount, SUM( COALESCE(  s.shipping, 0 ) ) AS shipping, w.name as warehouse  FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN ({$getwarehouse}) AND ";
        }
        $myQuery .= " DATE_FORMAT( date,  '%Y' ) =  '{$year}'
			GROUP BY date_format( s.date, '%c' ) ORDER BY date_format( s.date, '%c' ) ASC";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /*     * ** */

    /** HSN Code Model 1-22-2020* */
    public function salesHsnCodeReports($start_date = NULL, $end_date = NULL) {
        $this->db->select('(sma_sale_items.hsn_code) as hsn_code,'
                . 'ROUND(sma_sale_items.tax,2) as tax_rate,'
                . ' sum(sma_sale_items.invoice_unit_price * sma_sale_items.quantity) as basic_amount ,'
                . ' format(SUM(sma_sale_items.cgst), 2) as cgst,format(sum(sma_sale_items.sgst), 2) as sgst,'
                . ' format(sum(sma_sale_items.igst), 2) as igst, format(sum(sma_sale_items.sgst + sma_sale_items.cgst + sma_sale_items.igst), 2) as total_gst , '
                . ' sum(sma_sale_items.invoice_total_net_unit_price) as total_sales');
        $this->db->where('sma_sale_items.hsn_code != " "');

        if (!empty($start_date) && !empty($end_date)) {
            $this->db->join('sma_sales', 'sma_sales.id = sma_sale_items.sale_id');
            //$this->db->where('sma_sales.date >=', $start_date);
            //$this->db->where('sma_sales.date <=', $end_date);
            $this->db->where('DATE(' . $this->db->dbprefix('sales') . '.date) BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }
        $this->db->group_by(['sma_sale_items.hsn_code', 'sma_sale_items.tax']);
        return $this->db->get('sma_sale_items')->result();
    }

    /*     * * */

    /**
     * 
     * @param type $start_date
     * @param type $end_date
     * @return type
     */
    public function salesGSTRateReports($start_date = NULL, $end_date = NULL) {

        $this->db->select('ROUND(sma_sale_items.tax,2) as tax_rate,'
                . ' sum(sma_sale_items.invoice_unit_price * sma_sale_items.quantity ) as basic_amount ,'
                . ' SUM(sma_sale_items.sgst) as sgst,sum(sma_sale_items.cgst) as cgst,'
                . ' sum(sma_sale_items.igst) as igst, sum(sma_sale_items.sgst + sma_sale_items.cgst + sma_sale_items.igst) as total_gst , '
                . 'sum(sma_sale_items.invoice_total_net_unit_price) as total_sales');

        $this->db->where('sma_sale_items.gst_rate >  0');


        if (isset($start_date) && isset($end_date)) {
            $this->db->join('sma_sales', 'sma_sales.id = sma_sale_items.sale_id');
            //$this->db->where('sma_sales.date >=', $start_date);
            //$this->db->where('sma_sales.date <=', $end_date);
            $this->db->where('DATE(' . $this->db->dbprefix('sales') . '.date) BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        return $this->db->group_by('sma_sale_items.tax')->get('sma_sale_items')->result();
    }

    /*     * *1-21-2020 new Gst Report Model** */

    public function getSalesHsunt($salesid, $type) {
        $array = array('sale_id' => $salesid);
        $get = $this->db->select('(GROUP_CONCAT(DISTINCT ' . $type . ')) as hsunt')
                        ->where($array)
                        ->get('sma_sale_items')->row();
        return $get->hsunt;
    }

    public function getSalesQty($sid) {
        $array = array('sale_id' => $sid);
        $get = $this->db->select('format(sum(quantity), 2) as qty')
                        ->where($array)
                        ->get('sma_sale_items')->row();
        return $get->qty;
    }

    public function getSalesTax($saleid) {
        $array = array('sale_id' => $saleid);
        $get = $this->db->select('(GROUP_CONCAT(CONCAT(" " , format(tax,2),"%"))) as tax')
                        ->where($array)
                        ->get('sma_sale_items')->row();
        return $get->tax;
    }

    public function getSalesAsGst($saleid, $type) {
        $myQry = "SELECT  sum($type) as sum  FROM  sma_sale_items WHERE sale_id = $saleid  and $type > 0 Group By gst_rate";

        $res = $this->db->query($myQry, false)->row();
        // echo $saleid;
        // echo $res->sum;
        if ($res->sum != " " && $res->sum != 0.0000) {
            $myQuery = "SELECT  DISTINCT CONCAT('(',gst_rate, '%)Rs.',sum($type))as sumgst  FROM  sma_sale_items WHERE sale_id = $saleid  Group By gst_rate";
        } else {
            $myQuery = "SELECT  DISTINCT CONCAT('Rs.',sum($type))as sumgst  FROM  sma_sale_items WHERE sale_id = $saleid  Group By gst_rate";
        }
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**/

    /*     * 28-1-2020* */

    public function getSalesInvoice($start_date = NULL, $end_date = NULL, $warehouse_id) {
        $query = "SELECT  DATE_FORMAT(s.date, '%Y-%m-%d') as date , s.id as invoice_no , s.customer ,  s.total_discount AS discount, p.amount AS recieved_amt,
                  s.`total` as netsale, s.total_tax as tax,s.`total` as  net_total,s.`paid` as  paid ,s.`rounding` as  rounding
                  FROM  sma_sales s lEFT JOIN sma_payments p  ON p.sale_id = s.id  WHERE DATE(s.date) >= '$start_date' AND DATE(s.date) <= '$end_date'  AND 
                  s.warehouse_id = $warehouse_id  ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getreturnsales($start_date, $end_date, $sale_id) {

        $sql = "SELECT SUM(amount) as received_total FROM `sma_payments` WHERE type = 'received' AND  sale_id = $sale_id  AND  (DATE(date) >= '$start_date' AND DATE(date) <= '$end_date')";
        //$sql = "SELECT grand_total as return_total, total_discount as total_discount FROM `sma_sales` WHERE sale_status = 'returned' AND  id = $sale_id ";
        $q = $this->db->query($sql);
        return $q->row();
    }

    public function getOrderItemsByOrderIds(array $orderIds, $products) {
        $ordersIn = join(',', $orderIds);

        $query = "SELECT {$this->db->dbprefix('order_items')}.id as items_id, {$this->db->dbprefix('order_items')}.sale_id, {$this->db->dbprefix('order_items')}.item_tax, {$this->db->dbprefix('order_items')}.subtotal, {$this->db->dbprefix('order_items')}.tax as gst, {$this->db->dbprefix('order_items')}.hsn_code as hsn_code, {$this->db->dbprefix('order_items')}.quantity as quantity, 
                    {$this->db->dbprefix('order_items')}.product_unit_code as unit , {$this->db->dbprefix('order_items')}.product_code, {$this->db->dbprefix('order_items')}.product_name, {$this->db->dbprefix('order_items')}.product_id ,{$this->db->dbprefix('product_variants')}.name as variant_name
                FROM  {$this->db->dbprefix('order_items')}  lEFT JOIN {$this->db->dbprefix('product_variants')} ON {$this->db->dbprefix('product_variants')}.id = {$this->db->dbprefix('order_items')}.option_id
                ";
        if ($products) {
            $query .= " WHERE {$this->db->dbprefix('order_items')}.product_id= $products";
        } else {
            $query .= " WHERE `sale_id` IN ($ordersIn)";
        }

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * Urban piper Daily sales Reports
     * 
     */

    /**
     * 
     * @param type $user_id
     * @param type $year
     * @param type $month
     * @param type $warehouse_id
     * @return boolean
     */
    public function getDailySalesUP($year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);

        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
			FROM " . $this->db->dbprefix('sales') . " WHERE ";
        /* if ($warehouse_id) {
          $myQuery .= " warehouse_id = {$warehouse_id} AND ";
          } */

        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " created_by = {$user_id} AND  ";
        }
        $myQuery .= " sma_sales.up_sales = 1 AND ";
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
			GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * 
     * @param type $year
     * @param type $month
     * @param type $warehouse_id
     * @return boolean
     */
    public function getDailySalesUP_w($year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);

        $myQuery = "SELECT DATE_FORMAT( s.date,  '%e' ) AS date, SUM( COALESCE( s.product_tax, 0 ) ) AS tax1, SUM( COALESCE( s.order_tax, 0 ) ) AS tax2, SUM( COALESCE( s.grand_total, 0 ) ) AS total, SUM( COALESCE( s.total_discount, 0 ) ) AS discount, SUM( COALESCE( s.shipping, 0 ) ) AS shipping, w.name as warehouse 	FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        /* if ($warehouse_id) {
          $myQuery .= " warehouse_id = {$warehouse_id} AND ";
          } */

        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->session->userdata('view_right') == '0') {
            $myQuery .= " s.created_by = {$user_id} AND  ";
        }
        $myQuery .= " s.up_sales = 1 AND ";
        $myQuery .= " DATE_FORMAT( s.date,  '%Y-%m' ) =  '{$year}-{$month}'
			GROUP BY DATE_FORMAT( s.date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * 
     * @param type $user_id
     * @param type $year
     * @param type $month
     * @param type $warehouse_id
     * @return boolean
     */
    public function getStaffDailySalesUP($user_id, $year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( date,  '%e' ) AS date, SUM( COALESCE( product_tax, 0 ) ) AS tax1, SUM( COALESCE( order_tax, 0 ) ) AS tax2, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( total_discount, 0 ) ) AS discount, SUM( COALESCE( shipping, 0 ) ) AS shipping
            FROM " . $this->db->dbprefix('sales') . " WHERE ";
        if ($warehouse_id) {
            $myQuery .= " warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " created_by = {$user_id} AND ";
            }
        }
        $myQuery .= " sma_sales.up_sales = 1 AND ";
        $myQuery .= " DATE_FORMAT( date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * 
     * @param type $user_id
     * @param type $year
     * @param type $month
     * @param type $warehouse_id
     * @return boolean
     */
    public function getStaffDailySalesUP_w($user_id, $year, $month, $warehouse_id = NULL) {
        $getwarehouse = str_replace("_", ",", $warehouse_id);
        $myQuery = "SELECT DATE_FORMAT( s.date,  '%e' ) AS date, SUM( COALESCE( s.product_tax, 0 ) ) AS tax1, SUM( COALESCE( s.order_tax, 0 ) ) AS tax2, SUM( COALESCE( s.grand_total, 0 ) ) AS total, SUM( COALESCE( s.total_discount, 0 ) ) AS discount, SUM( COALESCE( s.shipping, 0 ) ) AS shipping, w.name as warehouse  FROM   sma_sales s  LEFT JOIN sma_warehouses w on s.warehouse_id = w.id WHERE ";
        if ($warehouse_id) {
            $myQuery .= " s.warehouse_id IN( {$getwarehouse} ) AND ";
        }
        if ($this->Owner || $this->Admin) {
            if ($user_id) {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        } else {
            if ($this->session->userdata('view_right') == '0') {
                $myQuery .= " s.created_by = {$user_id} AND ";
            }
        }
        $myQuery .= " s.up_sales = 1 AND ";
        $myQuery .= " DATE_FORMAT( s.date,  '%Y-%m' ) =  '{$year}-{$month}'
            GROUP BY DATE_FORMAT( s.date,  '%e' )";
        $q = $this->db->query($myQuery, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * End Urban piper Daily Sales Reports
     */
    /* Urbin Piper Daily Report 1-4-2020 */
    public function getDailyUrbinpiper($date) {

        $query = "SELECT  Count(id) AS invoice, up_channel, SUM( COALESCE( grand_total, 0 ) ) AS total
            FROM  " . $this->db->dbprefix('sales') . "  WHERE  up_sales = 1 AND DATE( `date` ) =  '$date'  GROUP BY up_channel ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    /* Start Category Report */

    public function category_count_data($Data, $search = '') {
        $start_date = $Data['start_date'];
        $end_date = $Data['end_date'];
        $warehouse = $Data['warehouse'];
        $category = $Data['category'];

        $this->db->select('count(*) as num')->from("sma_categories r")->join('sma_categories e', "(e.id=r.parent_id )", 'left');

        if ($category) {
            $this->db->where('r.id', $category);
        }
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $search_value = $search['value'];
                $where_search = "  (r.code like '%" . $search_value . "%' or r.name like '%" . $search_value . "%' or  e.name like '%" . $search_value . "%') ";
                $this->db->where($where_search);
            }
        }
        $q = $this->db->get();
        $result = $q->result_array();
        return $result[0]['num'];
    }

    public function getCategoryLists($Data, $startpoint, $per_page, $search = '') {
        $start_date = $Data['start_date'];
        $end_date = $Data['end_date'];
        $warehouse = $Data['warehouse'];
        $category = $Data['category'];

        $this->db->select('e.name AS parent_name, e.id AS parent_id, r.id AS cid, r.name AS child_name')->from("sma_categories r")->join('sma_categories e', "e.id=r.parent_id", 'left');

        if ($category) {
            $this->db->where('r.id', $category);
        }
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $search_value = $search['value'];
                $where_search = "  (r.code like '%" . $search_value . "%' or r.name like '%" . $search_value . "%' or  e.name like '%" . $search_value . "%') ";
                $this->db->where($where_search);
            }
        }
        $this->db->order_by('COALESCE(parent_name, child_name)');
        if ($startpoint != '') {
            if ($per_page != -1)
                $this->db->limit($per_page, $startpoint);
        }
        $q = $this->db->get();
        //echo $this->db->last_query();
        return $q;
    }

    function getCat($category) {
        $this->db->select("id")->from('sma_categories')->where('id', $category)->where('parent_id', 0);
        $q = $this->db->get();
        //echo $this->db->last_query();
        return $q->result_array();
    }

    function getCatByName($category) {
        $this->db->select("id, parent_id")->from('sma_categories')->where('name', $category)->where('parent_id', 0);
        $q = $this->db->get();
        //echo $this->db->last_query();
        return $q->result_array();
    }

    public function getCategoryListDetails($Data) {
        $start_date = $Data['start_date'];
        $end_date = $Data['end_date'];
        $warehouse = $Data['warehouse'];
        $category = $Data['category'];
        $ResCat = $this->getCat($category);
        //print_r($ResCat);
        if (empty($ResCat)) {
            $cat_id = 'subcategory_id';
        } else {
            $cat_id = $ResCat[0]['id'];
            if ($cat_id == $category) {
                $cat_id = 'category_id';
            } else {
                $cat_id = 'subcategory_id';
            }
        }

        $pp = "( SELECT pp." . $cat_id . " as category, CAST(SUM( pi.quantity ) as DECIMAL(10,2) ) purchasedQty, CAST(SUM( pi.subtotal ) as DECIMAL(10,2)) totalPurchase from sma_products pp
                left JOIN sma_purchase_items pi ON pp.id = pi.product_id 
                left join sma_purchases p ON p.id = pi.purchase_id where 1 ";
        $sp = "( SELECT sp." . $cat_id . " as category, CAST(SUM( si.quantity ) as DECIMAL(10,2)) soldQty, CAST(SUM( si.subtotal ) as DECIMAL(10,2)) totalSale from sma_products sp
                left JOIN sma_sale_items si ON sp.id = si.product_id 
                left join sma_sales s ON s.id = si.sale_id where 1 ";

        if ($start_date || $warehouse) {
            if ($start_date) {
                $pp .= " and (Date(p.date) between '{$start_date}' AND '{$end_date}' ) ";
                $sp .= " and (Date(s.date) between '{$start_date}' AND  '{$end_date}' ) ";
            }
            if ($warehouse) {

                $pp .= " AND pi.warehouse_id IN({$warehouse}) ";
                $sp .= " AND si.warehouse_id IN({$warehouse}) ";
            }
        }
        $pp .= " GROUP BY pp." . $cat_id . " ) PCosts";
        $sp .= " GROUP BY sp." . $cat_id . " ) PSales";


        $this->db->select("sma_categories.id as cid, sma_categories.code, sma_categories.name,
                    SUM( COALESCE( PCosts.purchasedQty, 0 ) ) as PurchasedQty,
                    SUM( COALESCE( PSales.soldQty, 0 ) ) as SoldQty,
                    SUM( COALESCE( PCosts.totalPurchase, 0 ) ) as TotalPurchase,
                    SUM( COALESCE( PSales.totalSale, 0 ) ) as TotalSales,
                    (SUM( COALESCE( PSales.totalSale, 0 ) )- SUM( COALESCE( PCosts.totalPurchase, 0 ) ) ) as Profit", FALSE)->from('sma_categories')->join($sp, 'sma_categories.id = PSales.category', 'left')->join($pp, 'sma_categories.id = PCosts.category', 'left');

        if ($category) {
            $this->db->where('sma_categories.id', $category);
        }

        $this->db->group_by('sma_categories.id, sma_categories.code, sma_categories.name, PSales.SoldQty, PSales.totalSale, PCosts.purchasedQty, PCosts.totalPurchase');
        $q = $this->db->get();
        //echo $this->db->last_query();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row;
            }
        }
        return $data;
    }

    /* End Category Report */

    public function get_overdue_sale($Customer) {
        $Sql = "SELECT s.id, DATE_FORMAT(s.date, '%Y-%m-%d %T') as date, s.reference_no, s.biller, s.customer, s.sale_status, s.grand_total, s.paid, (s.grand_total-s.paid) as balance, s.payment_status, s.attachment, s.return_id, s.delivery_status, c.email as cemail FROM sma_sales as s left join sma_companies c on c.id=s.customer_id  WHERE (s.payment_status='partial' or s.payment_status='due' or s.payment_status='pending') " . $Customer . " order by s.id desc ";
        $Res = $this->db->query($Sql);
        return $Res->result_array();
    }

    /* warhouse report */

    public function getreportbalance($start_date, $end_date, $warehouse) {

        $sql = "SELECT w.`id` as warehouse_id,  w.`name` as warehouse ,sum(s.`grand_total`) as total, sum(s.`paid`) as total_paid 
                    FROM `sma_sales` s 
                    LEFT JOIN `sma_warehouses` w on s.`warehouse_id` = w.`id`
                    ";
//             
        $where = '';

        if ($start_date) {

            $where = "  WHERE DATE(date) BETWEEN '$start_date' AND '$end_date' ";
        }

        $where .= " AND payment_status = 'partial'";

        if ($warehouse) {
            $where .= " AND s.`warehouse_id` = " . $warehouse;
        }
        $sql .= $where;

        $q = $this->db->query($sql);

        $retrundata = $q->row();

        $total_partial = $retrundata->total - $retrundata->total_paid;

        return $total_partial;
    }

    public function getDailyWareSalesItems($date) {
        $query = "SELECT  `product_id` ,`product_code` ,  `product_name` ,  `net_unit_price` , `product_unit_code` unit,
                    SUM(  `quantity` ) qty, SUM(  `item_tax` ) tax, tax as tax_rate, SUM(  `item_discount` ) discount, SUM(  `subtotal` ) total
                FROM  " . $this->db->dbprefix('sale_items') . "  
                WHERE  `sale_id` IN ( SELECT  `id`  FROM  " . $this->db->dbprefix('sales') . "  WHERE DATE( `date` ) =  '$date' )
                GROUP BY  `product_code` 
                ORDER BY  `product_name` ";

        $q = $this->db->query($query, false);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function sale_purchase_chart_details($WarehouseId = 0, $Type) {
        $Whr = '';
        if ($WarehouseId != 0)
            $Whr = " and warehouse_id='$WarehouseId' ";
        if ($Type == 'Monthly') {
            $myQuery = "SELECT MONTHNAME(S.date) as month_name, S.month,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT date_format(date, '%Y-%m') Month, date,
                SUM(grand_total+rounding) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE date >= date_sub( now( ) , INTERVAL 6 MONTH ) $Whr
                GROUP BY date_format(date, '%Y-%m')) S
            LEFT JOIN ( SELECT date_format(date, '%Y-%m') Month,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(grand_total+rounding) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
						WHERE date >= date_sub( now( ) , INTERVAL 6 MONTH ) $Whr
                        GROUP BY date_format(date, '%Y-%m')) P
            ON S.Month = P.Month GROUP BY S.Month
            ORDER BY S.Month";
        } else {
            $myQuery = "SELECT S.date, S.day,
        COALESCE(S.sales, 0) as sales,
        COALESCE( P.purchases, 0 ) as purchases,
        COALESCE(S.tax1, 0) as tax1,
        COALESCE(S.tax2, 0) as tax2,
        COALESCE( P.ptax, 0 ) as ptax
        FROM (  SELECT DATE_FORMAT(date, '%d-%m-%Y') date, DAYNAME(date) day,
                SUM(grand_total+rounding) Sales,
                SUM(product_tax) tax1,
                SUM(order_tax) tax2
                FROM " . $this->db->dbprefix('sales') . "
                WHERE `date` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) $Whr
                GROUP BY date_format(date, '%Y-%m-%d')) S
            LEFT JOIN ( SELECT DATE_FORMAT(date, '%d-%m-%Y') date, DAYNAME(date) day,
                        SUM(product_tax) ptax,
                        SUM(order_tax) otax,
                        SUM(grand_total+rounding) purchases
                        FROM " . $this->db->dbprefix('purchases') . "
						WHERE `date` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) $Whr
                        GROUP BY date_format(date, '%Y-%m-%d')) P
            ON S.day = P.day GROUP BY S.date
            ORDER BY S.date";
        }

        $q = $this->db->query($myQuery);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function sale_brand_chart_details($WarehouseId = 0, $StartDate, $EndDate, $Records = '') {
        $Whr = '';
        if ($WarehouseId != 0)
            $Whr .= " and s.warehouse_id='$WarehouseId' ";
        if ($StartDate != NULL)
            $Whr .= " and (Date(s.date) between '{$StartDate}' AND '{$EndDate}' ) ";
        $myQuery = "SELECT DATE_FORMAT(date, '%d-%m-%Y') as date, MONTHNAME(s.date) as month_name, b.name, sp.brand as brand,sum(si.quantity) as soldQty, sum(si.subtotal) as totalSale from sma_brands b inner join sma_products sp on sp.brand=b.id inner JOIN sma_sale_items si ON sp.id = si.product_id inner join sma_sales s ON s.id = si.sale_id WHERE 1 $Whr GROUP BY sp.brand "; //date_format(s.date, '%Y-%m-%d'),
        //echo $Records; exit;
        if ($Records == 'Top_10') {
            $myQuery .= " order by totalSale desc limit 0,10 ";
        } elseif ($Records == 'Bottom_10') {
            $myQuery .= " order by totalSale asc limit 0,10 ";
        } else {
            $myQuery .= " order by date_format(s.date, '%Y-%m-%d') ";
        }
        $q = $this->db->query($myQuery);
        $DataArr = array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $DataArr[] = $row;
            }
            return $DataArr;
        }
        return FALSE;
    }

    public function purchase_brand_chart_details($WarehouseId = 0, $StartDate, $EndDate, $Records = '') {
        $Whr = '';
        if ($WarehouseId != 0)
            $Whr .= " and s.warehouse_id='$WarehouseId' ";
        if ($StartDate != NULL)
            $Whr .= " and (Date(s.date) between '{$StartDate}' AND '{$EndDate}' ) ";
        $myQuery = "SELECT DATE_FORMAT(s.date, '%d-%m-%Y') as date, MONTHNAME(s.date) as month_name, b.name, sp.brand as brand,sum(si.quantity) as soldQty, sum(si.subtotal) as totalSale from sma_brands b inner join sma_products sp on sp.brand=b.id inner JOIN sma_purchase_items si ON sp.id = si.product_id inner join sma_purchases s ON s.id = si.purchase_id WHERE 1 $Whr GROUP BY sp.brand "; //date_format(s.date, '%Y-%m-%d'),
        //echo $Records; exit;
        if ($Records == 'Top_10') {
            $myQuery .= " order by totalSale desc limit 0,10 ";
        } elseif ($Records == 'Bottom_10') {
            $myQuery .= " order by totalSale asc limit 0,10 ";
        } else {
            $myQuery .= " order by date_format(s.date, '%Y-%m-%d') ";
        }
        $q = $this->db->query($myQuery);
        $DataArr = array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $DataArr[] = $row;
            }
            return $DataArr;
        }
        return FALSE;
    }

    public function sale_categories_chart_details($WarehouseId = 0, $StartDate, $EndDate, $cat_id = '', $Records = '') {
        $Whr = '';
        if ($WarehouseId != 0)
            $Whr .= " and s.warehouse_id='$WarehouseId' ";
        $CatJoin = ' sp.category_id ';
        $Whr_parent = " and c.parent_id=0 ";
        if ($cat_id != '') {
            $Whr_parent = " and c.parent_id='$cat_id' ";
            $CatJoin = ' sp.subcategory_id ';
        }
        $Whr .= $Whr_parent;
        if ($StartDate != NULL)
            $Whr .= " and (Date(s.date) between '{$StartDate}' AND '{$EndDate}' ) ";
        $myQuery = "SELECT c.id, DATE_FORMAT(s.date, '%d-%m-%Y') as date, c.name, sp.category_id as category_id, SUM(si.subtotal) total_sales, c.parent_id from sma_categories c inner join sma_products sp on c.id= $CatJoin inner JOIN sma_sale_items si ON sp.id = si.product_id inner join sma_sales s ON s.id = si.sale_id WHERE 1 $Whr GROUP BY c.id ";
        if ($Records == 'Top_10') {
            $myQuery .= " order by total_sales desc limit 0,10 ";
        } elseif ($Records == 'Bottom_10') {
            $myQuery .= " order by total_sales asc limit 0,10 ";
        } else {
            $myQuery .= " order by date_format(s.date, '%Y-%m-%d') ";
        }
        //echo $myQuery; exit;
        $q = $this->db->query($myQuery);
        $DataArr = array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $DataArr[] = $row;
            }
            return $DataArr;
        }
        return FALSE;
    }

    public function purchase_categories_chart_details($WarehouseId = 0, $StartDate, $EndDate, $cat_id = '', $Records = '') {
        $Whr = '';
        if ($WarehouseId != 0)
            $Whr .= " and s.warehouse_id='$WarehouseId' ";
        $CatJoin = ' sp.category_id ';
        $Whr_parent = " and c.parent_id=0 ";
        if ($cat_id != '') {
            $Whr_parent = " and c.parent_id='$cat_id' ";
            $CatJoin = ' sp.subcategory_id ';
        }
        $Whr .= $Whr_parent;
        if ($StartDate != NULL)
            $Whr .= " and (Date(s.date) between '{$StartDate}' AND '{$EndDate}' ) ";
        $myQuery = "SELECT c.id, DATE_FORMAT(s.date, '%d-%m-%Y') as date, c.name, sp.category_id as category_id, SUM(si.subtotal) total_sales, c.parent_id from sma_categories c inner join sma_products sp on c.id= $CatJoin inner JOIN sma_purchase_items si ON sp.id = si.product_id inner join sma_purchases s ON s.id = si.purchase_id WHERE 1 $Whr GROUP BY c.id ";
        if ($Records == 'Top_10') {
            $myQuery .= " order by total_sales desc limit 0,10 ";
        } elseif ($Records == 'Bottom_10') {
            $myQuery .= " order by total_sales asc limit 0,10 ";
        } else {
            $myQuery .= " order by date_format(s.date, '%Y-%m-%d') ";
        }
        //echo $myQuery; exit;
        $q = $this->db->query($myQuery);
        $DataArr = array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $DataArr[] = $row;
            }
            return $DataArr;
        }
        return FALSE;
    }

    public function sale_purchase_payment_summary_chart($WarehouseId = 0, $start_date, $end_date, $Records = '', $Sale_Purchase) {
        $this->db->select(' DATE_FORMAT(sma_payments.date, "%Y-%m-%d") as date, sum(sma_payments.amount) as Total, sma_payments.paid_by');
        if ($start_date && $end_date) {
            $this->db->where('DATE_FORMAT(sma_payments.date, "%Y-%m-%d") ' . ' BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($WarehouseId != 0) {
            if ($Sale_Purchase == 'Sale') {
                $this->db->join('sma_sales', 'sma_sales.id = sma_payments.sale_id');
                $this->db->where('sma_sales.warehouse_id', $WarehouseId);
            } else {
                $this->db->join('sma_purchases', 'sma_purchases.id = sma_payments.purchase_id');
                $this->db->where('sma_purchases.warehouse_id', $WarehouseId);
            }
        }
        if ($Sale_Purchase == 'Sale')
            $this->db->where('sma_payments.sale_id!=', '');
        else
            $this->db->where('sma_payments.purchase_id!=', '');
        if ($Records == 'Top_10') {
            $this->db->order_by('Total desc');
            $this->db->limit(10);
        } elseif ($Records == 'Bottom_10') {
            $this->db->order_by('Total asc');
            $this->db->limit(10);
        }
        $payment_summary = $this->db->group_by('sma_payments.paid_by')->get('sma_payments')->result();

        return $payment_summary;
    }

//26-09-2020
    public function count_product_varient_purchase_data($Data, $search = '') {
        //inner join sma_warehouses_products_variants wpv on p.id=wpv.product_id
        $Sql = "select count(Distinct spv.product_id) AS num from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_purchase_items ssi on spv.id=ssi.option_id inner join sma_purchases s on s.id=ssi.purchase_id ";
        //if($Data['warehouse'])
        //$Sql .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";
        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $Sql .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $Sql .= "  and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        if ($Data['warehouse'])
            $Sql .= " and ssi.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $Sql .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $Sql .= " and p.brand=" . $Data['brand'];

        if ($Data['start_date']) {
            $Sql .= " and DATE(s.date) BETWEEN '" . $Data['start_date'] . "' and '" . $Data['end_date'] . "'";
        }
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $Sql .= " and spv.name in (" . $Variant . ")";
        $Sql .= " order by p.name desc ";
        $Query = $this->db->query($Sql);
        $result = $Query->result_array();
        return $result[0]['num'];
    }

    function load_product_varient_purchase_data($Data, $startpoint = '', $per_page = '', $search = '') {
        $query = "select p.id as product_id, p.name, p.code, c.name as cat_name, b.name as brand_name, p.quantity as qty, (p.quantity * p.cost) as product_cost ";
        $query .= " from sma_products p inner join sma_product_variants spv on p.id = spv.product_id inner join sma_purchase_items ssi on spv.id=ssi.option_id inner join sma_purchases s on s.id=ssi.purchase_id ";
        //if($Data['warehouse'])
        //$query .= " inner join sma_warehouses_products swp on swp.product_id=p.id ";
        $BJoin = ' left ';
        if ($Data['brand'])
            $BJoin = ' inner ';
        $query .= " inner join sma_categories c on p.category_id=c.id $BJoin join sma_brands b on b.id=p.brand where 1 ";
        if ($Data['warehouse'])
            $query .= " and ssi.warehouse_id=" . $Data['warehouse'];
        if ($Data['category'])
            $query .= " and p.category_id=" . $Data['category'];
        if ($Data['brand'])
            $query .= " and p.brand=" . $Data['brand'];
        if ($Data['start_date']) {
            $query .= " and DATE(s.date) BETWEEN '" . $Data['start_date'] . "' and '" . $Data['end_date'] . "'";
        }
        if (isset($search['value'])) {
            if ($search['value'] != '') {
                $query .= " and (p.name like '%" . $search['value'] . "%' or p.code like '%" . $search['value'] . "%' or c.name like '%" . $search['value'] . "%' or b.name like '%" . $search['value'] . "%' or spv.name like '%" . $search['value'] . "%') ";
            }
        }
        $Variant = $this->site->showVariantFilter();
        if ($Data['Type'] != '')
            $query .= " and spv.name in (" . $Variant . ")";
        $query .= " group by spv.product_id order by p.name desc ";
        if ($Data['v'] == 'export') {
            $startpoint = $Data['start'];
            $per_page = $Data['limit'];
            $query .= " LIMIT {$startpoint} , {$per_page}";
        } else {
            if ($startpoint != '') {
                $query .= " LIMIT {$startpoint} , {$per_page}";
            } else {
                $query .= " ";
            }
        }
        //echo $query; exit;
        return $result = $this->db->query($query);
    }

    //26-09-2020
    public function getProductById($product_id) {
        $this->db->where('id', $product_id);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getCustomerCompanies() {
        $q = $this->db->select('id, name, company')->order_by('name', 'ASC')->get_where('companies', array('group_name' => 'customer'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getTodaySales($date, $warehouse) {

        $query = "SELECT sum(`total`) total, sum(`total_discount`) total_discount, sum(`total_tax`) total_tax, sum(`shipping`) shipping, sum(`grand_total`) grand_total, sum(`paid`) paid, sum(`rounding`) rounding "
                . "FROM " . $this->db->dbprefix('sales') . " "
                . "WHERE DATE(`date`) = '$date' ";

        if ($warehouse) {
            $query .= " AND `warehouse_id` = '$warehouse' ";
        }

        $q = $this->db->query($query, false);

        if ($q->num_rows() > 0) {
            $data = $q->result();
            return $data[0];
        }

        return FALSE;
    }



   /**
     * 
     * @param type $productid
     * @param type $warehouseid
     * @return type
     */
    function warehouseqty($productid, $warehouseid)
  {
      
      $this->db->select('ROUND(SUM(sma_transfer_request_items.request_quantity),2) as wpqty');
      $this->db->join('sma_transfer_request', 'sma_transfer_request_items.transfer_request_id = sma_transfer_request.id ','rigth');
      $this->db->where(['sma_transfer_request_items.product_id' =>$productid, 'sma_transfer_request_items.warehouse_id' => $warehouseid ]);
      $this->db->where_in('sma_transfer_request.status', ['pending']);
     
      $reuslt =  $this->db->get('sma_transfer_request_items')->row();
      return ($reuslt->wpqty?$reuslt->wpqty : 0);

  }


  /**
   * 
   * @param type $id
   * @return type
   */
  public function getWarehouse($id){
    $warehouse = $this->db->where(['id'=>$id])->get('warehouses')->result();
    return $warehouse;
  }  

}
