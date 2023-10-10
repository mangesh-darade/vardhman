<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Webshop_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_webshop_settings() {

        $q = $this->db->get('webshop_settings');

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function get_payment_gatways() {

        $selects = "paypal_pro, stripe, authorize, instamojo, ccavenue, paytm, UPI_QRCODE, razorpay";

        $q = $this->db->select($selects)->get('pos_settings');

        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getParentCategories() {

        $query = "SELECT `id`, `code` ,`name` ,`image` ,
                        `id` as cat_id,(select count(id) from sma_categories where 
                        `parent_id`=`cat_id`) as subcat_count,
                        (SELECT count(`id`) FROM `sma_products` where `category_id` = `sma_categories`.`id` ) as products_count,
                        IF(parent_id IS NULL,0,parent_id) as parent_id                        
                        FROM `sma_categories`                        
                        WHERE `id` in (SELECT `category_id` 
                        FROM `sma_products` group by `category_id`) 
                  ORDER BY `sma_categories`.`name` ASC";

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            return $q->result_array();
        }
        return FALSE;
    }

    public function get_categories() {

        $q = $this->db->select('id, code, name, image, parent_id')->order_by('name', 'asc')->get('categories');

        if ($q->num_rows() > 0) {

            foreach ($q->result() as $row) {

                if ((int) $row->parent_id > 0) {
                    $data[$row->parent_id][$row->id] = $row;
                } else {
                    $data['main'][$row->id] = $row;
                }
            }

            return $data;
        }
        return false;
    }

    public function get_products_list($by = NULL, $byid = NULL, $useHash = FALSE, $limit = 0, $page = 1) {

        $taxes = $this->get_taxes();
        $where = " WHERE p.in_eshop = '1' AND p.is_active = '1' ";
        $warehouse_id = $this->webshop_settings->warehouse_id;

        if ($by == 'category' && $byid) {
            $categories = $byid;
            $where .= $useHash ? " AND ( MD5(p.category_id) = '$categories' OR MD5(p.subcategory_id) = '$categories' ) " : " AND ( p.category_id = '$categories' OR p.subcategory_id = '$categories' ) ";
        } elseif ($by == 'brand' && $byid) {
            $brand = $byid;
            $where .= $useHash ? " AND ( MD5(p.brand) = '$brand' ) " : " AND ( p.brand = '$brand' ) ";
        } elseif ($by == 'products' && $byid) {

            $product_ids = is_array($byid) ? join(',', $byid) : $byid;
            $where .= " AND p.id IN ( $product_ids ) ";
        }

        $subquery_products = "SELECT p.id FROM `sma_products` AS p $where ";

        $query = "SELECT
                    p.id, p.code, p.name, p.type, p.price, p.mrp, p.image, p.weight, p.sale_unit, p.brand, p.category_id, p.subcategory_id, 
                    p.tax_rate AS tax_id, p.tax_method, p.promotion, p.promo_price, p.start_date AS promo_start_date, p.end_date AS promo_end_date, 
                    p.ratings_avarage, p.ratings_count, p.comments_count, p.quantity , p.product_details, p.storage_type                                                           
                    FROM `sma_products` p                              
                    $where ";

        $data['page'] = $page;
        if ($limit) {

            $qNum = $this->db->query($query);
            $total_items = $qNum->num_rows();
            $offset = ( $page - 1 ) * $limit;
            $query .= " LIMIT $limit OFFSET $offset ";
        }

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {

            $data['items_total'] = $total_items ? $total_items : $q->num_rows();

            /*
             * Get Item Variants
             */
            $variants = $this->db->query('SELECT * FROM `sma_product_variants` WHERE product_id IN (' . $subquery_products . ')')->result();
            if ($variants) {
                foreach ($variants as $v_item) {
                    $variant = (array) $v_item;
                    $product_variants[$variant['product_id']][$variant['id']] = $variant;
                }
            }
            // $data['product_variants'] = $product_variants;

            /*
             * Item Stocks Query
             */
            $items_stocks = $this->get_product_stocks($subquery_products, $warehouse_id);

            foreach ($q->result() as $row) {
                $row->quantity = 0;
                if (isset($product_variants[$row->id])) {
                    $row->variants = $product_variants[$row->id];
                    foreach ($product_variants[$row->id] as $variant_id => $variant) {

                        $quantity = isset($items_stocks[$row->id][$variant_id]) ? $items_stocks[$row->id][$variant_id] : 0;
                        $row->quantity += $quantity;

                        if ($row->storage_type == 'loose') {
                            $row->variants[$variant_id]['quantity'] = $quantity / $row->variants[$variant_id]['unit_quantity'];
                        } else {
                            $row->variants[$variant_id]['quantity'] = $quantity;
                        }
                    }
                } else {
                    $row->variants = null;
                    $row->quantity = isset($items_stocks[$row->id][0]) ? $items_stocks[$row->id][0] : 0;
                }
                $row->tax_rate = $taxes[$row->tax_id]->rate;
                if ($useHash) {
                    $data['items'][] = (array) $row;
                } else {
                    $data[$row->subcategory_id][] = (array) $row;
                }
            }//end foreach

            return $data;
        }
        return false;
    }

    /*
      public function get_products_by_brand($brand = NULL, $useHash = FALSE, $limit = 8, $page = 1) {

      $taxes = $this->get_taxes();

      if ($brand) {
      $where = $useHash ? " WHERE MD5(p.brand) = '$brand' OR MD5(p.brand) = '$brand' " : " WHERE p.brand = '$brand' OR p.brand = '$brand' ";
      } else {
      $where = '';
      }

      $query = "SELECT
      p.id, p.code, p.name, p.price, p.mrp, p.image, p.weight, p.sale_unit, p.brand, p.category_id, p.subcategory_id,
      p.tax_rate AS tax_id, p.tax_method, p.promotion, p.promo_price, p.start_date AS promo_start_date, p.end_date AS promo_end_date,
      p.ratings_avarage, p.ratings_count, p.comments_count,p.quantity , p.product_details, p.storage_type,
      V.`id` AS variant_id , V.`product_id`, V.`name` AS variant_name, V.`price` AS variant_price,
      V.`unit_quantity` AS variant_unit_quantity , V.quantity AS variant_quantity
      FROM `sma_product_variants` V
      INNER JOIN (
      SELECT `product_id`, MIN(`price`) minprice from `sma_product_variants` GROUP BY `product_id`
      ) MV
      ON V.`product_id` = MV.`product_id` AND V.price = MV.minprice
      RIGHT JOIN `sma_products` AS p ON p.id = V.`product_id`
      $where
      GROUP BY p.`id`";

      $q = $this->db->query($query);

      if ($q->num_rows() > 0) {
      foreach ($q->result() as $row) {
      $row->tax_rate = $taxes[$row->tax_id]->rate;

      $data[] = (array) $row;
      }
      return $data;
      }
      return false;
      }
     */

    public function get_category_tab_products($category_tabs) {

        if (is_array($category_tabs)) {
            foreach ($category_tabs as $category_id => $subcategoryArr) {
                $categoryProducts[$category_id] = $this->get_products_list('category', $category_id);
            }
            return $categoryProducts;
        }

        return FALSE;
    }

    public function get_tab_products_by_id($tab_products) {

        if (is_array($tab_products)) {
            foreach ($tab_products as $category_id => $productsArr) {
                $products = $this->get_products_list('products', $productsArr);
                unset($products['page'], $products['items_total']);
                foreach ($products as $subcat => $subcat_product) {
                    foreach ($subcat_product as $product) {
                        $categoryTabProducts[$category_id][] = $product;
                    }
                }
            }
            return $categoryTabProducts;
        }

        return FALSE;
    }

    public function get_category_products($categories = NULL, $useHash = FALSE) {

        $taxes = $this->get_taxes();

        $this->db->select('id, code, name, image, price, category_id, subcategory_id, tax_rate AS tax_id, tax_method, '
                . ' promotion, promo_price, start_date, end_date, sale_unit, brand, mrp, ratings_avarage, ratings_count,'
                . ' comments_count, quantity, storage_type');

        if ($categories) {
            if ($useHash) {
                $this->db->where('MD5(category_id)', $categories);
                $this->db->or_where('MD5(subcategory_id)', $categories);
            } else {
                $this->db->where('category_id', $categories);
                $this->db->or_where('subcategory_id', $categories);
            }
        }

        $this->db->order_by('name', 'asc');

        $q = $this->db->get('products');


        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->tax_rate = $taxes[$row->tax_id]->rate;

                if ($categories) {

                    $data[] = (array) $row;
                } else {

                    if ((int) $row->subcategory_id > 0) {
                        $data[$row->category_id][$row->subcategory_id][] = (array) $row;
                    } elseif ((int) $row->category_id > 0) {
                        $data[$row->category_id][] = (array) $row;
                    }
                }//end else
            }
            return $data;
        }
        return false;
    }

    public function get_category_product_variants($categories = NULL) {

        $this->db->select('v.id , p.id AS product_id, v.name, v.cost, v.price, v.quantity, v.unit_quantity');
        $this->db->from('product_variants AS v');
        $this->db->join('products AS p', "p.id = v.product_id", 'left');

        if ($categories) {
            $this->db->where('p.category_id', $categories);
            $this->db->or_where('p.subcategory_id', $categories);
        }

        $this->db->order_by('v.name', 'asc');

        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->product_id][] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_product_by_hash($productHash) {

        $this->db->select('p.id, p.code, p.name, p.price, p.image, p.product_details, p.quantity, p.unit, p.sale_unit, p.brand, '
                . 'p.storage_type, p.category_id, p.subcategory_id, p.tax_rate AS tax_id, t.rate AS tax_rate, p.tax_method, '
                . 'p.promotion, p.promo_price, p.start_date, p.end_date, p.ratings_avarage');
        $this->db->from('products AS p');
        // $this->db->join('product_variants AS pv', 'p.id=pv.product_id', 'left');
        $this->db->join('tax_rates AS t', 't.id=p.tax_rate', 'left');
        $this->db->where('MD5(p.id)', $productHash);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {

            $product = $q->result();

            $data['item'] = (array) $product[0];

            $product_id = $product[0]->id;

            $variants = $this->get_product_variants($product_id);

            $data['images'] = $this->get_product_images($product_id);

            $warehouse_id = $this->webshop_settings->warehouse_id;

            $data['stocks'] = $items_stocks = $this->get_product_stocks($product_id, $warehouse_id);

            $data['variants'] = $variants;
            $product['quantity'] = 0;

            if (is_array($items_stocks)) {
                if (is_array($variants)) {
                    foreach ($variants as $key => $variant) {
                        $quantity = isset($items_stocks[$product_id][$variant['id']]) ? $items_stocks[$product_id][$variant['id']] : 0;
                        $product['quantity'] += $quantity;

                        if ($product['storage_type'] == 'loose') {
                            $variant['quantity'] = $quantity / $variant['unit_quantity'];
                        } else {
                            $variant['quantity'] = $quantity;
                        }

                        $data['variants'][$key] = $variant;
                    }
                } else {
                    $product['quantity'] = $quantity = isset($items_stocks[$product_id][0]) ? $items_stocks[$product_id][0] : 0;
                    ;
                }
            }

            if ($data['variants']) {
                $productVariant = $data['variants'][0];
                $data['item']['variant_id'] = $productVariant['id'];
                $data['item']['variant_name'] = $productVariant['name'];
                $data['item']['variant_price'] = $productVariant['price'];
                $data['item']['variant_unit_quantity'] = $productVariant['unit_quantity'];
                $data['item']['variant_quantity'] = $productVariant['quantity'];
            }

            return $data;
        }
        return false;
    }

    public function get_product_by_id($productIds, $selects = null) {

        if ($selects) {
            $this->db->select($selects);
        }

        $this->db->where_in('id', $productIds);

        $q = $this->db->get('products');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_product_stocks($product_ids = NULL, $warehouse_id = null) {

        $queryStocks = "SELECT SUM(`quantity_balance`) as quantity, `product_id`, `option_id`, `warehouse_id`  
                  FROM `sma_purchase_items` 
                  WHERE  `status` IN ('received', 'partial') ";

        if ($warehouse_id) {
            $queryStocks .= " AND `warehouse_id` = '$warehouse_id' ";
        }
        if ($product_ids) {
            $queryStocks .= " AND `product_id` IN ( $product_ids ) ";
        }

        $queryStocks .= " GROUP BY `option_id`, `product_id` ";

        $qwp = $this->db->query($queryStocks);

        $stocks = $qwp->result();
        if ($stocks) {
            foreach ($stocks as $pstock) {
                $items_stocks[$pstock->product_id][$pstock->option_id] = $pstock->quantity;
            }
        }

        return $items_stocks;
    }

    public function get_product_images($product_id = NULL, $variant_id = null) {

        $where['product_id'] = $product_id;

        if ($variant_id) {
            $where['variant_id'] = $variant_id;
        }

        $q = $this->db->where($where)->get('product_photos');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_variant_images($products = null, $variant_id = null, $image_count = null) {

        $this->db->select('product_id, variant_id, photo');

        if ($image_count == 1) {
            $this->db->group_by('product_id, variant_id');
        }
        if ($products) {
            $this->db->where_in('product_id', $products);
        }
        if ($variant_id) {
            $this->db->where_in('variant_id', $variant_id);
        }
        $q = $this->db->get('product_photos');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {

                if ($image_count == 1) {
                    $key = ($row->variant_id) ? $row->product_id . '_' . $row->variant_id : $row->product_id;
                    $data[$key] = $row->photo;
                } else {
                    $data[] = (array) $row;
                }
            }
            return $data;
        }
        return false;
    }

    public function get_cart_data() {

        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

            if (isset($_SESSION['cart_coupon'])) {
                $data['coupon']['code'] = $_SESSION['cart_coupon']['code'];
                $data['coupon']['discount'] = $_SESSION['cart_coupon']['discount'];
            }

            foreach ($_SESSION['cart'] as $key => $item) {
                $products[] = $item['product_id'];
                if ((int) $item['variant_id']) {
                    $variants[] = $item['variant_id'];
                }
            }

            $data['products'] = $this->get_product_by_id($products, 'id, code, name, image');

            if (isset($variants)) {
                $data['variants'] = $this->get_variant_by_id($variants, 'id, name, unit_quantity');

                $data['variant_images'] = $this->get_variant_images($products, $variants, $image_count = 1);
            }

            return $data;
        }

        return false;
    }

    public function get_variant_by_id($variant_id = NULL) {

        if (!$variant_id)
            return false;

        $q = $this->db->where_in('id', $variant_id)->order_by('name', 'asc')->get('product_variants');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_product_variants($product_id = NULL) {

        $q = $this->db->where('product_id', $product_id)->order_by('price', 'asc')->get('product_variants');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_category_brands($category_id = null) {

        if ($category_id) {
            $where = "p.`category_id` = '$category_id' AND p.`brand` > '0'";
        } else {
            $where = 'p.`brand` > 0';
        }

        $q = $this->db->select('p.`category_id`, p.`brand`, b.id as brand_id , b.name as brand_name, b.image as brand_image')
                ->from('products AS p')
                ->join('brands AS b', 'b.id = p.`brand`', 'left')
                ->where($where)
                ->group_by('p.`category_id`, p.`brand`')
                ->get();

        if ($q->num_rows() > 0) {

            foreach ($q->result() as $row) {
                $data[$row->category_id][] = $row;
            }

            return $data;
        }
        return false;
    }

    public function get_all_brands() {

        $q = $this->db->select('p.`category_id`, b.id , b.name, b.image')
                ->from('products AS p')
                ->join('brands AS b', 'b.id = p.`brand`', 'left')
                ->where('p.`brand` > 0')
                ->group_by('p.`brand`')
                ->order_by('RAND()')
                ->get();

        if ($q->num_rows() > 0) {

            foreach ($q->result() as $row) {
                $data[$row->id] = $row;
            }

            return $data;
        }
        return false;
    }

    public function get_features() {

        $q = $this->db->select('title, subtitle, icon')
                ->from('webshop_features')
                ->where(['is_active' => 1])
                ->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function get_sliders() {

        $q = $this->db->get('webshop_sliders');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->slide_key] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_theme_sections($theme = "theme_1") {

        $q = $this->db->select('id, section_name, section_title, section_data')
                ->from('webshop_homepage_sections')
                ->where(['is_active' => 1, 'display_status' => 1, "$theme" => 1])
                ->order_by('display_order', 'ASC')
                ->get();

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function set_recent_viewed_product($product_id) {

        $now = date('Y-m-d H:i:s');

        if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
            $data = array(
                "user_id" => $_SESSION['user_id'],
                "product_id" => $product_id,
            );
        } else {
            $data = array(
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "product_id" => $product_id,
            );
        }

        $rec = $this->db->select('id,visits_count')->where($data)->get("webshop_recently_viewed")->result();

        if ($rec[0]->id) {
            $data['updated_at'] = $now;
            $data['visits_count'] = $rec[0]->visits_count + 1;
            if ($this->db->where(["id" => $rec[0]->id])->update("webshop_recently_viewed", $data)) {
                return TRUE;
            }
        } else {

            $data['created_at'] = $now;
            $data['updated_at'] = $now;

            if ($this->db->insert("webshop_recently_viewed", $data)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function get_recent_viewed_product() {

        $this->db->select('p.id, rv.product_id, p.name, p.price, p.image, p.tax_rate AS tax_id, t.rate AS tax_rate, p.tax_method, p.promotion, p.promo_price, p.start_date, p.end_date, p.ratings_avarage, pv.id AS variant_id, pv.name AS variant_name, max(pv.price) AS variant_price');
        $this->db->from('products AS p');
        $this->db->join('webshop_recently_viewed AS rv', 'p.id=rv.product_id', 'left');
        $this->db->join('product_variants AS pv', 'p.id=pv.product_id', 'left');
        $this->db->join('tax_rates AS t', 't.id=p.tax_rate', 'left');

        $this->db->where(["rv.ip_address" => $_SERVER['REMOTE_ADDR']]);

        if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
            $this->db->or_where(["rv.user_id" => $_SESSION['user_id']]);
        }

        $q = $this->db->order_by('rv.updated_at', 'desc')->group_by('p.id')->limit(15)->get();

        if ($q->num_rows() > 0) {
            return (array) $q->result();
        }

        return FALSE;
    }

    public function get_taxes() {

        $q = $this->db->get('tax_rates');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = $row;
            }
            return $data;
        }
        return false;
    }

    public function get_state() {

        $q = $this->db->get('state_master');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_units($id = null) {

        if ($id != NULL) {
            $this->db->where('id', $id);
        }
        $q = $this->db->get('units');

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        }
        return false;
    }

    public function get_company_by_id($company_id) {

        $this->db->where(['id' => $company_id]);
        $q = $this->db->get('companies');

        if ($q->num_rows()) {
            $data = (array) $q->result();
            return (array) $data[0];
        } else {
            return FALSE;
        }
    }

    public function get_customer(array $data) {

        if (is_array($data) && !empty($data)) {

            $this->db->select("id,logo as image,customer_group_id,customer_group_name,name,company,pan_card,address,city,state,state_code,postal_code,country,phone,email,vat_no,gstn_no,cf1,cf2,cf6,deposit_amount,price_group_id,price_group_name");

            $this->db->where(['group_id' => 3]);
            $i = 0;
            foreach ($data as $key => $value) {
                $i++;
                if ($i > 1) {
                    $this->db->or_where([$key => $value]);
                } else {
                    $this->db->where([$key => $value]);
                }
            }

            $q = $this->db->get('companies');

            if ($q->num_rows()) {
                $data = (array) $q->result();
                return (array) $data[0];
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function add_customer(array $customerData) {

        $this->db->insert('companies', $customerData);

        $customerData['id'] = $this->db->insert_id();

        return $customerData;
    }

    public function add_address(array $addressData) {

        $this->db->insert('addresses', $addressData);

        $address_id = $this->db->insert_id();

        return $address_id;
    }

    public function get_customer_address($customer_id, $address_id = null) {

        if ($address_id) {
            $where = ['company_id' => $customer_id, 'id' => $address_id];
        } else {
            $where = ['company_id' => $customer_id];
        }

        $q = $this->db->where($where)->order_by('is_default', 'desc')->get('addresses');

        if ($q->num_rows()) {
            foreach ($q->result() as $row) {
                $data[$row->id] = (array) $row;
            }
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_address_by_id($address_id) {

        $q = $this->db->where(['id' => $address_id])->get('addresses');

        if ($q->num_rows()) {
            foreach ($q->result() as $row) {
                $data = (array) $row;
            }
            return $data;
        } else {
            return FALSE;
        }
    }

    public function add_order(array $order, array $order_items) {

        if ($this->db->insert('orders', $order)) {
            $order_id = $this->db->insert_id();
            $now = date('Y-m-d');
            //Get formated Invoice No
            $order_no = $this->sma->invoice_format($order_id, $now);
            //Update formated invoice no
            $this->db->where(['id' => $order_id])->update('orders', ['invoice_no' => $order_no]);

            if ($this->site->getReference('eshop') == $order['reference_no']) {
                $this->site->updateReference('eshop');
            }

            foreach ($order_items as $item) {

                $item['sale_id'] = $order_id;

                $this->db->insert('order_items', $item);
            }

            return $order_id;
        }
        return false;
    }

    public function get_order_by_id($order_id) {

        $q = $this->db->where(['id' => $order_id])->get('orders');

        if ($q->num_rows()) {
            foreach ($q->result() as $row) {
                $data = (array) $row;
            }
            return $data;
        } else {
            return FALSE;
        }
    }

    public function get_customer_orders($customer_id, $order_id = null) {

        $select_order = "o.`id` AS order_id, o.`invoice_no`, o.`date`, o.`reference_no`, o.`total`,o.`total_tax`, o.`shipping`, o.`grand_total`,  o.`sale_status`, o.`payment_status`, o.`payment_method`, o.`rounding`, o.`delivery_status`, o.`sale_invoice_no`, o.`shipping_address_id`, o.`shipping_method`, o.`paid`, o.`total_discount` ";
        $select_order .= ", a.id AS address_id, a.address_name, a.line1, a.line2, a.city, a.postal_code, a.state, a.country, a.phone, a.email_id, a.state_code ";

        if ($order_id) {
            $where = ['MD5(o.id)' => $order_id];
        } else {
            $where = ['o.customer_id' => $customer_id];
        }

        $oq = $this->db->select($select_order)
                ->from('orders o')
                ->join('addresses a', 'a.id=o.shipping_address_id', 'left')
                ->where($where)
                ->order_by('o.date', 'desc')
                ->get();

        if ($oq->num_rows()) {
            foreach ($oq->result() as $oid => $orow) {
                $data['orders'][] = $orow;   //Fetch Customer Orders
            }

            //Get Order Payments.
            if ($order_id) {
                $op = $this->db->where(['MD5(order_id)' => $order_id])->get('payments');
                if ($op->num_rows()) {
                    foreach ($op->result() as $oprow) {
                        $data['payments'][] = $oprow;   //Fetch Order Payments if exista
                    }
                }
            }//end if.
            //Order Items Query
            $select_items = "oi.`id` AS order_item_id, oi.`sale_id` AS order_id, oi.`product_id`, p.`image`, oi.`product_code`, oi.`product_name`, pv.name AS option_name, oi.`option_id`, oi.`unit_price`, oi.`quantity`, oi.`item_tax`, oi.`tax`, oi.`discount`, oi.`item_discount`, oi.`subtotal`, oi.`gst_rate`, oi.`cgst`, oi.`sgst`, oi.`igst`, oi.`tax_method`";

            $this->db->select($select_items);
            $this->db->from('order_items oi');
            $this->db->join('product_variants pv', 'pv.id=oi.option_id', 'left');
            $this->db->join('products p', 'p.id=oi.product_id', 'left');

            if ($order_id) {
                $this->db->where(['MD5(oi.sale_id)' => $order_id]);
            } else {
                $this->db->join('orders o', 'o.id=oi.sale_id', 'left');
                $this->db->where(['o.customer_id' => $customer_id]);
            }

            $oiq = $this->db->get();

            if ($oiq->num_rows()) {
                foreach ($oiq->result() as $row) {
                    $data['items'][$row->order_id][] = $row;     //Fetch Order Items.
                }
                return $data;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function get_order_items_by_order_id($order_id) {

        $q = $this->db->where(['sale_id' => $order_id])->get('order_items');

        if ($q->num_rows()) {
            foreach ($q->result() as $row) {
                $data[] = (array) $row;
            }
            return $data;
        } else {
            return FALSE;
        }
    }

    public function authenticate_user($username, $passwdHash) {

        $q = $this->db->select('id, group_name, customer_group_id, customer_group_name, name, phone, email')
                ->from('companies')
                ->group_start()
                ->where(['group_id' => 3])
                ->group_start()
                ->where(['email' => $username])
                ->or_where(['phone' => $username])
                ->group_end()
                ->where(['password' => $passwdHash])
                ->group_end()
                ->get();

        if ($q->num_rows()) {
            $data = (array) $q->result();
            return $data[0];
        } else {
            return FALSE;
        }
    }

    public function search_category_products($keyword, $category = NULL) {

        $taxes = $this->get_taxes();

        $this->db->select('id, code, name, image, price, category_id, subcategory_id, tax_rate AS tax_id, tax_method, '
                . ' promotion, promo_price, start_date, end_date, sale_unit, brand, mrp, ratings_avarage, ratings_count,'
                . ' comments_count, quantity, storage_type');

        if ($category) {
            $this->db->where('category_id', $category);
        }

        $this->db->where("( name LIKE '%$keyword%' OR code LIKE '%$keyword%' OR product_details LIKE '%$keyword%' )");

        $this->db->order_by('name', 'asc');

        $q = $this->db->get('products');


        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->tax_rate = $taxes[$row->tax_id]->rate;

               // if ($category) {


                   $warehouse_id = $this->webshop_settings->warehouse_id;

                    $items_stocks = $this->get_product_stocks($product_id, $warehouse_id);

 
                
                    $variants = $this->get_product_variants($row->id);
                    if (is_array($items_stocks)) {
                        if (is_array($variants)) {
                            foreach ($variants as $key => $variant) {
                                $quantity = isset($items_stocks[$product_id][$variant['id']]) ? $items_stocks[$product_id][$variant['id']] : 0;
                                $product['quantity'] += $quantity;

                                if ($product['storage_type'] == 'loose') {
                                    $variant['quantity'] = $quantity / $variant['unit_quantity'];
                                } else {
                                    $variant['quantity'] = $quantity;
                                }

                                $row->variants->$key = $variant;
                            }
                        } else {
                            $product['quantity'] = $quantity = isset($items_stocks[$product_id][0]) ? $items_stocks[$product_id][0] : 0;
                           
                        }
                    }

                    if ($row->variants) {
                    
                        $row->item->variant_id = $productVariant['id'];                        $row->item->variant_id = $productVariant['id'];
                        $row->item->variant_name = $productVariant['name'];
                        $row->item->variant_price = $productVariant['price'];
                        $row->item->variant_unit_quantity = $productVariant['unit_quantity']; 
                        $row->item->variant_quantity = $productVariant['quantity'];
                    }
                
                    $row->variants = $variants;
                    $data[] = (array) $row;
                /*} else {

                    if ((int) $row->subcategory_id > 0) {
                        $data[$row->category_id][$row->subcategory_id] = (array) $row;
                    } elseif ((int) $row->category_id > 0) {
                        $data[$row->category_id]= (array) $row;
                    }
                }//end else*/
            }
            return $data;
        }
        return false;
    }

    public function search_other_products($keyword, $category = NULL) {

        $taxes = $this->get_taxes();

        $this->db->select('id, code, name, image, price, category_id, subcategory_id, tax_rate AS tax_id, tax_method, '
                . ' promotion, promo_price, start_date, end_date, sale_unit, brand, mrp, ratings_avarage, ratings_count,'
                . ' comments_count, quantity, storage_type');

        if ($category) {
            $this->db->where("category_id != $category");
        }

        $this->db->where("( name LIKE '%$keyword%' OR code LIKE '%$keyword%' OR product_details LIKE '%$keyword%' )");

        $this->db->order_by('name', 'asc');

        $q = $this->db->get('products');


        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $row->tax_rate = $row->tax_id ? $taxes[$row->tax_id]->rate : 0;

                $data[] = (array) $row;
            }
            return $data;
        }
        return false;
    }
    
    public function update_new_password($userid , $new_password_hash) {
        
        if($this->db->where(['id'=>$userid])->update('companies', ['password' => $new_password_hash])){
            return true;
        }
        return false;
    }
    
    public function is_valid_current_password($userid , $password) {
        
        $q = $this->db->select('id')
                ->from('companies')               
                ->where(['group_id' => 3, 'id' => $userid, 'password' => md5($password)])
                ->get();

        if ($q->num_rows()) {            
            return true;
        } else {
            return false;
        }
        
    }
    
    
    // Instamojo Payment Gateway


    public function instamojoEshop($data) {
        $this->load->library('instamojo');
        $ci = get_instance();
        $ci->config->load('payment_gateways', TRUE);
        $payment_config = $ci->config->item('payment_gateways');
        $instamojo_credential = $payment_config['instamojo'];
        $api = new Instamojo($instamojo_credential['API_KEY'], $instamojo_credential['AUTH_TOKEN'], $instamojo_credential['API_URL']);

        $CustomerData = $this->get_customer(['id' => $data['customer_id']]);
//       print_r($CustomerData);
        $res = array();
        try {

            $response = $api->paymentRequestCreate(array(
                "purpose" => 'Sales', //$data['x_description'],
                "amount" => $data['amount'],
                "send_email" => true,
                "email" => $data['billing_email'],
                "buyer_name" => $CustomerData['name'],
                "phone" => $data['billing_tel'],
                "redirect_url" => $data['redirect_url'],
            ));
            if (is_array($response)):
                $json_decode = $response;
            elseif (is_string($response)) :
                $json_decode = json_decode($response, true);
            endif;

            if (isset($json_decode['longurl']) && !empty($json_decode['longurl'])) {
                $res['longurl'] = $json_decode['longurl'];
                $arr = array();
                $arr['order_id'] = $data["order_id"];
                $arr['request_response'] = serialize($json_decode);
                $arr['update_date'] = date("Y-m-d H:i:s");
                $arr['request_id'] = $json_decode['id'];
                $this->db->insert('instamojo', $arr);
            }
        } catch (Exception $e) {
            $res['error'] = $e->getMessage();
        }
        return $res;
    }

    public function getInstamojoEshopTransaction($arr) {
        if (is_array($arr)):
            $q = $this->db->get_where('instamojo', $arr, 1);
            if ($q->num_rows() > 0) {
                return $q->row();
            }
        endif;
        return FALSE;
    }

    public function updateInstamojoEshopTransaction($id, $data = array()) {
        $this->db->where('request_id', $id);
        if ($this->db->update('instamojo', $data)) {
            return true;
        }
        return false;
    }

    public function instomojoEshopAfterSale($result, $order_id) {
        $payment = array();
        $payment['transaction_id'] = $result['payment_id'];
        $payment['amount'] = $result['amount'];
        $payment['currency'] = $result['currency'];
        $payment['order_id'] = $order_id;
        $payment['paid_by'] = 'instomojo';
        $payment['date'] = $result['created_at'];
        $payment['reference_no'] = 'PAY/' . $order_id . '/' . $this->site->getReference('pay');
        $payment['type'] = 'received';
        if (!empty($payment['transaction_id']) && !empty($payment['amount']) && !empty($payment['order_id'])):
            $this->updateOrder($order_id, array('sale_status' => 'completed'));
            $this->db->insert('payments', $payment);
            $pay_id = $this->db->insert_id();
            $this->site->updateReference('pay');
            $this->site->syncOrderPayments($order_id);
            return $order_id;
        endif;
        return false;
    }

    public function updateOrder($id, $data = array()) {

        $this->db->where('id', $id);
        if ($this->db->update('orders', $data)) {
            return $id;
        }
        return false;
    }

    public function get_coupon_data($coupon_code) {

        if (trim($coupon_code) != '') {
            $q = $this->db->select("id, coupon_code, customer_group_id, customer_id, discount_rate, expiry_date, is_active, max_coupons, maximum_discount_amount, minimum_cart_amount, status, used_coupons")
                    ->order_by('id', 'desc')
                    ->limit(1)
                    ->where(['coupon_code' => $coupon_code, 'is_active' => 1, 'is_deleted' => 0, 'status' => 'active'])
                    ->get('discount_coupons');

            if ($q->num_rows() > 0) {
                return $q->row();
            }
        }
        return FALSE;
    }

    public function add_to_wishlist($data) {

        $wishlist = $this->db->where($data)->get('eshop_wishlist')->result();

        if (!count($wishlist)) {
            $this->db->insert('eshop_wishlist', $data);
            if ($this->db->insert_id()) {
                return $this->db->where('user_id', $data['user_id'])->get('eshop_wishlist')->result();
            }
        }
    }

    public function remove_from_wishlist($data) {
        echo "<pre>Data:";
        print_r($data);

        $wishlist = $this->db->select('id')->where($data)->get('eshop_wishlist')->result();
        print_r($wishlist);
        if (count($wishlist)) {
            echo "###" . $id = $wishlist[0]->id;
            $this->db->delete('eshop_wishlist', ['id' => $id]);
            if ($this->db->affected_rows()) {
                return $this->db->where('user_id', $data['user_id'])->get('eshop_wishlist')->result();
            }
        }
        return false;
    }

    public function get_wishlist($user_id) {

        return $this->db->where('user_id', $user_id)->get('eshop_wishlist')->result();
    }

    public function get_wishlist_count($user_id) {

        $result = $this->db->select('count(id) AS count')->where('user_id', $user_id)->get('eshop_wishlist')->result();

        return $result[0]->count;
    }

    public function getCustomPages($page_hash_id = null) {
        $select = 'id, page_key, page_title, page_file, page_type, page_section, is_active, updated_at';
        if ($page_hash_id) {
            $select = $select . ', page_text';
            $q = $this->db->select($select)->where(['md5(id)' => $page_hash_id])->get('webshop_static_pages');
        } else {
            $q = $this->db->select($select)->where(['is_active' => 1])->order_by('page_section, page_title')->get('webshop_static_pages');
        }

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                if ($page_hash_id) {
                    $data = (array) $row;
                } else {
                    $data[$row->page_section][] = (array) $row;
                }
            }
            return $data;
        }
        return false;
    }

    public function set_customer_address($data) {

        if ($this->db->insert('addresses', $data)) {
            return $this->db->insert_id();
        }
    }

    public function set_address_default($customer_id, $address_id) {
        if ($this->db->where(['company_id' => $customer_id])->update('addresses', ['is_default' => 0])) {
            if ($this->db->where(['id' => $address_id])->update('addresses', ['is_default' => 1])) {
                return true;
            }
        }
    }

    public function update_customer_address($data, $id) {
        if ($this->db->where(['id' => $id])->update('addresses', $data)) {
            return true;
        }
    }

    public function delete_address($address_id) {

        if ($this->db->where(['id' => $address_id])->delete('addresses')) {
            return true;
        }
    }

    public function deleteSale($id) {

        if ($this->db->delete('sma_order_items', ['sale_id' => $id])) {

            $this->db->delete('sma_orders_items_tax', array('order_id' => $id));
            $this->db->delete('sma_orders', array('id' => $id));
            $this->db->delete('payments', array('order_id' => $id));
            return true;
        }
        return FALSE;
    }

    public function set_profile_photo($file_name, $user_id) {

        if ($this->db->where(['id' => $user_id])->update('companies', ['logo' => $file_name])) {
            return true;
        }
        return false;
    }

    public function update_profile($data, $user_id) {

        if ($this->db->where(['id' => $user_id])->update('companies', $data)) {
            return true;
        }
        return false;
    }

    /**
     * Paytn Payment Gateway
     */

    /**
     * 
     * @param type $arr
     * @return boolean
     */
    public function getPaytmTransaction($arr) {
        if (is_array($arr)):
            $q = $this->db->get_where('paytm', $arr, 1);
            if ($q->num_rows() > 0) {
                return $q->row();
            }
        endif;
        return FALSE;
    }

    /**
     * Paytm Payment
     * @param type $data
     */
    public function addPaytmTransaction($data) {
        $arr = array();
        $arr['order_id'] = $data["order_id"];
        $arr['request_data'] = serialize($data["req_data"]);
        $arr['created_time'] = date("Y-m-d H:i:s");
        $this->db->insert('paytm', $arr);
    }

    /**
     * 
     * @param type $id
     * @param type $data
     * @return boolean
     */
    public function updatePaytmTransaction($id, $data = array()) {

        $this->db->where('eshop_order', $id);
        if ($this->db->update('paytm', $data)) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param type $time
     * @return boolean
     */
    public function paytmTransTime($time) {
        if (!empty($time)) {
            $arr1 = explode(".", $time);

            return $arr1[0];
        }
        return false;
    }

    /**
     * Payment Table store details
     */
    public function PaytmAfterSale($result, $sid) {
        $payment = array();
        $payment['transaction_id'] = $result['TXNID'];
        $payment['amount'] = $result['TXNAMOUNT'];
        $payment['currency'] = $result['CURRENCY'];
        $payment['order_id'] = $sid;
        $payment['paid_by'] = 'paytm';
        $payment['reference_no'] = $this->site->getReference('pay');
        $payment['type'] = 'received';
        $trans_date = $this->paytmTransTime($result['TXNDATE']);
        if (!empty($trans_date)):
            $payment['date'] = $trans_date;
        endif;

        if (!empty($payment['transaction_id']) && !empty($payment['amount']) && !empty($payment['order_id'])):
            $this->updateOrder($payment['order_id'], array('sale_status' => 'completed'));
            $this->db->insert('payments', $payment);
            $pay_id = $this->db->insert_id();
            $this->site->syncOrderPayments($payment['order_id']);
            //  $this->updatePaytmStatusOrder($payment['order_id'], 'pending', 'Paytm Payment', 'paid', $payment['amount']);
            return $sid;
        endif;

        return false;
    }

    /**
     * 
     * @param type $id
     * @param type $status
     * @param type $note
     * @param type $payStatus
     * @return boolean
     */
    public function updatePaytmStatusOrder($id, $status, $note, $payStatus = '', $PaidAmt) {
        $sale = $this->get_order_by_id($id);
        $items = $this->get_order_items_by_order_id($id);
        $cost = array();
        if ($status == 'completed' && $status != $sale->sale_status) {
            foreach ($items as $item) {
                $items_array[] = (array) $item;
            }
            $cost = $this->site->costing($items_array);
        }

        $payment_status = (empty($payStatus)) ? $sale->payment_status : $payStatus;

        $this->db->update('orders', array('sale_status' => $status, 'payment_status' => $payment_status, 'note' => $note, 'paid' => $PaidAmt), array('id' => $id));

        return false;
    }

    /**
     * End paytm payment gateway
     */
    /**
     * Razorpay Payment Gateway
     */

    /**
     * Razorpay Payment Store
     * @param type $result
     * @param type $sid
     * @return boolean
     */
    public function RazorPayAfterSale($result, $sid) {

        $payment = array();
        $payment['transaction_id'] = $result['razorpay_payment_id'];
        $payment['amount'] = $result['amount'];
        $payment['currency'] = $result['currency'];
        $payment['order_id'] = $sid;
        $payment['paid_by'] = 'razorpay';
        $payment['reference_no'] = $this->site->getReference('pay');
        $payment['type'] = 'received';
        $trans_date = date('Y-m-d H:i:s');
        if (!empty($trans_date)):
            $payment['date'] = $trans_date;
        endif;

        if (!empty($payment['transaction_id']) && !empty($payment['amount']) && !empty($payment['order_id'])):
            $this->updateOrder($payment['order_id'], array('sale_status' => 'completed'));

            $this->db->insert('payments', $payment);
            $pay_id = $this->db->insert_id();
            $this->site->updateReference('pay');
            $this->site->syncOrderPayments($payment['order_id']);
//            $this->updatePaytmStatusOrder($payment['order_id'], 'pending', 'Paytm Payment', 'paid', $payment['amount']);
            return $sid;
        endif;

        return false;
    }

    /**
     * End Razorpay Payment Gateway
     */

    /**
     * Address Manage Automatic Default
     * @param type $customer_id
     * @return type
     */
    public function getAddressDefault($customer_id, $addressType){
        
        $addressId =  $this->db->select('id')->where(['company_id' =>$customer_id])->order_by('id','ASC')->get('addresses')->row();
        $address_id = $addressId->id;
        if($addressType == 'default'){
        if ($this->db->where(['company_id' => $customer_id])->update('addresses', ['is_default' => 0])) {
             if ($this->db->where(['id' => $address_id])->update('addresses', ['is_default' => 1])) {
               
                 return $address_id;
             }
         }
       }
       return $address_id;
    }
  
}

//end class