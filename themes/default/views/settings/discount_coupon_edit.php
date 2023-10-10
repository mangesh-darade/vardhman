<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    hr{height: 0.05em;
       background: #cccccc;}
    .select2-container-multi{height: auto;}
</style>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-gift"></i><?= lang('Edit Coupon'); ?></h2>
        <?php if (isset($pos->purchase_code) && !empty($pos->purchase_code) && $pos->purchase_code != 'purchase_code') { ?>
            <div class="box-icon">
                <ul class="btn-tasks">
                    <li class="dropdown"><a href="<?= site_url('pos/updates') ?>" class="toggle_down"><i
                                class="icon fa fa-upload"></i><span class="padding-right-10"><?= lang('updates'); ?></span></a>
                    </li>
                </ul>
            </div>
        <?php } ?>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php
                
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'pos_setting');
                echo form_open("system_settings/discount_coupon_edit", "id='offerform'"); //, $attrib
                ?>
                <input type='hidden' name="id" value="<?= $CouponInfo->id ?>"/>
                <fieldset class="scheduler-border">
                    <div class="form-group row">
                        <label class="col-sm-3" for="coupon_code"> <?= lang('Coupon_Code') ?>  </label>
                        <div class="col-sm-8">
                            <?= form_input('coupon_code', $CouponInfo->coupon_code, 'placeholder="Coupon Code" class="form-control" required = "required"  id="coupon_code" style="text-transform:uppercase"'); ?>
                            <span class="text-danger errormsg" id="errcoupon_code"></span>
                            <?php echo form_error('coupon_code'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3" for="coupon_descripion" > <?= lang('Coupon Descripion') ?>  </label>
                        <div class="col-sm-8">
                            <?= form_input('coupon_descripion',$CouponInfo->coupon_descripion, 'placeholder="Coupon Descripion" class="form-control" required = "required"  id="coupon_descripion" style="text-transform:uppercase"'); ?>
                            <span class="text-danger errormsg" id="errcoupon_descripion"></span>
                            <?php echo form_error('coupon_descripion'); ?>
                        </div>                        
                    </div>     

                    <div class="form-group row">
                        <label class="col-sm-3" for="Customer">Customer </label>
                        <div class="col-sm-8">
                            <?php
                            $cs[0] = 'All';
                            foreach ($customers as $Customer) {
                                $cs[$Customer->id] = $Customer->name;
                            }

                            echo form_dropdown('customer', $cs, $CouponInfo->customer_id, 'class="form-control select" id="Customer" style="width:100%;" required="required"');
                            ?>
                            <span class="text-danger errormsg" id="erroffer_for_customer"></span>
                             <?php echo form_error('customer'); ?>
                        </div>    
                    </div> 

                    <div class="form-group row">
                        <label class="col-sm-3" for="customer_group">Customer Groups </label>
                        <div class="col-sm-8">
                            <?php
                            $cgs[0] = 'All';
                            foreach ($customer_groups as $customer_group) {
                                $cgs[$customer_group->id] = $customer_group->name;
                            }
                            echo form_dropdown('customer_group', $cgs, $CouponInfo->customer_group_id, 'class="form-control select" id="customer_group" style="width:100%;" required="required"');
                            ?>
                            <span class="text-danger errormsg" id="erroffer_for_customer_group"></span>
                            <?php echo form_error('customer_group'); ?>
                        </div>    
                    </div> 


                    <div class="form-group row "  >
                        <label class="col-sm-3" > Minimum Cart Value </label>
                        <div class="col-sm-8">
                            <input type="text" value="<?= $CouponInfo->minimum_cart_amount ?>" maxlength="5" min="0"  name='minimum_cart_value' value="<?= (isset($_POST['minimum_cart_value'])) ? $_POST['minimum_cart_value'] : '' ?>" placeholder="Minimum Cart Value"  autocomplete="off" class="form-control " id="minimum_cart_value" onkeypress="return event.charCode >= 48 && event.charCode <= 57" onpaste="return false" />
                            <span class="text-danger errormsg" id="errminimum_cart_value" ></span>
                            <?php echo form_error('minimum_cart_value'); ?>
                        </div>
                    </div>    


                    <div class="form-group row "  >
                        <label class="col-sm-3" for="discount_rate"> Discount   </label>
                        <div class="col-sm-8">
                            <input type="text" value="<?= $CouponInfo->discount_rate ?>" maxlength="5" name='discount' value="<?= (isset($_POST['discount'])) ? $_POST['discount'] : '' ?>" placeholder="Discount"  autocomplete="off" class="form-control " required="required" id="discount_rate" />
                            <span class="text-danger errormsg" id="errDiscount" ></span>
                             <?php echo form_error('discount_rate'); ?>
                        </div>
                    </div>    



                    <div class="form-group row "  >
                        <label class="col-sm-3" for="maximum_discount_amount"> Maximum Discount Amount </label>
                        <div class="col-sm-8">
                            <input type="text" value="<?= $CouponInfo->maximum_discount_amount ?>" maxlength="5" min="0"  name='maximum_discount_amount' value="<?= (isset($_POST['maximum_discount_amount'])) ? $_POST['maximum_discount_amount'] : '' ?>" placeholder="Maximum Discount Amount"  autocomplete="off" class="form-control " id="maximum_discount_amount" onkeypress="return event.charCode >= 48 && event.charCode <= 57" onpaste="return false" />
                            <span class="text-danger errormsg" id="errmaximum_discount_amount" ></span>
                             <?php echo form_error('maximum_discount_amount'); ?>
                        </div>
                    </div>    


                    <div class="form-group row "  >
                        <label class="col-sm-3" for="expiry_date"> Expiry Date </label>
                        <div class="col-sm-8">
                            <?= form_input('expiry_date',  date('d/m/Y',strtotime($CouponInfo->expiry_date)), 'placeholder="Expiry Date"  autocomplete="off" class="form-control date " required="required"  id="expiry_date" '); ?>
                            <span class="text-danger errormsg" id="errexpiry_date"></span>
                            <?php echo form_error('expiry_date'); ?>
                        </div>
                    </div>    

                    <div class="form-group row "  >
                        <label class="col-sm-3" for="max_coupons"> Max Coupons  </label>
                        <div class="col-sm-8">
                            <input type="text" value="<?= $CouponInfo->max_coupons ?>" maxlength="5" name='max_coupons' value="<?= (isset($_POST['max_coupons'])) ? $_POST['max_coupons'] : '' ?>" placeholder="Max Coupons"  autocomplete="off" class="form-control "  id="max_coupons" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                            <span class="text-danger errormsg" id="errmax_coupons" ></span>
                            <?php echo form_error('max_coupons'); ?>
                        </div>
                    </div>    
                    
                      <div class="form-group row">
                        <label class="col-sm-3" for="staus">Status </label>
                        <div class="col-sm-8">
                            <?php
                                $status['active'] = 'Active';
                                $status['used'] = 'Used';
                                $status['expired'] = 'Expired';

                                echo form_dropdown('status', $status, $CouponInfo->status, 'class="form-control select" id="staus" style="width:100%;" required="required"');
                            ?>
                            <span class="text-danger errormsg" id="erroffer_for_customer"></span>
                             <?php echo form_error('status'); ?>
                        </div>    
                    </div> 

                </fieldset>    
                
                <div class="text-center">
                    <button type="Submit" class="btn btn-success" id="form_validation"> Save </button>
                    <button type="button" class="btn btn-primary" onclick="window.location = 'system_settings/discount_coupon_list'" > Back </button>
                </div>
                <?= form_close(); ?>
            </div>

        </div>
    </div>
</div>
