$(document).ready(function() {
    $('.datepick').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss'
    });

    $("#cmodifyform").bind("correct",function(){
        window.location.href="contest.php?virtual=1";
    });

    $("input[name='ctype']").change(function() {
        var ctp=$(this).val();
        $("#probs").problemlist("settype",ctp);
    });

    $("#probs").problemlist();
    $("#probs").problemlist("loadcontest",getURLPara("cid"));
    $(".ptype:checked").change();
    $("input[name='ctype']:checked").change();
});
