<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_pharma = isset($Settings->pos_type) && $Settings->pos_type == 'pharma' ? true : false;
?>
<script type="text/javascript">
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
            product_tax = 0, invoice_tax = 0, product_discount = 0, order_discount = 0, total_discount = 0, total = 0, allow_discount = <?= ($Owner || $Admin || $this->session->userdata('allow_discount')) ? 1 : 0; ?>,
            tax_rates = <?php echo json_encode($tax_rates); ?>;
    //var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
    //var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        localStorage.clear();
        if (localStorage.getItem('remove_slls')) {
            if (localStorage.getItem('slitems')) {
                localStorage.removeItem('slitems');
            }
            if (localStorage.getItem('sldiscount')) {
                localStorage.removeItem('sldiscount');
            }
            if (localStorage.getItem('sltax2')) {
                localStorage.removeItem('sltax2');
            }
            if (localStorage.getItem('slref')) {
                localStorage.removeItem('slref');
            }
            if (localStorage.getItem('slshipping')) {
                localStorage.removeItem('slshipping');
            }
            if (localStorage.getItem('slwarehouse')) {
                localStorage.removeItem('slwarehouse');
            }
            if (localStorage.getItem('slnote')) {
                localStorage.removeItem('slnote');
            }
            if (localStorage.getItem('slinnote')) {
                localStorage.removeItem('slinnote');
            }
            if (localStorage.getItem('slcustomer')) {
                localStorage.removeItem('slcustomer');
            }
            if (localStorage.getItem('slbiller')) {
                localStorage.removeItem('slbiller');
            }
            if (localStorage.getItem('slcurrency')) {
                localStorage.removeItem('slcurrency');
            }
            if (localStorage.getItem('sldate')) {
                localStorage.removeItem('sldate');
            }
            if (localStorage.getItem('slsale_status')) {
                localStorage.removeItem('slsale_status');
            }
            if (localStorage.getItem('slpayment_status')) {
                localStorage.removeItem('slpayment_status');
            }
            if (localStorage.getItem('paid_by')) {
                localStorage.removeItem('paid_by');
            }
            if (localStorage.getItem('amount_1')) {
                localStorage.removeItem('amount_1');
            }
            if (localStorage.getItem('paid_by_1')) {
                localStorage.removeItem('paid_by_1');
            }
            if (localStorage.getItem('pcc_holder_1')) {
                localStorage.removeItem('pcc_holder_1');
            }
            if (localStorage.getItem('pcc_type_1')) {
                localStorage.removeItem('pcc_type_1');
            }
            if (localStorage.getItem('pcc_month_1')) {
                localStorage.removeItem('pcc_month_1');
            }
            if (localStorage.getItem('pcc_year_1')) {
                localStorage.removeItem('pcc_year_1');
            }
            if (localStorage.getItem('pcc_no_1')) {
                localStorage.removeItem('pcc_no_1');
            }
            if (localStorage.getItem('cheque_no_1')) {
                localStorage.removeItem('cheque_no_1');
            }
            if (localStorage.getItem('payment_note_1')) {
                localStorage.removeItem('payment_note_1');
            }
            if (localStorage.getItem('slpayment_term')) {
                localStorage.removeItem('slpayment_term');
            }
            localStorage.removeItem('remove_slls');
        }
