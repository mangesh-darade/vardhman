<table id="sales_list" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sales Invoice</th>
            <th>Payments</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(is_array($sales)) {
            foreach ($sales as $key => $sale) {
    ?>
        <tr>
            <td>
                <b>Date :</b> <?=dateTimeFormat($sale['date'], 'jS M Y H:i')?><br/>
                <b>Customer :</b> <?=$sale['customer']?><br/>
                <b>Invoice No. :</b> <?=$sale['invoice_no']?><br/>
                <b>Reff. No. :</b> <?=$sale['reference_no']?><br/>
                <b>Sale Status :</b> <span class="text-capitalize"><?=$sale['sale_status']?></span>
                
            </td>
            <td>
                <b>Total :</b> Rs. <?php echo number_format($sale['grand_total']+$sale['rounding'],2)?><br/>
                <b>Paid :</b> Rs. <?php echo number_format($sale['paid'],2)?><br/>
                <b>Balance :</b> Rs. <?php echo number_format(($sale['grand_total']+$sale['rounding']) - $sale['paid'],2)?><br/>             
                <b>Payments :</b> <span class="<?=$sale['payment_status']?> text-capitalize"><?=$sale['payment_status']?></span><br/>
                <b>Delivery :</b> <span class="text-capitalize"><?=$sale['delivery_status']?></span>
            </td>
           
        </tr>
    <?php     } } ?>
    </tbody>
   
</table>

<script>

    $('#sales_list').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "responsive": true,
    });

</script>