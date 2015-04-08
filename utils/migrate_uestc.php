<?php
/**
 * Short description for migrate_uestc.php
 *
 * @package migrate_uestc
 * @author Chen Ran <crccw@moonux.org>
 * @version 0.1
 * @copyright (C) 2015 Chen Ran <crccw@moonux.org>
 * @license CC-BY-SA 4.0
 */
include_once(dirname(__FILE__)."/../functions/global.php");
include_once(dirname(__FILE__)."/../functions/problems.php");
include_once(dirname(__FILE__)."/../functions/pcrawlers.php");

$old = $db->get_results("select pid,title from problem where vname = 'UESTC'", ARRAY_A);
$new = array();
for($pid=1;$pid<=10;$pid++){
    $url = "http://acm.uestc.edu.cn/problem/data/$pid";
    $data = json_decode(file_get_contents($url), true);

    if ($data['result']==="error") continue;
    $problem = $data['problem'];
    $title = $problem['title'];
    $new[$pid] = $title;
}
foreach($old as $problem){
    $newid = array_keys($new, $problem['title']);
    if(sizeof($newid)==1){
        $sql = "update problem set vid = $newid[0] where pid = ".$problem['pid'];
        echo $sql."\n";
        //$db->query($sql);
    }else{
        echo "FAILED: ".$problem['title']."\n";
    }
}
?>
