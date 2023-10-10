<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_warehouse'); ?></h4>
        </div>
        <?php
        $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/edit_warehouse/" . $id, $attrib);
        ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">           
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="code"><?php echo $this->lang->line("code"); ?></label>
                        <?php echo form_input('code', $warehouse->code, 'class="form-control" id="code" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="name"><?php echo $this->lang->line("name"); ?></label>
                        <?php echo form_input('name', $warehouse->name, 'class="form-control" id="name" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="country"><?php echo $this->lang->line("country"); ?></label>
                        <?php echo form_input('country', ($warehouse->country ? $warehouse->country : 'India'), 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="city"><?php echo $this->lang->line("city"); ?> Name</label>
                        <?php echo form_input('city', $warehouse->city, 'class="form-control" required="required" id="city"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="state"><?php echo $this->lang->line("state"); ?></label>
                        <?php
                        $state_list[''] = lang('select') . ' ' . lang('state');
                        foreach ($states as $state) {
                            $state_list[$state->id . '~' . $state->code] = $state->name;
                            if ($warehouse->state == $state->id) {
                                $selectSt = $state->id . '~' . $state->code;
                            }
                        }
                        echo form_dropdown('state', $state_list, $selectSt, 'class="form-control tip select" required="required" id="state" style="width:100%;"');
                        ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="price_group"><?php echo $this->lang->line("price_group"); ?></label>
                        <?php
                        $pgs[''] = lang('select') . ' ' . lang('price_group');
                        foreach ($price_groups as $price_group) {
                            $pgs[$price_group->id] = $price_group->name;
                        }
                        echo form_dropdown('price_group', $pgs, $warehouse->price_group_id, 'class="form-control tip select" id="price_group" style="width:100%;"');
                        ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="phone"><?php echo $this->lang->line("phone"); ?></label>
<?php echo form_input('phone', $warehouse->phone, 'class="form-control" id="phone"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="email"><?php echo $this->lang->line("email"); ?></label>
<?php echo form_input('email', $warehouse->email, 'class="form-control" id="email"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="postal_code">Pincode</label>
<?php echo form_input('postal_code', $warehouse->postal_code, 'class="form-control" required="required" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="state_code">State Code</label>
<?php echo form_input('state_code', $warehouse->state_code, 'class="form-control" readonly " id="state_code"'); ?>
                    </div>
                </div>
            </div>
            <div class="row"> 
                <div class="col-sm-12 form-group">
                    <label class="control-label" for="address"><?php echo $this->lang->line("address"); ?></label>
<?php echo form_textarea('address', $warehouse->address, 'class="form-control" id="address" required="required"'); ?>
                </div>
            </div>
            <div class="row"> 
                <div class="col-sm-12 form-group">
<?= lang("warehouse_map", "image") ?>
                    <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                           class="form-control file">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" form-group">
                     <label class="control-label" for="is_active"><?php echo $this->lang->line("Statue"); ?></label>
                         <?php
                         $status = ["0" => "Deactive", "1" => "Active"];

                         echo form_dropdown('is_active', $status, $warehouse->is_active, 'class="form-control tip select" id="is_active" style="width:100%;"');
                         ?>
                </div>
                <div class="col-sm-6 form-group">
                    <label class="control-label" for="is_disabled"><?php echo $this->lang->line("POS Status"); ?></label>
                    <?php
                    $posstatus = ["1" => "Disabled", "0" => "Enable"];

                    echo form_dropdown('is_disabled', $posstatus, $warehouse->is_disabled, 'class="form-control tip select" disabled="disabled" id="is_disabled" style="width:100%;"');
                    ?>
                </div>
            </div>
<?php if ($eshop_setting->active_multi_outlets) { ?>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="in_eshop"><?php echo $this->lang->line("In Eshop as Outlet"); ?></label>
                            <?php
                            $ineshop = ["0" => "Deactive", "1" => "Active"];

                            echo form_dropdown('in_eshop', $ineshop, $warehouse->in_eshop, 'class="form-control tip select" id="in_eshop" style="width:100%;"');
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="eshop_biller_id"><?php echo $this->lang->line("Select Outlet Biller"); ?></label>
                            <?php
                            if (is_array($billers)) {
                                foreach ($billers as $key => $biller) {
                                    $eshop_billers[$biller['id']] = $biller['name'];
                                }
                            }
                            echo form_dropdown('eshop_biller_id', $eshop_billers, $warehouse->eshop_biller_id, 'class="form-control tip select" id="eshop_biller_id" style="width:100%;"');
                            ?>
                        </div>
                    </div>

                </div>   


            <?php }//end if.   ?>
        </div>
        <div class="modal-footer">
    <?php echo form_submit('edit_warehouse', lang('edit_warehouse'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
<?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/modal.js"></script>