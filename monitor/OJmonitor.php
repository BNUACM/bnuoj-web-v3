<?php

include_once(dirname(__FILE__)."/../config.php");
include_once(dirname(__FILE__)."/../functions/simple_html_dom.php");
include_once(dirname(__FILE__)."/../functions/global.php");

$maxwaitnum=8;
$timeoutopts = stream_context_create(array('http' =>
    array(
        'timeout' => 120 
    )
));

function check_pku() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://poj.org/status", false, $timeoutopts);
    if ($html==null||$html->find("table",4)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",4)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if ($result=="Waiting") $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum waitings.";
        return "Normal";
    }
}

function check_hdu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.hdu.edu.cn/status.php", false, $timeoutopts);
    if ($html==null||$html->find("table.table_text",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table.table_text tr");
        foreach ($res as $row) {
            $result=$row->find("td",2)->plaintext;
            // echo $result;
            if ($result=="Queuing") $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_uvalive() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://livearchive.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=19", false, $timeoutopts);
    if ($html==null||$html->find("td.maincontent table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("td.maincontent table tr");
        foreach ($res as $row) {
            $result=$row->find("td",4)->plaintext;
            // echo $result;
            if ($result=="In judge queue") $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_codeforces() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://www.codeforces.com/problemset/status", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table tr");
        foreach ($res as $row) {
            $result=$row->find("td",5)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_sgu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.sgu.ru/status.php", false, $timeoutopts);
    if ($html==null||$html->find("table",12)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",12)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",5)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_lightoj() {
    global $maxwaitnum,$config;
    $ojuser=$config["accounts"]["lightoj"]["username"];
    $ojpass=$config["accounts"]["lightoj"]["password"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.lightoj.com/login_check.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/lightoj_check.cookie");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "myuserid=".urlencode($ojuser)."&mypassword=".urlencode($ojpass)."&Submit=Login");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    $content = curl_exec($ch);
    curl_close($ch);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.lightoj.com/volume_submissions.php");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/lightoj_check.cookie");
    $content = curl_exec($ch);
    curl_close($ch);
    unlink("/tmp/lightoj_check.cookie");

    $html=str_get_html($content);
    if ($html==null||$html->find("table",2)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",2)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",6)->plaintext;
            // echo $result;
            if (stristr($result,"not judged yet")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_ural() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.timus.ru/status.aspx", false, $timeoutopts);
    if ($html==null||$html->find("table.status",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table.status",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",5)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_zju() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.zju.edu.cn/onlinejudge/showRuns.do?contestId=1", false, $timeoutopts);
    if ($html==null||$html->find("table.list",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table.list tr");
        foreach ($res as $row) {
            $result=$row->find("td",2)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_uva() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=19", false, $timeoutopts);
    if ($html==null||$html->find("div#col3_content_wrapper table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("div#col3_content_wrapper table tr");
        foreach ($res as $row) {
            $result=$row->find("td",4)->plaintext;
            // echo $result;
            if ($result=="In judge queue") $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_spoj() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://www.spoj.pl/status/", false, $timeoutopts);
    if ($html==null||$html->find("table.problems",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table.problems tr");
        foreach ($res as $row) {
            $result=$row->find("td",4)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_uestc() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.uestc.edu.cn/status.php", false, $timeoutopts);
    if ($html==null||$html->find("div.list",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("div.list ul");
        foreach ($res as $row) {
            $result=$row->find("li",3)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_fzu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.fzu.edu.cn/log.php", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table tr");
        foreach ($res as $row) {
            $result=$row->find("td",2)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_nbut() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://ac.nbutoj.com/Problem/status.xhtml", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_whu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.whu.edu.cn/land/status", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_sysu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://soj.me/status.php", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table tr");
        foreach ($res as $row) {
            $result=$row->find("td",5)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_openjudge() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://poj.openjudge.cn/practice/status", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",2)->plaintext;
            // echo $result;
            if ($result=="Waiting") $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum waitings.";
        return "Normal";
    }
}

function check_scu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://cstest.scu.edu.cn/soj/solutions.action", false, $timeoutopts);
    if ($html==null||$html->find("table",1)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",1)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",5)->plaintext;
            // echo $result;
            if (stristr($result,"queu")||stristr($result,"waiting")||stristr($result,"being")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_hust() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.hust.edu.cn/status", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if (stristr($result,"pending")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_njupt() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acm.njupt.edu.cn/acmhome/showstatus.do", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",2)->plaintext;
            // echo $result;
            if (stristr($result,"pending")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_aizu() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://judge.u-aizu.ac.jp/onlinejudge/status.jsp", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if (stristr($result,"pending")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_acdream() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://acdream.info/status", false, $timeoutopts);
    if ($html==null||$html->find("table",0)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",3)->plaintext;
            // echo $result;
            if (stristr($result,"pending")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

function check_codechef() {
    global $maxwaitnum, $timeoutopts;
    $html=file_get_html("http://www.codechef.com/submissions", false, $timeoutopts);
    if ($html==null||$html->find("table",4)==null) return "Down: cannot connect.";
    else {
        $num=0;
        $res=$html->find("table",0)->find("tr");
        foreach ($res as $row) {
            $result=$row->find("td",5);
            // echo $result;
            if (stristr($result,"pending")||stristr($result,"waiting")) $num++;
        }
        if ($num>$maxwaitnum) return "Possibly down: more than $maxwaitnum queuings.";
        return "Normal";
    }
}

$ojs=$db->get_results("select name from ojinfo where name not like 'BNU'",ARRAY_N);

foreach ($ojs as $one) {
    $name="check_".strtolower($one[0]);
    $stat=$db->escape($name());
//    echo $name." ".$stat."\n";
    $db->query("update ojinfo set lastcheck=now(), status='$stat' where name='".$one[0]."'");
}


// echo check_pku();
// echo check_hdu();
// echo check_uvalive();
// echo check_cf();
// echo check_sgu();
// echo check_lightoj();
// echo check_ural();
// echo check_zju();
// echo check_uva();
// echo check_spoj();
// echo check_uestc();
// echo check_fzu();
// echo check_nbut();
// echo check_whu();
// echo check_sysu();
// echo check_openjudge();
// echo check_scu();
// echo check_hust();
?>
