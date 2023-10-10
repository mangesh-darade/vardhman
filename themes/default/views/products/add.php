<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = array();
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#subcategory').change(function () {
            var id = $(this).val();
            if (id) {
                setProductTaxRate(id);
            }
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        console.log(scdata);
                        if (scdata != null) {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });

                setProductTaxRate(v);

            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });

    function setProductTaxRate(id) {

        $.ajax({
            type: "get",
            async: false,
            url: "<?= site_url('products/getCategoryTaxrate') ?>/" + id,
            dataType: "json",
            success: function (taxrateid) {
                if (taxrateid != null) {
                    $('#tax_rate').val(taxrateid);
                    $('#tax_rate').select2().trigger('change');
                } else {
                    $('#tax_rate').val(1);
                    $('#tax_rate').select2().trigger('change');
                }
            },
            error: function () {
                bootbox.alert('<?= lang('ajax_error') ?>');
                $('#modal-loading').hide();
            }
        });
    }

</script>
<style>
    .select2-container-multi{
        height: auto;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_product'); ?></h2>
    </div>
<p class="introtext"><?php echo lang('enter_info'); ?></p>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("products/add", $attrib);
                ?>
                <input type="hidden" name="pos_type" id="pos_type" value="<?= $Settings->pos_type ?>" />
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("product_type", "type") ?>
                                <?php
                                $opts = array('standard' => lang('standard'), 'combo' => lang('combo'), 'digital' => lang('digital'), 'service' => lang('service'));
                                echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                                ?>
                            </div>
                        </div>                    
                        <div class="col-md-6">
                            <div class="form-group all ">
                                <?= lang("Storage Type", "storage_type") ?>
                                <?php                        
                                $ist = ['packed'=>'Packed Products','loose'=>'Loose Products'];
                                echo form_dropdown('storage_type', $ist, (isset($_POST['storage_type']) ? $_POST['storage_type'] : ''), 'class="form-control select" id="storage_type" placeholder="' . lang("select") . " " . lang("division") . '" style="width:100%"')
                                ?>
                            </div>
                         </div>                     
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group all">
                                <?= lang("category", "category") ?>
                                <?php
                                $cat[''] = "";
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category"  placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group all">
                                <?= lang("subcategory", "subcategory") ?>
                                <div class="controls" id="subcat_data"> <?php
                                    echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                          <div class="form-group all">
                            <?= lang("product_name", "name") ?>
                            <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control" id="name" required="required" autocomplete="off" '); ?>
                          </div>
                        </div>
                        <div class="col-md-4">
                              <div class="form-group all">
                                <?= lang("product_code", "code") ?>
                                    <div class="input-group">
                                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                                        <span class="input-group-addon pointer" id="random_num" style="padding: 1px 10px;">
                                            <i class="fa fa-random"></i>
                                        </span>
                                    </div>
                               <!-- <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>-->
                               </div>
                        </div>
                            <script>
                              $("document").ready(function () {
                                  setTimeout(function () {
                                      var url = "<?= site_url('products/add') ?>";
                                      //alert(url);
                                      $("[id*='random_num']").trigger('click');
                                      //window.location.href='products/add';
                                      return false;
                                  }, 10);
                              });
                          </script>
                    </div>
                      <div class="row">
                        <div class="col-md-4">
                            <div class="form-group all">
                                        <?= lang("Article Number", "Article Number") ?>
                                <?= form_input('article_code', (isset($_POST['article_code']) ? $_POST['article_code'] : ($product ? $product->article_code : '')), 'class="form-control" id="article_no"   type="text"  ') ?>
                                <span id="error" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                            </div> 
                        </div>  
                        <!-- End 12-03-19 -->
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang("hsn_code", "hsn_code") ?>
                                <?= form_input('hsn_code', (isset($_POST['hsn_code']) ? $_POST['hsn_code'] : ($product ? $product->hsn_code : '')), 'class="form-control" id="hsn_code"  '); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group all">                         
                                <?= lang("brand", "brand") ?>
                                <?php
                                if (!empty($brands) && is_array($brands)) {
                        
                                    $brnd[''] = "";

                                    foreach ($brands as $brand) {
                                        $brnd[$brand->id] = $brand->name;
                                    }                                    
                                    echo form_dropdown('brand', $brnd, (isset($_POST['brand']) ? $_POST['brand'] : ($product ? $product->brand : '')), 'class="form-control select" id="brand" placeholder="' . lang("select") . " " . lang("brand") . '" style="width:100%"');
                                }   
                                ?>
                            </div>
                        </div>
                    </div>
                      
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group standard">
                                <?= lang('product_unit', 'unit'); ?>
                                <?php
                                $pu[''] = lang('select') . ' ' . lang('unit');
                                foreach ($base_units as $bu) {
                                    $pu[$bu->id] = $bu->name . ' (' . $bu->code . ')';
                                }
                                ?>
                                <?= form_dropdown('unit', $pu, set_value('unit', ($product ? $product->unit : '')), 'class="form-control tip" id="unit" required="required" style="width:100%;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group standard">
                                <?= lang('default_sale_unit', 'default_sale_unit'); ?>
                                <?php $uopts[''] = lang('select_unit_first'); ?>
                                <?= form_dropdown('default_sale_unit', $uopts, ($product ? $product->sale_unit : ''), 'class="form-control" id="default_sale_unit" style="width:100%;"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group standard">
                                <?= lang('default_purchase_unit', 'default_purchase_unit'); ?>
                                <?= form_dropdown('default_purchase_unit', $uopts, ($product ? $product->purchase_unit : ''), 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang("product_cost", "cost") ?> *
                                <?= form_input('cost', (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->sma->formatDecimal($product->cost) : '')), 'class="form-control tip custom_price" id="cost" required="required"') ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang("product_price", "price") ?>
                                <?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->sma->formatDecimal($product->price) : '')), 'class="form-control tip custom_price" id="price" required="required"') ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang("product_mrp", "price") ?>
                                <?= form_input('mrp', (isset($_POST['mrp']) ? $_POST['mrp'] : ($product ? $this->sma->formatDecimal($product->mrp) : '')), 'class="form-control tip custom_price" id="mrp" required="required"') ?>
                            </div>
                        </div>
                    </div> 
                    
                    <?php if (in_array($Settings->pos_type , ['restaurant'])) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group all">
                                 <?= lang("division", "division") ?>
                                <?php
                                $br[''] = "";
                                foreach ($divisions as $division) {
                                    $br[$division->id] = $division->name;
                                }
                                echo form_dropdown('division', $br, (isset($_POST['division']) ? $_POST['division'] : ($product ? $product->division : $_SESSION['division'])), 'class="form-control select" id="division" placeholder="' . lang("select") . " " . lang("division") . '" style="width:100%"')
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if (in_array($Settings->pos_type , ['fruits_vegetables', 'fruits_vegetabl', 'grocerylite', 'grocery'])) { ?>
                    <div class="form-group all">
                        <?= lang("Product_Weight", 'weight') ?>
                        <div class="input-group">
                            <?= form_input('weight', (isset($_POST['weight']) ? $_POST['weight'] : ($product ? $product->weight : '')), 'class="form-control" id="weight" size="6" ') ?>
                            <span class="input-group-addon" style="padding: 1px 10px;">
                                / Kilogram (KG)
                            </span>
                        </div>  
                        <span class="help-block"><?= lang('Products weight should be in Kilogram (Ex. 1Kg = 1 | 500Gm = 0.500 | 250Gm = 0.250, etc.)') ?></span>
                    </div>
                    <?php } ?>
                    <!-- 12-03-19 -->
                  
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group standard">
                                <?= lang("alert_quantity", "alert_quantity") ?>
                                <div class="input-group"> <?= form_input('alert_quantity', (isset($_POST['alert_quantity']) ? $_POST['alert_quantity'] : ($product ? $this->sma->formatQuantity($product->alert_quantity) : '')), 'class="form-control tip" id="alert_quantity"') ?>
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="track_quantity" id="track_quantity"
                                               value="1" <?= ($product ? (isset($product->track_quantity) ? 'checked="checked"' : '') : 'checked="checked"') ?>>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?= lang("barcode_symbology", "barcode_symbology") ?>
                                <?php
                                $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E');
                                echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                    </div>
                    
                  
                    <div class="form-group">
                        <input type="checkbox" class="checkbox" value="1" name="promotion" id="promotion" <?= $this->input->post('promotion') ? 'checked="checked"' : ''; ?>>
                        <label for="promotion" class="padding05">
                            <?= lang('promotion'); ?>
                        </label>
                    </div>
                   

                    <div id="promo" style="display:none;">
                        <div class="well well-sm">
                            <div class="form-group">
                                <?= lang('promo_price', 'promo_price'); ?> 
                                <?= form_input('promo_price', set_value('promo_price'), 'class="form-control tip custom_price" id="promo_price" required="required"'); ?>
                            </div>
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?= form_input('start_date', set_value('start_date'), 'class="form-control tip datetime" id="start_date"'); ?>
                            </div>
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?= form_input('end_date', set_value('end_date'), 'class="form-control tip datetime" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($Settings->tax1) { ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group all" >
                                    <?= lang("product_tax", "tax_rate") ?>
                                    <?php
                                    $tx[""] = "";
                                    foreach ($tax_rates as $tax) {
                                        if($tax->is_substitutable == 0) {
                                            $tx[$tax->id] = $tax->name;
                                        }
                                    }
                                    echo form_dropdown('tax_rate', $tx, (isset($_POST['tax_rate']) ? $_POST['tax_rate'] : ($product ? $product->tax_rate : $Settings->default_tax_rate)), 'class="form-control select" id="tax_rate" placeholder="' . lang("select") . ' ' . lang("product_tax") . '" style="width:100%"')
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                               <div class="form-group all"  >
                                   <?= lang("tax_method", "tax_method") ?>
                                   <?php
                                   $tm = array('0' => lang('inclusive'), '1' => lang('exclusive'));
                                   echo form_dropdown('tax_method', $tm, (isset($_POST['tax_method']) ? $_POST['tax_method'] : ($product ? $product->tax_method : '')), 'class="form-control select" id="tax_method" placeholder="' . lang("select") . ' ' . lang("tax_method") . '" style="width:100%"')
                                   ?>
                               </div>
                            </div>
                        </div>
                    <?php } ?>
                   

                    <div class="form-group all">
                        <?= lang("product_image", "product_image") ?>
                        <input id="product_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="product_image" data-show-upload="false"
                               data-show-preview="false" accept="image/*" class="form-control file">
                    </div>

                    <div class="form-group all">
                        <?= lang("product_gallery_images", "images") ?>
                        <input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple="true" data-show-upload="false"
                               data-show-preview="false" class="form-control file" accept="image/*">
                    </div>
                    <div id="img-details"></div>


                    <!--- Restaurant Type POS  ---->                    
                    <?php if ($Settings->pos_type == 'restaurant') { ?>
                        <?php if ($Settings->product_external_platform == '1') { ?>
                            <!--- Urbanpiper  ----> 
                            <div class="form-group all">
                                <?= lang("Used By External Platform (Ex. Zomato, Swiggy, Etc.)", "UrbanPiper Products") ?> 
                                <select class="form-control" name="up_items" id="urbanpiperitem">
                                    <option value="1">Yes</option>
                                    <option selected="selected" value="0"> No </option>
                                </select>     
                            </div>                     
                            <div id="urbanpipercontain" style="display:none">
                                <fieldset style=" border: 1px solid #ccc; padding: 5px;">
                                    <legend style="width: auto; padding: 0px 10px; border-bottom: 0;margin-bottom: 0px;">External Platform Options </legend>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <?= lang("Price ", "Price") ?>
                                                <input  class="form-control" type="number" name="upprice" id="upprice" />                                 
                                            </div> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <?= lang("Food Type ", "Food Type") ?>
                                                <select class="form-control" name="up_food_type" id="up_food_type">
                                                    <option value="">Select  </option>
                                                    <?php foreach ($foodtype as $foodtype_value) { ?>
                                                        <option value="<?= $foodtype_value->id ?>"><?= $foodtype_value->food_type ?></option>
                                                    <?php } ?>
                                                </select>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <?= lang("Is Available", "Is Available") ?>
                                                <select class="form-control" name="available" id="available">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>    
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <?= lang("Sold At Store", "Sold At Store") ?>
                                                <select class="form-control" name="sold_at_store" id="sold_at_store">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <?= lang("Is Recommended", "Is Recommended") ?>
                                                <select class="form-control" name="recommended" id="recommended">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>    
                                            </div>
                                        </div>                                
                                        <div class="col-sm-6">
                                           <div class="form-group ">
                                                <?= lang("Manage_stock", "Manage_stock") ?>
                                                <select class="form-control" name="manage_stock" id="manage_stock">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>    
                                            </div>
                                        </div>
                                    </div>                          
                                    <div class="row">

                                         <div class="col-sm-12">
                                            <div class="form-group ">
                                                <?= lang("Tags for Default ", "Tags") ?> <small class="text-info">(*Use comma for multiple tags)</small><br/> 
                                                <select class="form-control" name="default_tag" id="default_tag" data-role="tagsinput">
                                                    <option value="packaged-good"> Packaged Good</option> 
                                                </select>      
                                             
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group ">
                                                <?= lang("Tags for Zomato ", "Tags") ?> <small class="text-info">(*Use comma for multiple tags)</small><br/> 
                                                <input class="form-control" type="text" name="tag_zomato" id="tag_zomato" data-role="tagsinput" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group ">
                                                <?= lang("Tags for Swiggy ", "Tags") ?> <small class="text-info">(*Use comma for multiple tags)</small><br/> 
                                                <input class="form-control" type="text" name="tag_swiggy" id="tag_swiggy" data-role="tagsinput" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group ">
                                                <?= lang("Tags for Food Panda ", "Tags") ?> <small class="text-info">(*Use comma for multiple tags)</small><br/> 
                          -                      <input class="form-control" type="text" name="tag_foodpanda" id="tag_foodpanda" data-role="tagsinput" />
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group ">
                                                <?= lang("Tags for Uber Eats ", "Tags") ?> <small class="text-info">(*Use comma for multiple tags)</small><br/> 
                                                <input class="form-control" type="text" name="tag_ubereats" id="tag_ubereats" data-role="tagsinput" />
                                            </div>
                                        </div>
                                    </div>                          

                                </fieldset>    
                                <br>
                            </div>   
                        <?php } ?> 
                        <!---- End Urbanpiper ----->
                    <?php } ?>
                    <!--- End Restaurant Type POS  ----> 
                </div>
                
                <!-----vcvcvcvbcv-->
                <div class="col-md-5">
                    <div class="standard">

                        <div id="attrs"></div>

                        <div class="form-group">
                            <input type="checkbox" class="checkbox" name="attributes"
                                   id="attributes" <?= $this->input->post('attributes') || $product_options ? 'checked="checked"' : ''; ?>><label
                                   for="attributes"
                                   class="padding05"><?= lang('product_has_attributes'); ?></label> <br/><span class="text-info">Ex. Sizes, Colors, Models or Weight</span>
                        </div>
                        <div class="well well-sm" id="attr-con"
                             style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>">
                            <div class="form-group" id="ui" style="margin-bottom: 0;">
                                <div class="input-group">
                                    <?php echo form_input('attributesInput', '', 'class="form-control select-tags" id="attributesInput" placeholder="' . $this->lang->line("enter_attributes") . '"'); ?>
                                    <div class="input-group-addon" style="padding: 2px 5px;">
                                        <a href="#" id="addAttributes">
                                            <i class="fa fa-2x fa-plus-circle" id="addIcon"></i>
                                        </a>
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div class="table-responsive">
                                <table id="attrTable" class="table table-bordered table-condensed table-striped"
                                       style="<?= $this->input->post('attributes') || $product_options ? '' : 'display:none;'; ?>margin-bottom: 0; margin-top: 10px;">
                                    <thead>
                                        <tr class="active">
                                            <th><?= lang('name') ?></th>
                                            <!--<th><?= lang('warehouse') ?></th>-->
                                            <!--<th><?= lang('quantity') ?></th>-->
                                            <th><?= lang('Cost') ?></th>
                                            <th><?= lang('Price') ?></th>
 <?php if ($Settings->pos_type == 'restaurant') { ?>               
                                            <th><?= lang('Urbanpier_Price_Addition') ?></th>
                                            <?php } ?>
                                            <th><?= 'Unit Quantity'?></th>
                                            <th><?= 'Unit Weight'?></th>
                                            <th><i class="fa fa-times attr-remove-all"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody><?php
                                        if ($this->input->post('attributes')) {
                                            $a = sizeof($_POST['attr_name']);
                                            for ($r = 0; $r <= $a; $r++) {
                                                if (isset($_POST['attr_name'][$r]) && (isset($_POST['attr_warehouse'][$r]) || isset($_POST['attr_quantity'][$r]))) {
                                                    echo '<tr class="attr">'
                                                    . '<td><input type="hidden" class="attr_name" name="attr_name[]" value="' . $_POST['attr_name'][$r] . '"><span>' . $_POST['attr_name'][$r] . '</span></td>'
                                                    . '<!--<td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $_POST['attr_warehouse'][$r] . '"><input type="hidden" class="attr_wh_name" name="attr_wh_name[]" value="' . $_POST['attr_wh_name'][$r] . '"><span>' . $_POST['attr_wh_name'][$r] . '</span></td>-->'
                                                    . '<!--<td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $_POST['attr_quantity'][$r] . '"><span>' . $_POST['attr_quantity'][$r] . '</span></td>-->'
                                                    . '<td class="cost text-right"><input type="hidden" name="attr_cost[]" value="' . $_POST['attr_cost'][$r] . '"><span>' . $_POST['attr_cost'][$r] . '</span></span></td>'
                                                    . '<td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $_POST['attr_price'][$r] . '"><span>' . $_POST['attr_price'][$r] . '</span></span></td>';
if ($Settings->pos_type == 'restaurant') {  
                                                    echo '<td class="upprice text-right"><input type="hidden" name="attr_upprice[]" value="' . $_POST['attr_upprice'][$r] . '"><span>' . $_POST['attr_upprice'][$r] . '</span></span></td>';
                                                   }  
                                                    echo  '<td class="price text-right"><input type="hidden" name="attr_unit_quantity[]" value="' . $_POST['attr_unit_quantity'][$r] . '"><span>' . $_POST['attr_unit_quantity'][$r] . '</span></span></td>'
                                                    . '<td class="price text-right"><input type="hidden" name="attr_unit_weight[]" value="' . $_POST['attr_unit_weight'][$r] . '"><span>' . $_POST['attr_unit_weight'][$r] . '</span></span></td>'
                                                    . '<td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
                                                }
                                            }
                                        } elseif ($product_options) {
                                            foreach ($product_options as $option) {
                                                echo '<tr class="attr">'
                                                . '<td><input type="hidden" class="attr_name" name="attr_name[]" value="' . $option->name . '"><span>' . $option->name . '</span></td>'
                                                . '<!--<td class="code text-center"><input type="hidden" name="attr_warehouse[]" value="' . $option->warehouse_id . '"><input type="hidden" class="attr_wh_name" name="attr_wh_name[]" value="' . $option->wh_name . '"><span>' . $option->wh_name . '</span></td>-->'
                                                . '<!--<td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="' . $this->sma->formatQuantity($option->wh_qty) . '"><span>' . $this->sma->formatQuantity($option->wh_qty) . '</span></td>-->'
                                                . '<td class="cost text-right"><input type="hidden" name="attr_cost[]" value="' . $this->sma->formatMoney($option->cost) . '"><span>' . $this->sma->formatMoney($option->cost) . '</span></span></td>'
                                                . '<td class="price text-right"><input type="hidden" name="attr_price[]" value="' . $this->sma->formatMoney($option->price) . '"><span>' . $this->sma->formatMoney($option->price) . '</span></span></td>';
if ($Settings->pos_type == 'restaurant'){ 
                                                 echo  '<td class="upprice text-right"><input type="hidden" name="attr_upprice[]" value="' . $this->sma->formatMoney($option->up_price) . '"><span>' . $this->sma->formatMoney($option->up_price) . '</span></span></td>';
                                               } 
                                                echo '<td class="unit_quantity text-center"><input type="hidden" name="attr_unit_quantity[]" value="' . ($option->wh_unit_qty) . '"><span>' . ($option->wh_unit_qty) . '</span></td>'
                                                . '<td class="unit_weight text-center"><input type="hidden" name="attr_unit_weight[]" value="' . ($option->wh_unit_weight) . '"><span>' . ($option->wh_unit_weight) . '</span></td>'
                                                . '<td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>';
                                            }
                                        }
                                        ?></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="<?= $product ? 'text-warning' : '' ?>" style="display:none;">
                            <strong><?= lang("warehouse_quantity") ?></strong><br>
                            <?php
                            $permisions_werehouse = explode(",", $this->session->userdata('warehouse_id'));
                            if (!empty($warehouses)) {
                                if ($product) {
                                    echo '<div class="row"><div class="col-md-12"><div class="well"><div id="show_wh_edit">';
                                    if (!empty($warehouses_products)) {
                                        echo '<div style="display:none;">';
                                        foreach ($warehouses_products as $wh_pr) {
                                            echo '<span class="bold text-info">' . $wh_pr->name . ': <span class="padding05" id="rwh_qty_' . $wh_pr->id . '">' . $this->sma->formatQuantity($wh_pr->quantity) . '</span>' . ($wh_pr->rack ? ' (<span class="padding05" id="rrack_' . $wh_pr->id . '">' . $wh_pr->rack . '</span>)' : '') . '</span><br>';
                                        }
                                        echo '</div>';
                                    }
                                    foreach ($warehouses as $warehouse) {

                                        if ($Owner || $Admin) {
                                            //$whs[$warehouse->id] = $warehouse->name;
                                            echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : (isset($warehouse->quantity) ? $warehouse->quantity : '')), 'class="form-control wh" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
                                            if ($Settings->racks) {
                                                echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : (isset($warehouse->rack) ? $warehouse->rack : '')), 'class="form-control wh" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
                                            }
                                            echo '</div>';
                                        } elseif (in_array($warehouse->id, $permisions_werehouse)) {
                                            echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : (isset($warehouse->quantity) ? $warehouse->quantity : '')), 'class="form-control wh" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
                                            if ($Settings->racks) {
                                                echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : (isset($warehouse->rack) ? $warehouse->rack : '')), 'class="form-control wh" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
                                            }
                                            echo '</div>';
                                        }
                                    }
                                    echo '</div><div class="clearfix"></div></div></div></div>';
                                } else {
                                    echo '<div class="row"><div class="col-md-12"><div class="well">';
                                    foreach ($warehouses as $warehouse) {
                                        //$whs[$warehouse->id] = $warehouse->name;
                                        if ($Owner || $Admin) {
                                            echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : ''), 'class="form-control" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
                                            if ($Settings->racks) {
                                                echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : ''), 'class="form-control" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
                                            }
                                            echo '</div>';
                                        } elseif (in_array($warehouse->id, $permisions_werehouse)) {
                                            echo '<div class="col-md-6 col-sm-6 col-xs-6" style="padding-bottom:15px;">' . $warehouse->name . '<br><div class="form-group">' . form_hidden('wh_' . $warehouse->id, $warehouse->id) . form_input('wh_qty_' . $warehouse->id, (isset($_POST['wh_qty_' . $warehouse->id]) ? $_POST['wh_qty_' . $warehouse->id] : (isset($warehouse->quantity) ? $warehouse->quantity : '')), 'class="form-control wh" id="wh_qty_' . $warehouse->id . '" placeholder="' . lang('quantity') . '"') . '</div>';
                                            if ($Settings->racks) {
                                                echo '<div class="form-group">' . form_input('rack_' . $warehouse->id, (isset($_POST['rack_' . $warehouse->id]) ? $_POST['rack_' . $warehouse->id] : (isset($warehouse->rack) ? $warehouse->rack : '')), 'class="form-control wh" id="rack_' . $warehouse->id . '" placeholder="' . lang('rack') . '"') . '</div>';
                                            }
                                            echo '</div>';
                                        }
                                    }
                                    echo '<div class="clearfix"></div></div></div></div>';
                                }
                            }
                            ?>
                        </div>
                        <div class="clearfix"></div>

                    </div>
                    <div class="combo" style="display:none;">

                        <div class="form-group">
                            <?= lang("add_product", "add_item") . ' (' . lang('not_with_variants') . ')'; ?>
                            <?php echo form_input('add_item', '', 'class="form-control ttip" id="add_item" data-placement="top" data-trigger="focus" data-bv-notEmpty-message="' . lang('please_add_items_below') . '" placeholder="' . $this->lang->line("add_item") . '"'); ?>
                        </div>
                        <div class="control-group table-group">
                            <label class="table-label" for="combo"><?= lang("combo_products"); ?></label>
                            <div class="controls table-controls">
                                <table id="prTable"
                                       class="table items table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-md-5 col-sm-5 col-xs-5"><?= lang("product_name") . " (" . $this->lang->line("product_code") . ")"; ?></th>
                                            <th class="col-md-2 col-sm-2 col-xs-2"><?= lang("quantity"); ?></th>
                                            <th class="col-md-3 col-sm-3 col-xs-3"><?= lang("unit_price"); ?></th>
                                            <th class="col-md-3 col-sm-3 col-xs-3"><?= lang("Price"); ?></th>
                                            <th class="col-md-1 col-sm-1 col-xs-1 text-center">
                                                <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="digital" style="display:none;">
                        <div class="form-group digital">
                            <?= lang("digital_file", "digital_file") ?>
                            <input id="digital_file" type="file" data-browse-label="<?= lang('browse'); ?>" name="digital_file" data-show-upload="false"
                                   data-show-preview="false" class="form-control file">
                        </div>
                    </div>


