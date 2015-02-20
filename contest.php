<?php
$pagetitle="Contest List";
include_once("header.php");
include_once("functions/contests.php");
?>
        <div class="span12">
          <button id="arrangevirtual" class="hide btn btn-primary">Arrange VContest</button>
          <div class="btn-group">
            <button id="showall" class="btn btn-info active">All</button>
            <button id="showstandard" class="btn btn-info">Standard</button>
            <button id="showvirtual" class="btn btn-info">Virtual</button>
          </div>
          <div class="btn-group">
            <button id="showcall" class="btn btn-info active">All</button>
            <button id="showcicpc" class="btn btn-info">ICPC</button>
            <button id="showccf" class="btn btn-info">CF</button>
            <button id="showcreplay" class="btn btn-info">Replay</button>
            <button id="showcnonreplay" class="btn btn-info">Non-Replay</button>
          </div>
          <div class="btn-group">
            <button id="showtall" class="btn btn-info active">All</button>
            <button id="showtpublic" class="btn btn-info">Public</button>
            <button id="showtprivate" class="btn btn-info">Private</button>
            <button id="showtpassword" class="btn btn-info">Password</button>
          </div>
          
          <div id="flip-scroll">
              <table width="100%" class="table table-hover table-striped cf basetable" id="contestlist">
                <thead>
                  <tr>
                    <th width='10%'> CID </th>
                    <th width="30%"> Title </th>
                    <th width='15%'> Start Time </th>
                    <th width='15%'> End Time </th>
                    <th width='10%'> Status </th>
                    <th width='10%'> Access </th>
                    <th width="10%"> Manager </th>
                    <th> Private </th>
                    <th> Type </th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
          </div>
        </div>
    <div id="arrangevdialog" class="modal hide fade">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Arrange a virtual contest</h3>
        </div>
        <form method="post" action="ajax/vcontest_arrange.php" class="ajform form-inline" id="arrangeform">
            <div class="modal-body">
                <div class="well hide typenote">
                    In CF, Parameter A represents the points lost per minute. Parameter B represents the points lost for each incorrect submit.<br />
                    In CF Dynamic, parameters will decrease according to the AC ratio.<br />
                    In TC, parameters defined as below. A + B must equal to 1. Parameter C is usually the length of this contest in TopCoder. Parameter E is the percentage of penalty for each incorrect submit.<br />
                    <img src='img/tcpoint.png' />
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <h4>Contest Information</h4>
                        <input type="text" name="title" class="input-block-level" placeholder="Contest Title *" />
                        Type: <label class="radio inline"><input type="radio" name="ctype" value="0" checked="checked" /> ICPC format</label><label class="radio inline"><input type="radio" name="ctype" value="1" /> CF format</label>
                        <textarea name="description" rows="8" class="input-block-level" placeholder="Contest Description"></textarea>
                        <fieldset class="contest-time-pick">
                            <label class="input-prepend input-append date"><span class="add-on">Start Time* : </span><input type="text" name="start_time" value='<?=date("Y-m-d")." 09:00:00"?>'/><span class="add-on"><i class="icon-th"></i></span></label>
                            <p class="prompt_text">( At least after 10 minutes )</p>
                            <label class="input-append input-prepend"><span class="add-on">Duration* : </span><input type="text" name="duration" value='5:00:00'/></label>
                            <p class="prompt_text">( Duration should be between 30 minutes and 15 days )</p>
                            <label class="input-append input-prepend date"><span class="add-on">End Time* : </span><input type="text" name="end_time" value='<?=date("Y-m-d")." 14:00:00"?>'/><span class="add-on"><i class="icon-th"></i></span></label>
                            <p class="prompt_text">( Has to be later than start time )</p>
                            <label class="input-append input-prepend date"><span class="add-on">Lock Board Time: </span><input type="text" name="lock_board_time" value='<?=date("Y-m-d")." 14:00:00"?>'/><span class="add-on"><i class="icon-th"></i></span></label>
                            <p class="prompt_text">( Set it later than end time if you don't want to lock board )</p>
                        </fieldset>
                        <label class="radio inline"><input type="radio" name="localtime" value="1" />Use local timezone</label><label class="radio inline"><input type="radio" name="localtime" value="0" checked="checked" /> Use server timezone</label>
                        <p class="prompt_text">Your timezone: <span id="localtz"></span><input name="localtz" type="hidden" id="tzinp" /></p>
                        <label class="radio inline"><input type="radio" name="hide_others" value="1" /> Hide others' status</label><label class="radio inline"><input type="radio" name="hide_others" value="0" checked="checked" />  Show others' status</label>
                        <label class="input-prepend"><span class="add-on">Password: </span><input type="password" name="password" /></label>
                        <p class="prompt_text">( Leave it blank if not needed )</p>
                        <label><input type="checkbox" name="owner_viewable" />Allow owner view participant's code</label>
                    </div>

<?php
if ($_GET['clone']==1) {
    $ccid=convert_str($_GET['cid']);
    if (contest_passed($ccid)&&(!contest_is_private($ccid)||($current_user->is_valid()&&($current_user->is_in_contest($ccid)||$current_user->is_root())))) {
        $ccrow=contest_get_problem_basic($ccid);
    }
}
?>
                    <div id="probs" class="span6">
                        <h4>Add Problems For Contest</h4>
                        <p>Leave Problem ID blank if you don't want to add it.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span id="msgbox" style="display:none"></span>
                <input name='login' class="btn btn-primary" type='submit' value='Submit' />
            </div>
        </form>
    </div>

<script type="text/javascript" src="js/jstz.min.js"></script>
<script type="text/javascript">
var timezone = jstz.determine_timezone();
$("#localtz").html(timezone.name()+" GMT"+timezone.offset());
$("#tzinp").val(timezone.name());
var searchstr='<?=$_GET['search']?>';
var conperpage=<?=$config["limits"]["contests_per_page"]?>;
var cshowtype='<?=$_GET['type']?>';
$.fn.problemlist.ojoptions="<?=addslashes($ojoptions)?>";
</script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/contest.js?<?=filemtime("js/contest.js")?>"></script>

<?php
include("footer.php");
?>