<?php if ($quote_id) { ?>
            // localStorage.setItem('sldate', '<?= $this->sma->hrld($quote->date) ?>');
            localStorage.setItem('slcustomer', '<?= $quote->customer_id ?>');
            localStorage.setItem('slbiller', '<?= $quote->biller_id ?>');
            localStorage.setItem('slwarehouse', '<?= $quote->warehouse_id ?>');
            localStorage.setItem('slnote', '<?= str_replace(array("\r", "\n"), "", $this->sma->decode_html($quote->note)); ?>');
            localStorage.setItem('sldiscount', '<?= $quote->order_discount_id ?>');
            localStorage.setItem('sltax2', '<?= $quote->order_tax_id ?>');
            localStorage.setItem('slshipping', '<?= $quote->shipping ?>');
            localStorage.setItem('slitems', JSON.stringify(<?= $quote_items; ?>));
<?php } ?>
<?php if ($this->input->get('customer')) { ?>
            if (!localStorage.getItem('slitems')) {
                localStorage.setItem('slcustomer', <?= $this->input->get('customer'); ?>);
            }
<?php } ?>
<?php if ($Owner || $Admin) { ?>
            if (!localStorage.getItem('sldate')) {
                $("#sldate").datetimepicker({
                    format: site.dateFormats.js_ldate,
                    fontAwesome: true,
                    language: 'sma',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    forceParse: 0
                }).datetimepicker('update', new Date());
            }
            $(document).on('change', '#sldate', function (e) {
                localStorage.setItem('sldate', $(this).val());
            });
            if (sldate = localStorage.getItem('sldate')) {
                $('#sldate').val(sldate);
            }
<?php } ?>
        $(document).on('change', '#slbiller', function (e) {
            localStorage.setItem('slbiller', $(this).val());
        });
        if (slbiller = localStorage.getItem('slbiller')) {
            $('#slbiller').val(slbiller);
        }
        if (!localStorage.getItem('slref')) {
            localStorage.setItem('slref', '<?= $slnumber ?>');
        }

        // New Customer Add 
        if (!localStorage.getItem('slcustomer')) {
            var quick_custome_nm = $('#quick_custome_nm').val();
            //alert(quick_custome_nm);
            if (quick_custome_nm.length != '') {
                localStorage.setItem('slcustomer', quick_custome_nm);
            } else {
                localStorage.setItem('slcustomer', <?= $pos_settings->default_customer; ?>);
            }
        } else {

            var quick_custome_nm = $('#quick_custome_nm').val();
            if (quick_custome_nm.length != '') {
                localStorage.setItem('slcustomer', quick_custome_nm);
            }

        }
        // End new customer add

        if (!localStorage.getItem('sltax2')) {
            localStorage.setItem('sltax2', <?= $Settings->default_tax_rate2; ?>);
        }
        ItemnTotals();
        $('.bootbox').on('hidden.bs.modal', function (e) {
            $('#add_item').focus();
        });
        $("#add_item").autocomplete({
            source: function (request, response) {
                if (!$('#slcustomer').val()) {
                    $('#add_item').val('').removeClass('ui-autocomplete-loading');
                    bootbox.alert('<?= lang('select_above'); ?>');
                    $('#add_item').focus();
                    return false;
                }

                if (request.term.length >= 3) {
                $.ajax({
                    type: 'get',
                    url: '<?= site_url('sales/suggestions'); ?>',
                    dataType: "json",
                    data: {
                        term: request.term,
                        warehouse_id: $("#slwarehouse").val(),
                        customer_id: $("#slcustomer").val()
                    },
                    success: function (data) {
                        response(data);
                    }
                });
                }
            },
            minLength: 1,
            autoFocus: false,
            delay: 250,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                } else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    $(this).removeClass('ui-autocomplete-loading');
                } else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    bootbox.alert('<?= lang('no_match_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).removeClass('ui-autocomplete-loading');
                    $(this).val('');
                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_invoice_item(ui.item);
                    if (row)
                        $(this).val('');
                    $('#print_invoice').attr('disabled', false);
                } else {
                    bootbox.alert('<?= lang('no_match_found') ?>');
                }
            }
        });
        $(document).on('change', '.gift_card_no', function () {
            $('.final-btn').prop('disabled', true);
            var getid = $(this).attr('id').split("_");
            var p_id = getid[3];
            $('#gc_details_' + p_id).html('');
            var cn = $(this).val() ? $(this).val() : '';
            var paid_amount = $('#amount_' + p_id).val();
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_gift_card/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#gift_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?= lang('incorrect_gift_card') ?>');
                        } else if (data.customer_id !== null && data.customer_id !== $('#slcustomer').val()) {
                            $('#gift_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?= lang('gift_card_not_for_customer') ?>');

                        } else if (parseFloat(paid_amount) > parseFloat(data.balance)) {
                            $('#gift_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            $('#gift_card_no_' + p_id).val('');
                            bootbox.alert('<?= lang("Unable to process payment: low card balance") ?>');
                        } else {
                            $('.final-btn').prop('disabled', false);
                            $('#gc_details_' + p_id).html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + ' - Balance: ' + data.balance + '</small>');
                            $('#gift_card_no_' + p_id).parent('.form-group').removeClass('has-error');
                        }
                    }
                });
            }
        });
        $(document).on('change', '.credit_card_no', function () {
            $('.final-btn').prop('disabled', true);
            var getid = $(this).attr('id').split("_");
            var p_id = getid[3];
            $('#credit_details_' + p_id).html('');
            var cn = $(this).val() ? $(this).val() : '';
            var paid_amount = $('#amount_' + p_id).val();
            if (cn != '') {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "sales/validate_credit_note/" + cn,
                    dataType: "json",
                    success: function (data) {
                        if (data === false) {
                            $('#credit_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?= lang('incorrect_gift_card') ?>');
                        } else if (data.customer_id !== null && data.customer_id !== $('#slcustomer').val()) {
                            $('#credit_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            bootbox.alert('<?= lang('gift_card_not_for_customer') ?>');

                        } else if (parseFloat(paid_amount) > parseFloat(data.balance)) {
                            $('#credit_card_no_' + p_id).parent('.form-group').addClass('has-error');
                            $('#credit_card_no_' + p_id).val('');
                            bootbox.alert('<?= lang("Unable to process payment: low card balance") ?>');
                        } else {
                            $('.final-btn').prop('disabled', false);
                            $('#credit_details_' + p_id).html('<small>Card No: ' + data.card_no + '<br>Value: ' + data.value + ' - Balance: ' + data.balance + '</small>');
                            $('#credit_card_no_' + p_id).parent('.form-group').removeClass('has-error');
                        }
                    }
                });
            }
        });
    });
