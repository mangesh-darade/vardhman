
<table class="table table-head-fixed text-nowrap">
    <thead>
        <tr>
            <?php if($postData['list_type'] == 'sale_list') { ?>
            <th>Customer Invoice</th>
            <?php } else { ?>
            <th>Customer(s)</th>
            <?php } ?>            
            <th>Payment(s)</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(is_array($payments)){
            foreach ($payments as $payment) {
    ?>
        <tr>
            <?php if($postData['list_type'] == 'customers_list') { ?>
            <td><b>CODE :</b> #<?=$payment['customer_id']?><br/>
                <b>NAME :</b> <?=$payment['customer']?>
                </td>
                <td class="align-right"><b>Total :</b> Rs. <?=$this->sma->formatDecimal($payment['sale_total'])?><br/>
                    <b>Paid :</b> Rs. <?=$this->sma->formatDecimal($payment['paid'])?><br/>
                    <?= $postData['payment_status'] == 'pending' ? '<span class="text-danger"><b>Balance :</b> Rs. '.$this->sma->formatDecimal($payment['balance']).'</span>' : '';?>
                </td>
            <?php } ?>
            
            <?php if($postData['list_type'] == 'sale_list') { ?>
                <td><b>Invoice No:</b> <a href="<?=base_url('storeapp/sales/'.$payment['id'])?>"><?=$payment['invoice_no']?></a><br/>
                    <b>DATE :</b> <?=DateTimeFormat($payment['date'])?><br/> 
                    <b>CUSTOMER CODE :</b> #<?=$payment['customer_id']?><br/>  
                    <b>NAME :</b> <?=$payment['customer']?>
                </td>
                <td><b>Total :</b> Rs. <?=$this->sma->formatDecimal($payment['sale_total'])?><br/> 
                    <b>Paid :</b> Rs. <?=$this->sma->formatDecimal($payment['paid'])?><br/> 
                    <?= $postData['payment_status'] == 'pending' ? '<b>Pending :</b> <span class="text-danger">Rs. '.$this->sma->formatDecimal($payment['balance']).'</span><br/>' : '';?> 
                    <b>Status :</b> <span class="<?=$payment['payment_status']?>"><?=$payment['payment_status']?></span>
                </td> 
            <?php } ?> 
        </tr>
    <?php
    
            $total_balance += $payment['balance'];
            }//end foreach.            
        }
    ?> 
    </tbody>
   <?php if($total_balance) { ?>
    <tfoot>
        <tr>
            <th>Total Pending Amount</th>
            <th class="due">Rs. <?=$this->sma->formatDecimal($total_balance,2)?></th>
        </tr>
    </tfoot>
   <?php } ?>
</table> 
