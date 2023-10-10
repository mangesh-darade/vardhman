<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
   .modal-lg{width: 80%}
    table td p{    width: 250px;
      overflow-wrap: break-word;}
</style>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <h2 class="text-center"> list of items </h2>
            <div>
                <strong> Supplier : </strong> <spna> <?= $notificationDetails->biller ?> </spna><br/>
                <strong> Ref.No. : </strong> <spna> <?= $notificationDetails->reference_no ?> </spna>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th> Sr. No. </th>
                        <th> Code</th>
                        <th> Item name </th>
                        <th> QTY </th>
                    </tr>
                </thead>
                <tbody>
                    
                   <?php foreach($items as $key => $itemsValue){  ?>
                    <tr> 
                        <td> <?= $key +1 ?></td>
                        <td> <?= $itemsValue['product_code']?></td>
                        <td> <?= $itemsValue['product_name']?> <?= ($itemsValue['variant_name']?'('.$itemsValue['variant_name'].')' : '') ?></td>                        
                        <td> <?= round($itemsValue['quantity'],2)?> Qty.</td>
                    </tr>
                    
                   <?php } ?>  
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
</script>