</script>
<script>
    var pa = 1, grand_total = 0;
    $(document).on('click', '.addButton', function () {
        if (pa == 3) {
            bootbox.alert('<?= lang('max_reached') ?>');
            document.getElementById("more_payment_block").style.display = "none";
            return false;
        }

        grand_total = formatDecimal(parseFloat(((total + invoice_tax) - order_discount) + shipping));
        var total_amt = 0, roundig_amt = 0;
        for (var i = 1; i <= pa; i++) {
            total_amt = parseFloat(total_amt) + parseFloat($('#amount_' + i).val());
        }

        pa++;
        $('#paid_by_1, #pcc_type_1').select2('destroy');
        var phtml = $('#payments').html(), update_html = phtml.replace(/_1/g, '_' + pa);
        pi = 'amount_' + pa;


        $('#multi-payment').append('<button type="button" class="close close-payment" style="margin: -10px 0px 0 0;"><i class="fa fa-2x">&times;</i></button>' + update_html);
//                                        $('#paid_by_1, #pcc_type_1, #paid_by_' + pa + ', #pcc_type_' + pa).select2({minimumResultsForSearch: 7});

        roundig_amt = roundNumberNEW(parseFloat(grand_total) - parseFloat(total_amt), Number(pos_settings.rounding));

        $('#amount_' + pa).val(formatDecimal(roundig_amt));

        document.getElementById("pcheque_" + pa).style.display = "none";
        document.getElementById("g_transaction_id_" + pa).style.display = "none";
        document.getElementById("gc_" + pa).style.display = "none";
        $('#gc_details_' + pa).html('');
        document.getElementById("cd_" + pa).style.display = "none";
        $('#credit_details_' + pa).html('');
        if (pa >= 3) {
            document.getElementById("more_payment_block").style.display = "none";
        }

    });

    $(document).on('click', '.close-payment', function () {
        $(this).next().remove();
        $(this).remove();
        pa--;
        document.getElementById("more_payment_block").style.display = "block";
    });
</script>
<input type="hidden" name="sale_action" id="sale_action" value="<?php echo $sale_action; ?>">
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang($sale_action == 'chalan' ? 'Add Sale Challan' : 'add_sale'); ?></h2>
    </div>
    <p class="introtext"><?php echo lang('enter_info'); ?></p>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-sale-form');
                echo form_open_multipart("sales/add", $attrib);
                if ($quote_id || $order_id) {
                    echo form_hidden(['quote_id' => $quote_id, 'order_id' => $order_id]);
                }
                echo form_hidden(['syncQuantity' => $syncQuantity]);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <?php if ($Owner || $Admin || $GP['sales-date']) { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("date", "sldate"); ?>
                                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("reference_no", "slref"); ?>
                                    <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" id="slref"'); ?>
                                </div>
                            </div>
                            <?php if ($Owner || $Admin || !$this->session->userdata('biller_id')) { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("biller", "slbiller"); ?>
                                        <?php
                                        $bl[""] = "";
                                        foreach ($billers as $biller) {
                                            $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                        }
                                        echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $pos_settings->default_biller), 'id="slbiller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            <?php
                            } else {
                                $biller_input = array(
                                    'type' => 'hidden',
                                    'name' => 'biller',
                                    'id' => 'slbiller',
                                    'value' => $this->session->userdata('biller_id'),
                                );

                                echo form_input($biller_input);
                            }
                            ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if ($is_pharma): ?>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="patient_name">Patient Name </label>
                                    <input type="text" name="patient_name" id="patient_name" class="form-control input-tip required">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="doctor_name">Doctor Name </label>
                                    <input type="text" name="doctor_name" id="doctor_name" class="form-control input-tip required">
                                </div>
                            </div>
                        </div>  
                        <div class="clearfix"></div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-warning">
                                    <div class="panel-heading"><?= lang('please_select_these_before_adding_product') ?></div>
                                    <div class="panel-body" style="padding: 5px;">
