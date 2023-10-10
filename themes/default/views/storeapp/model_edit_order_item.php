<div class="modal" id="modal_edit" style="width: 100%;">            
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= $itemData['product_name'] ?> (#<?= $itemData['product_code'] ?>)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">                                       
                                <label>Product Tax</label>
                                <select class="form-control select2bs4" name="edit_product_tax" id="edit_product_tax" style="width: 100%;"  onchange="cal_unit_price();">
                                    <?php
                                    if (is_array($taxes)) {
                                        foreach ($taxes as $tax) {

                                            $taxSelected = $itemData['tax_rate_id'] == $tax['id'] ? ' selected="selected" ' : '';
                                            echo '<option value="' . $tax['id'] . '" ' . $taxSelected . ' rate="' . $tax['rate'] . '" >' . $tax['name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- /.form-group -->
                            <div class="form-group">
                                <label>Unit Quantity</label>
                                <input type="text" name="edit_unit_quantity" id="edit_unit_quantity" value="<?= $itemData['unit_quantity'] ?>" class="form-control" onchange="cal_unit_price();"/>
                            </div>
                            <!-- /.form-group -->  
                            <div class="form-group">
                                <label>Unit Price</label>
                                <input type="text" name="edit_unit_price" id="edit_real_unit_price" value="<?= $itemData['real_unit_price'] ?>" class="form-control" onchange="cal_unit_price();"/>
                            </div>
                            <!-- /.form-group -->  
                            <div class="form-group">
                                <label>Discount (Rs. / %)</label>
                                <input type="text" name="edit_product_discount" id="edit_product_discount" value="<?= $itemData['discount'] ?>" class="form-control" onchange="cal_unit_price();"/>
                            </div>
                            <!-- /.form-group -->  
                        </div>
                        <!-- /.col -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tax Method</label>
                                <?php
                                $tax_method = '_' . $itemData['tax_method'];
                                $$tax_method = ' selected="selected" ';
                                ?>
                                <select name="edit_tax_method" id="edit_tax_method" class="form-control select2bs4" onchange="cal_unit_price();">                        
                                    <option value="1" <?= $_1 ?> >Exclusive</option>
                                    <option value="0" <?= $_0 ?> >Inclusive</option>
                                </select>
                            </div>
                            <!-- /.form-group -->
                            <div class="form-group">
                                <label>Units</label>
                                <select name="edit_unit" id="edit_unit" class="form-control select2bs4" name="unit" onchange="cal_unit_price();">
                                    <?php
                                    if (is_array($units)) {
                                        foreach ($units as $unit) {
                                            $unitSelected = $itemData['product_unit_id'] == $unit['id'] ? ' selected="selected" ' : '';
                                            echo '<option value="' . $unit['id'] . '" ' . $unitSelected . ' base_unit="' . $unit['base_unit'] . '" operator="' . $unit['operator'] . '" operation_value="' . $unit['operation_value'] . '">' . $unit['name'] . '</option>';
                                        }
                                    }
                                    ?>                                                             
                                </select>
                                <input type="hidden" id="previous_unit" value="<?= $itemData['product_unit_id'] ?>" />
                            </div>
                            <!-- /.form-group -->
                            <div class="form-group">
                                <label>Product Options</label>
                                <select name="edit_product_varient" id="edit_product_varient" class="form-control select2bs4"  onchange="cal_unit_price();">
                                    <?php
                                    if (is_array($varients)) {
                                        foreach ($varients as $varient) {
                                            $optionSelected = $itemData['option_id'] == $varient['id'] ? ' selected="selected" ' : '';
                                            echo '<option value="' . $varient['id'] . '" price="' . $varient['price'] . '" unit_qty="'. $varient['unit_quantity'] .'" ' . $optionSelected . '>' . $varient['name'] . ' (Rs. ' . number_format($varient['price']) . ')</option>';
                                        }
                                    } else {
                                        echo '<option value="0" selected="selected" price="0">NA</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="previous_varient" value="<?= $itemData['option_id'] ?>" />
                            </div>
                            <!-- /.form-group -->  
                            <div class="form-group">
                                <label>Note</label>
                                <input type="text" name="edit_note" id="edit_note" value="<?= $itemData['note'] ?>" class="form-control" />
                            </div>
                            <!-- /.form-group -->
                        </div>
                        <!-- /.col -->              
                    </div>              
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" style="background: #ccc;">
                                <tr>
                                    <td><label>Product Price</label></td>
                                    <td><label>Rs. <span id="show_unit_price"><?= $itemData['unit_price'] ?></span></label></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-md-12" id="action_message"></div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
            </div>
            <div class="modal-footer justify-content-between">
               
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" onclick="edit_order_items()" class="btn btn-primary">Save changes</button>

                <input type="hidden" id="edit_product_id" value="<?= $itemData['product_id'] ?>" />
                <input type="hidden" id="edit_order_id" value="<?= $itemData['order_id'] ?>" />
                <input type="hidden" id="edit_item_id" value="<?= $itemData['id'] ?>" />
                <input type="hidden" id="edit_unit_price" value="<?= $itemData['unit_price'] ?>" />
                <input type="hidden" id="edit_quantity" value="<?= $itemData['quantity'] ?>" />
                <input type="hidden" id="edit_item_weight" value="<?= $itemData['item_weight'] ?>" />
                <input type="hidden" id="edit_unit_weight" value="<?= (float)$itemData['item_weight']/(float)$itemData['unit_quantity'] ?>" />
                <input type="hidden" id="edit_variant_unit_quantity" value="<?= (float)$itemData['quantity']/(float)$itemData['unit_quantity'] ?>" />
            </div>
        </div>
        <!-- /.modal-content -->
    </div>            
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