<!--                    <div class="form-group standard">
                        <div class="form-group">
                            < ?= lang("supplier", "supplier") ?>
                            <button type="button" class="btn btn-primary btn-xs" id="addSupplier"><i class="fa fa-plus"></i>
                            </button>
                        </div>-->
                        <!--
                        <div class="col-xs-12">
                                <div class="form-group">
                        < ?php
                        //echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control ' . ($product ? '' : 'suppliers') . '" id="' . ($product && !empty($product->supplier1) ? 'supplier1' : 'supplier') . '" placeholder="' . lang("select") . ' ' . lang("supplier") . '" style="width:100%;"');
                        ?>
                                </div>
                        </div>
                        -->
<!--                        <div class="row" id="supplierrow_1">  supplier_con
                            <div>
                                <div class="col-xs-11">
                                    <div class="form-group">
                                        < ?php
                                        echo form_input('supplier', (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'class="form-control ' . ($product ? '' : 'suppliers') . '" id="' . ($product && !empty($product->supplier1) ? 'supplier1' : 'supplier') . '" placeholder="' . lang("select") . ' ' . lang("supplier") . '" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-xs" >
                                    <i class="fa fa-times deleteSupplier"  id="1"  style="cursor:pointer;"></i>
                                </button>
                            </div>    
                            <div class="col-xs-6">
                                <div class="form-group">
                                    < ?= form_input('supplier_part_no', (isset($_POST['supplier_part_no']) ? $_POST['supplier_part_no'] : ""), 'class="form-control tip" id="supplier_part_no" placeholder="' . lang('supplier_part_no') . '"'); ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    < ?= form_input('supplier_price', (isset($_POST['supplier_price']) ? $_POST['supplier_price'] : ""), 'class="form-control tip" id="supplier_price" placeholder="' . lang('supplier_price') . '"'); ?>
                                </div>
                            </div>
                        </div>
                        <div id="ex-suppliers"></div>
                    </div>-->


                </div>

                <div class="col-md-12">
                    <?php                    
                    $active_cf = FALSE;
                    for($i=1; $i<=6; $i++){
                        if($custome_fields->{"cf$i"}){
                            $active_cf = TRUE;
                            break;
                        }
                    }                     
                    ?>
                    <div class="form-group">
                        <input name="cf" type="checkbox" class="checkbox" id="extras" value="1" <?= ($active_cf) ? 'checked="checked" disabled="disabled"' : '' ?> />
                        <label for="extras" class="padding05"><?= lang('custom_fields') ?></label>
                    </div>                    
                    <div class="row" id="extras-con" style="<?= ($active_cf) ? 'display: block;' : 'display: none;' ?>">
                         
                        <div class="col-md-4">                             
                            <div class="form-group all">                         
                                <?php echo (!empty($custome_fields->cf1) ? lang($custome_fields->cf1, 'pcf1') : lang('pcf1', 'pcf1')) ?>                                
                                <?php                        
                                if ($custome_fields->cf1_input_type == 'list_box' && $custome_fields->cf1_input_options != '') {                            
                                    echo form_dropdown('cf1', (json_decode($custome_fields->cf1_input_options, TRUE)) , '', 'class="form-control tip" id="cf1"'. ((strpos($custome_fields->cf1, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf1', '', 'class="form-control" id="cf1" '. ((strpos($custome_fields->cf1, '*')) ? ' required="required" ' : '')); 
                                }
                                ?>                        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group all">
                                <?php echo (!empty($custome_fields->cf2) ? lang($custome_fields->cf2, 'pcf2') : lang('pcf2', 'pcf2')) ?> 
                               <?php                        
                                if ($custome_fields->cf2_input_type == 'list_box' && $custome_fields->cf2_input_options != '') {                            
                                    echo form_dropdown('cf2', (json_decode($custome_fields->cf2_input_options, TRUE)) , '', 'class="form-control tip" id="cf2"'. ((strpos($custome_fields->cf2, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf2', '', 'class="form-control" id="cf2" '. ((strpos($custome_fields->cf2, '*')) ? ' required="required" ' : ''));
                                }  ?>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?php  echo (!empty($custome_fields->cf3) ? lang($custome_fields->cf3, 'pcf3') : lang('pcf3', 'pcf3')) ?>
                                <?php                        
                                if ($custome_fields->cf3_input_type == 'list_box' && $custome_fields->cf3_input_options != '') {                            
                                    echo form_dropdown('cf3', (json_decode($custome_fields->cf3_input_options, TRUE)) , '', 'class="form-control tip" id="cf3"'. ((strpos($custome_fields->cf3, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf3', '', 'class="form-control" id="cf3" '. ((strpos($custome_fields->cf3, '*')) ? ' required="required" ' : '')); 
                                }    
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?php echo (!empty($custome_fields->cf4) ? lang($custome_fields->cf4, 'pcf4') : lang('pcf4', 'pcf4')) ?>
                                <?php                        
                                if ($custome_fields->cf4_input_type == 'list_box' && $custome_fields->cf4_input_options != '') {                            
                                    echo form_dropdown('cf4', (json_decode($custome_fields->cf4_input_options, TRUE)) , '', 'class="form-control tip" id="cf4"'. ((strpos($custome_fields->cf4, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf4', '', 'class="form-control" id="cf4"'. ((strpos($custome_fields->cf4, '*')) ? ' required="required" ' : '')); 
                                }   
                                ?> 
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?php echo (!empty($custome_fields->cf5) ? lang($custome_fields->cf5, 'pcf5') : lang('pcf5', 'pcf5')) ?>
                                <?php                        
                                if ($custome_fields->cf5_input_type == 'list_box' && $custome_fields->cf5_input_options != '') {                            
                                    echo form_dropdown('cf5', (json_decode($custome_fields->cf5_input_options, TRUE)) , '', 'class="form-control tip" id="cf5"'. ((strpos($custome_fields->cf5, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf5', '', 'class="form-control" id="cf5"'. ((strpos($custome_fields->cf5, '*')) ? ' required="required" ' : '')); 
                                }    
                                ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group all">
                                <?php echo (!empty($custome_fields->cf6) ? lang($custome_fields->cf6, 'pcf6') : lang('pcf6', 'pcf6')) ?>
                                <?php                        
                                if ($custome_fields->cf6_input_type == 'list_box' && $custome_fields->cf6_input_options != '') {                            
                                    echo form_dropdown('cf6', (json_decode($custome_fields->cf6_input_options, TRUE)) , '', 'class="form-control tip" id="cf6"'. ((strpos($custome_fields->cf6, '*')) ? ' required="required" ' : ''));
                                } else {
                                    echo form_input('cf6', '', 'class="form-control" id="cf6"'. ((strpos($custome_fields->cf6, '*')) ? ' required="required" ' : '')); 
                                }   
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group all">
                        <?= lang("product_details", "product_details") ?>
                        <?= form_textarea('product_details', (isset($_POST['product_details']) ? $_POST['product_details'] : ($product ? $product->product_details : '')), 'class="form-control" id="details"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_details_for_invoice", "details") ?>
                        <?= form_textarea('details', (isset($_POST['details']) ? $_POST['details'] : ($product ? $product->details : '')), 'class="form-control" id="details"'); ?>
                    </div>

                    <div class="form-group">
                        <?php echo form_submit('add_product', $this->lang->line("add_product"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
<?php
if ($combo_items) {
    foreach ($combo_items as $item) {
        //echo 'ietms['.$item->id.'] = '.$item.';';
        if ($item->code) {
            echo 'add_product_item(' . json_encode($item) . ');';
        }
    }
}
?>
<?= isset($_POST['cf']) ? '$("#extras").iCheck("check");' : '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

<?= isset($_POST['promotion']) ? '$("#promotion").iCheck("check");' : '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
            $('#promo_price').removeAttr('required');
            $('#add_product').removeAttr('disabled');
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });
        //$('#cost').removeAttr('required');
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'standard') {
                $('.standard').slideUp();
                $('#cost').attr('required', 'required');
                $('#track_quantity').iCheck('uncheck');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
            } else {
                $('.standard').slideDown();
                $('#track_quantity').iCheck('check');
                $('#cost').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#digital_file').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'digital_file');
            } else {
                $('.digital').slideDown();
                $('#digital_file').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'digital_file');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
        });

        var t = $('#type').val();
        if (t !== 'standard') {
            $('.standard').slideUp();
            $('#cost').attr('required', 'required');
            $('#track_quantity').iCheck('uncheck');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
        } else {
            $('.standard').slideDown();
            $('#track_quantity').iCheck('check');
            $('#cost').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
            $('#digital_file').removeAttr('required');
            $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'digital_file');
        } else {
            $('.digital').slideDown();
            $('#digital_file').attr('required', 'required');
            $('form[data-toggle="validator"]').bootstrapValidator('addField', 'digital_file');
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }

        $("#add_item").autocomplete({
            source: '<?= site_url('products/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                } else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                } else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
                    if (row) {
                        $(this).val('');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });

<?php
if ($this->input->post('type') == 'combo') {
    $c = sizeof($_POST['combo_item_code']);
    for ($r = 0; $r <= $c; $r++) {
        if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
            $items[] = array('id' => $_POST['combo_item_id'][$r], 'name' => $_POST['combo_item_name'][$r], 'code' => $_POST['combo_item_code'][$r], 'qty' => $_POST['combo_item_quantity'][$r], 'price' => $_POST['combo_item_price'][$r]);
        }
    }
    echo '
            var ci = ' . json_encode($items) . ';
            $.each(ci, function() { add_product_item(this); });
            ';
}
?>
        function add_product_item(item) {
            if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
            } else {
                items[item_id] = item;
            }
            var pp = 0;
            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '" data-item-id="' + row_no + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.name + ' (' + this.code + ')</span></td>';
                tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatDecimal(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input class="form-control text-center rtprice" name="combo_item_total_price[]" type="text" value="' + (formatDecimal(this.price) * formatDecimal(this.qty)) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_total_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
                pp += formatDecimal(parseFloat(this.price) * parseFloat(this.qty));
            });
            $('.item_' + item_id).addClass('warning');
            $('#price').val(pp);
            $('#cost').val(pp);
            return true;
        }

        function calculate_price() {

            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            var row_total = 0
            $.each(rows, function () {
                row_total = formatDecimal(parseFloat($(this).find('.rprice').val()) * parseFloat($(this).find('.rquantity').val()));
                $(this).find('.rtprice').val(row_total);
                //console.log(this);
                //alert(row_total);
                pp += row_total;
            });
            //console.log(pp);
            $('#price').val(pp);
            return true;
        }

        $(document).on('change textchange', '.rquantity, .rprice', function () {
            calculate_price();
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
            calculate_price();
        });
        var su = 2;
        /*
         $('#addSupplier').click(function () {
         if (su <= 5) {
         $('#supplier_1').select2('destroy');
         var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
         $('#ex-suppliers').append(html);
         var sup = $('#supplier_' + su);
         suppliers(sup);
         su++;
         } else {
         bootbox.alert('<?= lang('max_reached') ?>');
         return false;
         }
         });*/

        $('#addSupplier').click(function () {//onClick="delete_supplier(' + su + ')"
            if (su <= 5) {
                //$('#supplier_1').select2('destroy');//style="width:100%;display: block !important;"
                var html = '<div style="clear:both;height:5px;" ></div><div class="row" id="supplierrow_' + su + '"><div><div class="col-xs-11"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>"  /></div></div><div><button type="button" class="btn btn-primary btn-xs" ><i class="fa fa-times deleteSupplier"  id="' + su + '"  style="cursor:pointer;"></i></button></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });


        $(document).on('click', '.deleteSupplier', function () {
            var id = $(this).attr('id');
            console.log(id);
            su--;
            $(this).closest('#supplierrow_' + id).remove();
        });

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);
                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?= json_encode($vars); ?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            var wh_arr = [];
            var attr_arr = [];
            if ($.trim($('#attrTable tbody').html()) != '') {
//                $.each($(".attr_wh_name"), function (index, ele) {
//                    wh_arr.push(ele.value);
//
//                });
                $.each($(".attr_name"), function (index, ele) {
                    attr_arr.push(ele.value);

                });
            }
            var wh_arr_unique = $.unique(wh_arr);
            var attr_arr_unique = $.unique(attr_arr);
            for (var i in attrs) {
                if (attrs[i] !== '') {
<?php
//if (!empty($warehouses)) {
//    foreach ($warehouses as $warehouse) {
        ?>
//                            var wh_id = '< ?php echo $warehouse->name; ?>';
//                            //console.log(wh_id);
//
//                            if (($.inArray(attrs[i], attr_arr_unique) >= 0) && ($.inArray(wh_id, wh_arr_unique) >= 0)) {
//                                //alert('found');
//                            } else {
                        <?php //echo '$(\'#attrTable\').show().append(\'<tr class="attr"><td><input type="hidden" class="attr_name" name="attr_name[]" value="\' + attrs[i] + \'"><span>\' + attrs[i] + \'</span></td><td class="code text-center"><input type="hidden" class="attr_warehouse" name="attr_warehouse[]" value="' . $warehouse->id . '"><input type="hidden" class="attr_wh_name" name="attr_wh_name[]" value="' . $warehouse->name . '"><span>' . $warehouse->name . '</span></td><!--<td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value=""><span>0</span></td>--><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_quantity[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>\');'; ?>
//                        }
        <?php
//    }
//} else {
    ?>
                     //   $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" class="attr_name" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden"  class="attr_warehouse" name="attr_warehouse[]" value=""><input type="hidden" class="attr_wh_name" name="attr_wh_name[]" value=""><span></span></td><!--<td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value=""><span></span></td>--><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_quantity[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                //$('#attrTable').show().append('<tr class="attr"><td><input type="hidden" class="attr_name" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="cost text-right"><input type="hidden" name="attr_cost[]" value="0"><span>0</span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_quantity[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_weight[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');

<?php  if ($Settings->pos_type == 'restaurant') { ?>
                    

 $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" class="attr_name" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="cost text-right"><input type="hidden" name="attr_cost[]" value="0"><span>0</span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="upprice text-right"><input type="hidden" name="attr_upprice[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_quantity[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_weight[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php } else { ?>
                      $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" class="attr_name" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="cost text-right"><input type="hidden" name="attr_cost[]" value="0"><span>0</span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_quantity[]" value="0"><span>0</span></span></td><td class="unit_quantity text-right"><input type="hidden" name="attr_unit_weight[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                    <?php } ?>  


<?php //} ?>
                }
            }
        });
//$('#attributesInput').on('select2-blur', function(){
//    $('#addAttributes').click();
//});
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
//            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
//            $('#aquantity').val(row.children().eq(2).find('input').val());
            $('#acost').val(row.children().eq(1).find('span').text());

           $('#aprice').val(row.children().eq(2).find('span').text());

         <?php if ($Settings->pos_type == 'restaurant') { ?>
                $('#aupprice').val(row.children().eq(3).find('span').text());
                $('#aunit_quantity').val(row.children().eq(4).find('input').val());
                $('#aunit_weight').val(row.children().eq(5).find('input').val());
            <?php }else{ ?>
             $('#aunit_quantity').val(row.children().eq(3).find('input').val());
             $('#aunit_weight').val(row.children().eq(4).find('input').val());
            <?php } ?>

            //$('#aunit_quantity').val(row.children().eq(3).find('input').val());
            
            $('#aModal').appendTo('body').modal('show');
        });

        $('#aModal').on('shown.bs.modal', function () {
            $('#aquantity').focus();
            $(this).keypress(function (e) {
                if (e.which == 13) {
                    $('#updateAttr').click();
                }
            });
        });
        $(document).on('click', '#updateAttr', function () {
//            var wh = $('#awarehouse').val(), wh_name;
//            $.each(warehouses, function () {
//                if (this.id == wh) {
//                    wh_name = this.name;
//                }
//            });
           // row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" class="attr_wh_name" value="' + wh_name + '"><span>' + wh_name + '</span>');
            //row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + $('#aquantity').val() + '"><span>' + decimalFormat($('#aquantity').val()) + '</span>');
            //row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');
            row.children().eq(1).html('<input type="hidden" name="attr_cost[]" value="' + $('#acost').val() + '"><span>' + $('#acost').val() + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + $('#aprice').val() + '</span>');
            
<?php if ($Settings->pos_type == 'restaurant') { ?>
                row.children().eq(3).html('<input type="hidden" name="attr_upprice[]" value="' + $('#aupprice').val() + '"><span>' + $('#aupprice').val() + '</span>');
                row.children().eq(4).html('<input type="hidden" name="attr_unit_quantity[]" value="' + $('#aunit_quantity').val() + '"><span>' + decimalFormat($('#aunit_quantity').val()) + '</span>');
  row.children().eq(5).html('<input type="hidden" name="attr_unit_weight[]" value="' + $('#aunit_weight').val() + '"><span>' + ($('#aunit_weight').val()) + '</span>');
            <?php }else{ ?>
                row.children().eq(3).html('<input type="hidden" name="attr_unit_quantity[]" value="' + $('#aunit_quantity').val() + '"><span>' + decimalFormat($('#aunit_quantity').val()) + '</span>');
  row.children().eq(4).html('<input type="hidden" name="attr_unit_weight[]" value="' + $('#aunit_weight').val() + '"><span>' + ($('#aunit_weight').val()) + '</span>');
            <?php } ?>  
    ///row.children().eq(3).html('<input type="hidden" name="attr_unit_quantity[]" value="' + $('#aunit_quantity').val() + '"><span>' + ($('#aunit_quantity').val()) + '</span>');
          
            $('#aModal').modal('hide');

        });
    });

<?php if ($product) { ?>
        $(document).ready(function () {
            var t = "<?= $product->type ?>";
            if (t !== 'standard') {
                $('.standard').slideUp();
                $('#cost').attr('required', 'required');
                $('#track_quantity').iCheck('uncheck');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'cost');
            } else {
                $('.standard').slideDown();
                $('#track_quantity').iCheck('check');
                $('#cost').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'cost');
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
                $('#digital_file').removeAttr('required');
                $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'digital_file');
            } else {
                $('.digital').slideDown();
                $('#digital_file').attr('required', 'required');
                $('form[data-toggle="validator"]').bootstrapValidator('addField', 'digital_file');
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
                //$('#add_item').removeAttr('required');
                //$('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
            } else {
                $('.combo').slideDown();
                //$('#add_item').attr('required', 'required');
                //$('form[data-toggle="validator"]').bootstrapValidator('addField', 'add_item');
            }
            $("#code").parent('.form-group').addClass("has-error");
            $("#code").focus();
            $("#product_image").parent('.form-group').addClass("text-warning");
            $("#images").parent('.form-group').addClass("text-warning");
            $.ajax({
                type: "get", async: false,
                url: "<?= site_url('products/getSubCategories') ?>/" + <?= $product->category_id ?>,
                dataType: "json",
                success: function (scdata) {
                    if (scdata != null) {
                        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                            placeholder: "<?= lang('select_category_to_load') ?>",
                            data: scdata
                        });
                    } else {
                        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                            placeholder: "<?= lang('no_subcategory') ?>",
                            data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                        });
                    }
                }
            });
    <?php if ($product->supplier1) { ?>
                select_supplier('supplier1', "<?= $product->supplier1; ?>");
                $('#supplier_price').val("<?= $product->supplier1price == 0 ? '' : $this->sma->formatDecimal($product->supplier1price); ?>");
    <?php } ?>
    <?php if ($product->supplier2) { ?>
                $('#addSupplier').click();
                select_supplier('supplier_2', "<?= $product->supplier2; ?>");
                $('#supplier_2_price').val("<?= $product->supplier2price == 0 ? '' : $this->sma->formatDecimal($product->supplier2price); ?>");
    <?php } ?>
    <?php if ($product->supplier3) { ?>
                $('#addSupplier').click();
                select_supplier('supplier_3', "<?= $product->supplier3; ?>");
                $('#supplier_3_price').val("<?= $product->supplier3price == 0 ? '' : $this->sma->formatDecimal($product->supplier3price); ?>");
    <?php } ?>
    <?php if ($product->supplier4) { ?>
                $('#addSupplier').click();
                select_supplier('supplier_4', "<?= $product->supplier4; ?>");
                $('#supplier_4_price').val("<?= $product->supplier4price == 0 ? '' : $this->sma->formatDecimal($product->supplier4price); ?>");
    <?php } ?>
    <?php if ($product->supplier5) { ?>
                $('#addSupplier').click();
                select_supplier('supplier_5', "<?= $product->supplier5; ?>");
                $('#supplier_5_price').val("<?= $product->supplier5price == 0 ? '' : $this->sma->formatDecimal($product->supplier5price); ?>");
    <?php } ?>
            function select_supplier(id, v) {
                $('#' + id).val(v).select2({
                    minimumInputLength: 1,
                    data: [],
                    initSelection: function (element, callback) {
                        $.ajax({
                            type: "get", async: false,
                            url: "<?= site_url('suppliers/getSupplier') ?>/" + $(element).val(),
                            dataType: "json",
                            success: function (data) {
                                callback(data[0]);
                            }
                        });
                    },
                    ajax: {
                        url: site.base_url + "suppliers/suggestions",
                        dataType: 'json',
                        quietMillis: 15,
                        data: function (term, page) {
                            return {
                                term: term,
                                limit: 10
                            };
                        },
                        results: function (data, page) {
                            if (data.results != null) {
                                return {results: data.results};
                            } else {
                                return {results: [{id: '', text: 'No Match Found'}]};
                            }
                        }
                    }
                });//.select2("val", "<?= $product->supplier1; ?>");
            }

            var whs = $('.wh');
            $.each(whs, function () {
                $(this).val($('#r' + $(this).attr('id')).text());
            });
        });
<?php } ?>
    $(document).ready(function () {

       <?php if($product){ ?> 
            productunit('<?= $product->unit ?>'); 
        <?php } ?> 


        $('#unit').change(function (e) {
            var v = $(this).val();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('products/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name + ' (' + this.code + ')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name + ' (' + this.code + ')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        });
    });


     function productunit(v){
       
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('products/getSubUnits') ?>/" + v,
                    dataType: "json",
                    success: function (data) {
                        $('#default_sale_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $('#default_purchase_unit').select2("destroy").empty().select2({minimumResultsForSearch: 7});
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.name + ' (' + this.code + ')'}).appendTo($('#default_sale_unit'));
                            $("<option />", {value: this.id, text: this.name + ' (' + this.code + ')'}).appendTo($('#default_purchase_unit'));
                        });
                        $('#default_sale_unit').select2('val', v);
                        $('#default_purchase_unit').select2('val', v);
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                    }
                });
            } else {
                $('#default_sale_unit').select2("destroy").empty();
                $('#default_purchase_unit').select2("destroy").empty();
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_sale_unit'));
                $("<option />", {value: '', text: '<?= lang('select_unit_first') ?>'}).appendTo($('#default_purchase_unit'));
                $('#default_sale_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
                $('#default_purchase_unit').select2({minimumResultsForSearch: 7}).select2('val', '');
            }
        
    }
</script>

<div class="modal" id="aModal" tabindex="-1" role="dialog" aria-labelledby="aModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                        <i class="fa fa-2x">&times;</i></span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="aModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
<!--                    <div class="form-group">
                        <label for="awarehouse" class="col-sm-4 control-label"><?= lang('warehouse') ?></label>
                        <div class="col-sm-8">
                            < ?php
                            $wh[''] = '';
                            foreach ($warehouses as $warehouse) {
                                $wh[$warehouse->id] = $warehouse->name;
                            }
                            echo form_dropdown('warehouse', $wh, '', 'id="awarehouse" class="form-control"');
                            ?>
                        </div>
                    </div>-->
                    <!--<div class="form-group">
                         <label for="aquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>
                         <div class="col-sm-8">
                             <input type="text" class="form-control" id="aquantity" onkeypress="return isNumberKeyQua(event)">
                             <span id="errorq" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                         </div>
                     </div>-->
                    <div class="form-group">
                        <label for="acost" class="col-sm-4 control-label"><?= lang('Cost_Addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="acost" onkeypress="return isNumberKeyPrice(event)">
                            <span id="errorc" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="aprice" class="col-sm-4 control-label"><?= lang('price_addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aprice" onkeypress="return isNumberKeyPrice(event)">
                            <span id="errorp" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                        </div>
                    </div>
<?php if ($Settings->pos_type == 'restaurant') { ?>
                     <div class="form-group">
                        <label for="aupprice" class="col-sm-4 control-label"><?= lang('Urbanpiper_Price_Addition') ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="aupprice" onkeypress="return isNumberKeyPrice(event)">
                            <span id="errorup" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="aunit_quantity" class="col-sm-4 control-label"><?= 'Unit Quantity'?></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="aunit_quantity" min="0" max="100" step="0.125"  value="1" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="aunit_weight" class="col-sm-4 control-label"><?= 'Unit Weight (In KG)'?></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" min="0" max="100" step="0.125" id="aunit_weight" >
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="updateAttr"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Urbanpiper --->

<script type="text/javascript">
    $('#urbanpiperitem').click(function () {
        let values = $(this).val();
        if (values == '1') {
            $('#urbanpipercontain').show();
        } else {
            $('#urbanpipercontain').hide();
        }
    });
    $(document.activeElement).filter(':select#category:focus').blur();

    function onlyAlphabets1(e, t) {
        var charCode = e.which ? e.which : e.keyCode
        var ret = (charCode == 32 || (charCode >= 97 && charCode <= 122) || (charCode >= 65 && charCode <= 90));
        document.getElementById("error2").style.display = ret ? "none" : "inline";
        return ret;
    }

    /****/
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            document.getElementById("error").style.display = "inline";
            return false;
        }
        document.getElementById("error").style.display = "none";
        return true;
    }

    function isNumberKeyQua(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            document.getElementById("errorq").style.display = "inline";
            return false;
        }
        document.getElementById("errorq").style.display = "none";
        return true;
    }

    function isNumberKeyPrice(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            document.getElementById("errorp").style.display = "inline";
            return false;
        }
        document.getElementById("errorp").style.display = "none";
        return true;
    }
</script> 


<!-- End Urbanpiper --->