<?php //if ($Owner || $Admin || !$this->session->userdata('warehouse_id')) {  ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?= lang("warehouse", "slwarehouse"); ?>
                                                <?php
                                                $permisions_werehouse = explode(",", $this->session->userdata('warehouse_id'));
                                                // $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    if ($Owner || $Admin) {
                                                        $wh[$warehouse->id] = $warehouse->name;
                                                    } elseif (in_array($warehouse->id, $permisions_werehouse)) {
                                                        $wh[$warehouse->id] = $warehouse->name;
                                                    }
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                        <?php /* } else {
                                          $warehouse_input = array(
                                          'type' => 'hidden',
                                          'name' => 'warehouse',
                                          'id' => 'slwarehouse',
                                          'value' => $this->session->userdata('warehouse_id'),
                                          );

                                          echo form_input($warehouse_input);
                                          } */ ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                    <?= lang("customer", "slcustomer"); ?>
                                                <div class="input-group">
                                                    <?php
                                                    echo form_input('customer', (isset($_SESSION['quick_customername']) ? $_SESSION['quick_customername'] : (isset($_POST['customer']) ? $_POST['customer'] : $pos_settings->default_customer)), 'id="slcustomer" data-placeholder="' . lang("select") . ' ' . lang("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                    ?>
                                                    <input type="hidden" name="quick_custome_nm" id="quick_custome_nm" value="<?php echo (isset($_SESSION['quick_customerid']) ? $_SESSION['quick_customerid'] : ''); ?>">

                                                    <div class="input-group-addon no-print" style="padding: 2px 8px; border-left: 0;">
                                                        <a href="#" id="toogle-customer-read-attr" class="external edit-customers">
                                                            <i class="fa fa-pencil" id="addIcon" style="font-size: 1.2em;"></i>
                                                        </a>
                                                    </div>
                                                    <div class="input-group-addon no-print" style="padding: 2px 7px; border-left: 0;">
                                                        <a href="#" id="view-customer" class="external" data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-eye" id="addIcon" style="font-size: 1.2em;"></i>
                                                        </a>
                                                    </div>
                                                    <?php if ($Owner || $Admin || $GP['customers-add']) { ?>
                                                        <div class="input-group-addon no-print" style="padding: 2px 8px;">
                                                            <a href="<?= site_url('customers/add/quick'); ?>" id="add-customer"class="external" data-toggle="modal" data-target="#myModal">
                                                                <i class="fa fa-plus-circle" id="addIcon"  style="font-size: 1.2em;"></i>
                                                            </a>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($saleAction) { ?>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <?= lang("Sale Action", "slsale_action"); ?>
                                                    <?php
                                                    $sstyp['sales'] = lang('As Sales');
                                                    if ($this->data['Owner'] || $this->data['GP']['sales-add_challans']) {
                                                        $sstyp['chalan'] = lang('As Chalan');
                                                    }
                                                    if ($sale_action == 'chalan') {
                                                        unset($sstyp['sales']);
                                                    }
                                                    echo form_dropdown('sale_action', $sstyp, $sale_action, 'class="form-control input-tip" required="required" id="sale_action"');
                                                    ?>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <input type="hidden" name="sale_action" id="sale_action" value="<?= $sale_action ? $sale_action : 'sales'; ?>" />                                        
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="sticker">
                                <div class="well well-sm">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <div class="input-group wide-tip">
                                            <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                <i class="fa fa-2x fa-barcode addIcon"></i></a></div>
<?php echo form_input('add_item', '', 'class="form-control input-lg" id="add_item" placeholder="' . lang("add_product_to_order") . '"'); ?>
<?php if ($Owner || $Admin || $GP['products-add']) { ?>
                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="addManually" class="tip" title="<?= lang('add_product_manually') ?>">
                                                        <i class="fa fa-2x fa-plus-circle addIcon" id="addIcon"></i>
                                                    </a>
                                                </div>
<?php } if ($Owner || $Admin || $GP['sales-add_gift_card']) { ?>
                                                <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                    <a href="#" id="sellGiftCard" class="tip" title="<?= lang('sell_gift_card') ?>">
                                                        <i class="fa fa-2x fa-credit-card addIcon" id="addIcon"></i>
                                                    </a>
                                                </div>
<?php } ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="control-group table-group">
                                    <label class="table-label"><?= lang("order_items"); ?> *</label>

                                    <div class="controls table-controls">
                                        <table id="slTable" class="table items table-striped table-bordered table-condensed table-hover sortable_table">
                                            <thead>
                                                <tr>
                                                    <th><?= lang("product_name") . " (" . lang("product_code") . ")"; ?></th>
                                                    <th class="col-md-1">Variant</th>
                                                    
                                                    <?php if($this->Settings->overselling == 0) { ?>
                                                    <th class="col-md-1">Item<br/>Cart Qt. / Stock Qt.</th>
                                                    <?php } ?>
                                                    <?php
                                                        if ($Settings->product_serial) {
                                                            echo '<th class="col-md1">' . lang("serial_no") . '</th>';
                                                        }
                                                    ?>
                                                    <?php if ($Settings->product_batch_setting > 0) { ?>
                                                        <th style="width:10%;"><?= lang("Batch_Number"); ?></th>
                                                    <?php } ?>
                                                    <?php if ($Settings->product_expiry > 0) { ?>
                                                        <th class="col-md-1"><?= lang("expiry_date") ?></th> 
                                                    <?php } ?>
                                                    <th class="col-md-1"><?= lang("quantity"); ?></th>
                                                    <?php if ($Settings->product_weight == 1) { ?>
                                                        <th class="col-md-1"><?= lang("Weight") ?></th> 
                                                    <?php } ?>
                                                    <th class="col-md-1"><?= lang("Unit Price") ?></th>                                                    
                                                    <?php
                                                    if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) {
                                                        echo '<th class="col-md-1">' . lang("discount") . '</th>';
                                                    }
                                                    ?>
                                                    <th class="col-md-1"><?= lang("Net Price"); ?> </th>
                                                    <?php
                                                    if ($Settings->tax1) {
                                                        echo '<th class="col-md-1">' . lang("product_tax") . '</th>';
                                                    }
                                                    ?>
                                                    <th>
                                                        <?= lang("subtotal"); ?>
                                                        (<span class="currency"><?= $default_currency->code ?></span>)
                                                    </th>
                                                    <th style="width: 30px !important; text-align: center;">
                                                        <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                    <?php if ($Settings->tax2) { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang("order_tax", "sltax2"); ?>
                                        <?php
                                        $tr[""] = "";
                                        foreach ($tax_rates as $tax) {
                                            $tr[$tax->id] = $tax->name;
                                        }
                                        echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . lang("select") . ' ' . lang("order_tax") . '" class="form-control input-tip select" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) {
                                if ($Settings->sales_order_discount == '1') {
                                    ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                    <?= lang("order_discount", "sldiscount"); ?>
                                    <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sldiscount"'); ?>
                                        </div>
                                    </div>
                                <?php }
                            } ?>

                            <div class="col-md-4">
                                <div class="form-group">
<?= lang("shipping", "slshipping"); ?>
<?php echo form_input('shipping', '', 'class="form-control input-tip" id="slshipping"'); ?>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
<?= lang("document", "document") ?>
                                    <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                           data-show-preview="false" class="form-control file">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
<?= lang("sale_status", "slsale_status"); ?>
<?php $sst = array('completed' => lang('completed'), 'pending' => lang('pending'));
echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"');
?>

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                            <?= lang("payment_term", "slpayment_term"); ?>
<?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); ?>

                                </div>
                            </div>
                                    <?php if ($Owner || $Admin || $GP['sales-payments']) { ?>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                <?= lang("payment_status", "slpayment_status"); ?>
                                <?php $pst = array('pending' => lang('pending'), 'due' => lang('due'), 'partial' => lang('partial'), 'paid' => lang('paid'));
                                echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="slpayment_status"');
                                ?>

                                    </div>
                                </div>
                                <?php
                            } else {
                                echo form_hidden('payment_status', 'pending');
                            }
                            ?>
                        </div>

                        <div id="payments" style="display: none;">
                            <div class="col-md-12">
                                <div class="well well-sm well_1">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                        <?= lang("payment_reference_no", "payment_reference_no"); ?>
                                                        <?= form_input('payment_reference_no[]', (isset($_POST['payment_reference_no']) ? $_POST['payment_reference_no'] : $payment_ref), 'class="form-control tip" id="payment_reference_no"'); ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="payment">
                                                    <div class="form-group ngc">
<?= lang("amount", "amount_1"); ?>
                                                        <input name="amount-paid[]" type="text" id="amount_1"
                                                               class="pa form-control kb-pad amount" onkeypress="return isNumberKey(event)" required="required"/>
                                                        <span id="error" style="color:#a94442; display: none;font-size:11px;">please enter numbers only</span>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
<?= lang("paying_by", "paid_by_1"); ?>
                                                    <select name="paid_by[]" id="paid_by_1" class="form-control paid_by">
<?= $this->sma->paid_opts(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="payment">
                                                    <div class="pcheque_1" id="pcheque_1" style="display:none;">
                                                        <div class="form-group"><?= lang("cheque_no", "cheque_no_1"); ?>
                                                            <input name="cheque_no[]" type="text" id="cheque_no_1"
                                                                   class="form-control cheque_no"/>
                                                        </div>
                                                    </div>
                                                    <div class="gc_1" id="gc_1" style="display: none;">
<?= lang("gift_card_no", "gift_card_no"); ?>
                                                        <input name="gift_card_no[]" type="text" id="gift_card_no_1"
                                                               class="pa form-control kb-pad gift_card_no"/>

                                                        <div id="gc_details_1"></div>
                                                    </div>
                                                    <div class="cd_1" id="cd_1" style="display:none;">
                                                        <label for="credit_card_no_1">Credit Note</label>
                                                        <input name="credit_card_no[]" type="text" id="credit_card_no_1" class="pa form-control kb-pad credit_card_no" autocomplete="off">
                                                        <div id="credit_details_1"></div>  
                                                    </div>
                                                    <div class="g_transaction_id_1" id="g_transaction_id_1" style="display: none;">
<?= lang("transaction_id", "transaction_id"); ?>
                                                        <input name="transaction_id[]" type="text" id="transaction_id_1" class="transaction_id form-control kb-pad"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="pcc_1" style="display:none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_no[]" type="text" id="pcc_no_1"
                                                               class="form-control" placeholder="<?= lang('cc_no') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_holder[]" type="text" id="pcc_holder_1"
                                                               class="form-control"
                                                               placeholder="<?= lang('cc_holder') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <select name="pcc_type[]" id="pcc_type_1"
                                                                class="form-control pcc_type"
                                                                placeholder="<?= lang('card_type') ?>">
                                                            <option value="Visa"><?= lang("Visa"); ?></option>
                                                            <option
                                                                value="MasterCard"><?= lang("MasterCard"); ?></option>
                                                            <option value="Amex"><?= lang("Amex"); ?></option>
                                                            <option value="Discover"><?= lang("Discover"); ?></option>
                                                        </select>
                                                        <!-- <input type="text" id="pcc_type_1" class="form-control" placeholder="<?= lang('card_type') ?>" />-->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input name="pcc_month[]" type="text" id="pcc_month_1"
                                                               class="form-control" placeholder="<?= lang('month') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="pcc_year[]" type="text" id="pcc_year_1"
                                                               class="form-control" placeholder="<?= lang('year') ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">

                                                        <input name="pcc_ccv[]" type="text" id="pcc_cvv2_1"
                                                               class="form-control" placeholder="<?= lang('cvv2') ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div id="multi-payment" style="display:none"> </div>
                        <div id="more_payment_block" style="display:none">
                            <button type="button" class="btn btn-primary col-md-12 addButton"><i class="fa fa-plus"></i> <?= lang('Add_More_Payments') ?></button>
                        </div>
                        <div class="form-group">
<?= lang('payment_note', 'payment_note_1'); ?>
                            <textarea name="payment_note" id="payment_note_1"
                                      class="pa form-control kb-text payment_note"></textarea>
                        </div>
                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="row" id="bt">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= lang("sale_note", "slnote"); ?>
                                            <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="slnote" style="margin-top: 10px; height: 100px;"'); ?>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
<?= lang("staff_note", "slinnote"); ?>
<?php echo form_textarea('staff_note', (isset($_POST['staff_note']) ? $_POST['staff_note'] : ""), 'class="form-control" id="slinnote" style="margin-top: 10px; height: 100px;"'); ?>

                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="submit_type" id="submit_type" value="">
                                <div class="fprom-group">
<?php echo form_submit('add_sale', lang("submit"), 'id="add_sale" class="btn btn-primary final-btn" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                    <button type="button" class="btn btn-info cmdprint final-btn" name="cmdprint" style="padding: 6px 15px; margin:15px 0;"  id="print_invoice" >Submit & Print</button>
                                    <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-total" class="well well-sm" style="margin-bottom: 0;">
                    <table class="table table-bordered table-condensed totals" style="margin-bottom:0;">
                        <tr class="warning">
                            <td><?= lang('items') ?> <span class="totals_val pull-right" id="titems">0</span></td>
                            <td><?= lang('total') ?> <span class="totals_val pull-right" id="total">0.00</span></td>
<?php if ($Owner || $Admin || $this->session->userdata('allow_discount')) { ?>
                               <!-- <td><?= lang('order_discount') ?> <span class="totals_val pull-right" id="tds">0.00</span></td> -->
<?php } ?>
<?php if ($Settings->tax2) { ?>
                                <td><?= lang('order_tax') ?> <span class="totals_val pull-right" id="ttax2">0.00</span></td>
                <?php } ?>
                            <td><?= lang('shipping') ?> <span class="totals_val pull-right" id="tship">0.00</span></td>
                            <td><?= lang('grand_total') ?> <span class="totals_val pull-right" id="gtotal">0.00</span></td>
                        </tr>
                    </table>
                </div>

<?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<div class="modal" id="prModal" tabindex="-1" role="dialog" aria-labelledby="prModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?= lang('close'); ?></span></button>
                <h4 class="modal-title" id="prModalLabel"></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                            <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?= lang('product_tax') ?></label>
                            <div class="col-sm-8">
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('ptax', $tr, "", 'id="ptax" class="form-control pos-input-tip" style="width:100%;"');
                                ?>
                            </div>
                        </div>
<?php } ?>
<?php if ($Settings->product_serial) { ?>
                        <div class="form-group">
                            <label for="pserial" class="col-sm-4 control-label"><?= lang('serial_no') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pserial">
                            </div>
                        </div>
<?php } ?>
                    <div class="form-group">
                        <label for="pquantity" class="col-sm-4 control-label"><?= lang('quantity') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pquantity">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="punit" class="col-sm-4 control-label"><?= lang('product_unit') ?></label>
                        <div class="col-sm-8">
                            <div id="punits-div"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="poption" class="col-sm-4 control-label"><?= lang('product_option') ?></label>
                        <div class="col-sm-8">
                            <div id="poptions-div"></div>
                        </div>
                    </div>
<?php if ((int) $Settings->product_batch_setting) { ?>
                        <div class="form-group">
                            <label for="pbatch_number" class="col-sm-4 control-label"><?= lang('batch_number') ?></label>
                            <div class="col-sm-8" id="batchNo_div" ></div>
                        </div>
<?php } ?>
<?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                        <div class="form-group">
                            <label for="pdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="pdiscount">
                            </div>
                        </div>
<?php } ?>
                    <div class="form-group">
                        <label for="pprice" class="col-sm-4 control-label"><?= lang('unit_price') ?></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pprice" <?= ($Owner || $Admin || $GP['edit_price']) ? '' : 'readonly'; ?>>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="net_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="pro_tax"></span></th>
                        </tr>
                    </table>
                    <input type="hidden" id="punit_price" value=""/>
                    <input type="hidden" id="old_tax" value=""/>
                    <input type="hidden" id="old_qty" value=""/>
                    <input type="hidden" id="old_price" value=""/>
                    <input type="hidden" id="row_id" value=""/>
                    <input type="hidden" id="item_id" value=""/>
                    <input type="hidden" id="storage_type" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editItem"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="mModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"><i
                            class="fa fa-2x">&times;</i></span><span class="sr-only"><?= lang('close'); ?></span></button>
                <h4 class="modal-title" id="mModalLabel"><?= lang('add_product_manually') ?></h4>
            </div>
            <div class="modal-body" id="pr_popover_content">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="mcode" class="col-sm-4 control-label"><?= lang('product_code') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mcode">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mname" class="col-sm-4 control-label"><?= lang('product_name') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mname">
                        </div>
                    </div>
                            <?php if ($Settings->tax1) { ?>
                        <div class="form-group">
                            <label for="mtax" class="col-sm-4 control-label"><?= lang('product_tax') ?> *</label>

                            <div class="col-sm-8">
                                <?php
                                $tr[""] = "";
                                foreach ($tax_rates as $tax) {
                                    $tr[$tax->id] = $tax->name;
                                }
                                echo form_dropdown('mtax', $tr, "", 'id="mtax" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
<?php } ?>
                    <div class="form-group">
                        <label for="mquantity" class="col-sm-4 control-label"><?= lang('quantity') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mquantity">
                        </div>
                    </div>
<?php if ($Settings->product_discount && ($Owner || $Admin || $this->session->userdata('allow_discount'))) { ?>
                        <div class="form-group">
                            <label for="mdiscount"
                                   class="col-sm-4 control-label"><?= lang('product_discount') ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="mdiscount">
                            </div>
                        </div>
<?php } ?>
                    <div class="form-group">
                        <label for="mprice" class="col-sm-4 control-label"><?= lang('unit_price') ?> *</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="mprice">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th style="width:25%;"><?= lang('net_unit_price'); ?></th>
                            <th style="width:25%;"><span id="mnet_price"></span></th>
                            <th style="width:25%;"><?= lang('product_tax'); ?></th>
                            <th style="width:25%;"><span id="mpro_tax"></span></th>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addItemManually"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-2x">&times;</i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
<?= lang("card_no", "gccard_no"); ?> *
                    <div class="input-group">
                    <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genNo"><i class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname"/>

                <div class="form-group">
<?= lang("value", "gcvalue"); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
<?= lang("price", "gcprice"); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>
                <div class="form-group">
<?= lang("customer", "gccustomer"); ?>
<?php echo form_input('gccustomer', '', 'class="form-control" id="gccustomer"'); ?>
                </div>
                <div class="form-group">
<?= lang("expiry_date", "gcexpiry"); ?>
<?php echo form_input('gcexpiry', $this->sma->hrsd(date("Y-m-d", strtotime("+2 year"))), 'class="form-control date" id="gcexpiry"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#print_invoice').attr('disabled', true);
        $('#gccustomer').select2({
            minimumInputLength: 1,
            ajax: {
                url: site.base_url + "customers/suggestions",
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
        });
        $('#genNo').click(function () {
            var no = generateCardNo();
            $(this).parent().parent('.input-group').children('input').val(no);
            return false;
        });
    });

    jQuery('.cmdprint').on('click', function () {
        jQuery('#submit_type').val('print');
    });

    $('#add_sale').click(function () {
        var paid_by = $('.paid_by').val();
        var credit_card_no = $('.credit_card_no').val();
        if (paid_by == 'credit_note') {
            if (credit_card_no.length == '') {
                $('.credit_card_no').parent('.form-group').addClass('has-error');
                bootbox.alert('Credit Note required.');
                return false;
            }
        }

        var gift_card_no = $('.gift_card_no').val();
        if (paid_by == 'gift_card') {
            if (gift_card_no.length == '') {
                $('.gift_card_no').parent('.form-group').addClass('has-error');
                bootbox.alert('<?= lang('required_gift_card') ?>');
                return false;
            }
        }
    });
    $('#print_invoice').click(function () {
        var paid_by = $('.paid_by').val();
        var credit_card_no = $('.credit_card_no').val();
        if (paid_by == 'credit_note') {
            if (credit_card_no.length == '') {
                $('.credit_card_no').parent('.form-group').addClass('has-error');
                bootbox.alert('Credit Note required.');
                return false;
            }
        }
        var gift_card_no = $('.gift_card_no').val();
        if (paid_by == 'gift_card') {
            if (gift_card_no.length == '') {
                $('.gift_card_no').parent('.form-group').addClass('has-error');
                bootbox.alert('<?= lang('required_gift_card') ?>');
                return false;
            }
        }
        $('#print_invoice').text('<?= lang('loading'); ?>').attr('disabled', true);
        document.getElementById('add-sale-form').submit();
    });

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
    /****/



    <?php 
        if($Settings->send_sales_excel){
            if($_SESSION['Send_Excel']==1){ ?>
               $.ajax({
                       type:'ajax',
                       method:'get',
                       url:'<?= base_url()."sales/export_excel"."/".$_SESSION['sale_id']; ?>',
                       //async:false,
                       success:function(res){
                               console.log(res);
                               console.log('success');
                       }, error:function(){
                               console.log('errror');
                       }
               });
         <?php } 
        }
   ?>
</script>
