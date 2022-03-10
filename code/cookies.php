<?php namespace presentation;
/* check for cookies -- YOU MUST EDIT THE COOKIE NAME */
$cookieName="tscskunks-pwa";
if(isset($_COOKIE[$cookieName]))
{
    $cArray=json_decode($_COOKIE[$cookieName], true);
    $was="<br>Array:WasCookie(".__LINE__."()<br><pre>".print_r($cArray,true). "</pre><hr>";
    /* to unset a cookie create it at negative time like this */
    //setcookie($cookieName, "", time() + (-3* 86400 * 30), "/");
}
/* you can set cookie values here */
// $cookieArray['setAt']=date('Y-m-d H:i:s');
// $cookieArray['email']='wayne.p@cllkie.com';
// $cookieArray['userName']='waynePH';
// $cookieArray['auth']=array();
// setcookie($cookieName, json_encode($cookieArray), time() + (3* 86400 * 30), "/"); //3 days
// $was.="<br>Array:IsCookie(".__LINE__."()<br><pre>".print_r($cookieArray,true). "</pre><hr>";
echo($was);