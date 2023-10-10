<footer class="main-footer">
    <table style="width: 100%;">
        <tr>
            <td><a class="btn  text-white" href="<?=base_url('storeapp')?>"> <i class="fa fa-home text-md"></i></a></td>
            <td><button type="button" class="btn float-right text-white" onclick="javascript:history.back(-1)"><i class="fa fa-arrow-circle-left text-md"></i></button></td>
        </tr>
    </table>
            
</footer>
<script>
function formatDecimal(x, d) {
    if (!d) {
        d = 4;
    }
    return x.toFixed(d);
}
</script>