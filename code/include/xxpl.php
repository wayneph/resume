<?php namespace presentationLogic;
class Pl
{
    static public function setConditionalArray(array $elementsArray)
    {
        $retArray=array();
        for($i=0;$i<count($elementsArray);$i++){
            if($elementsArray[$i]['conditional']==1){
                $retArray[$elementsArray[$i]['position_name']]=$elementsArray[$i]['element_text'];
            }
        }
        return $retArray;
    }

    static public function xxbuildEntityListsAccordion(array $entitiesList)
    {
        /*
            description is in the json field..
        */
        $outPutHtml="<!-- plGenerated::".__METHOD__."::line::".__LINE__."  -->";
        $htmlTemplate="\n<button class=\"accordion\">##heading## Information</button>\n
        <div class=\"panel\">\n
          <p>\n
            <br>
            <ul>\n
                <li>
                    See <b>All Information &amp; Related Items</b> for &rarr; <a href=\"entityInfoAll.php/##slug##/all\">##heading##</a>\n
                </li>
                <li>\n
                    See Only <b>Information Items</b> on &rarr; <a href=\"entityInfo.php/##slug##/info\">##heading##</a>\n
                </li>
                <li>
                    See Only <b>Related Items</b> for &rarr; <a href=\"entityInfo.php/##slug##/relate\">##heading##</a>\n
                </li>\n
            </ul>
          </p>\n
        </div>\n";
        for($i=0;$i<count($entitiesList);$i++){
            $itemHtml = $htmlTemplate;
            $itemHtml = str_replace("##heading##",$entitiesList[$i]['entity'],$itemHtml);
            $itemHtml = str_replace("##slug##",$entitiesList[$i]['slug'],$itemHtml);
            //$itemHtml = str_replace("##description##",$description,$itemHtml);
            $outPutHtml.=$itemHtml;
        }
        return $outPutHtml;
    }

    static public function xxxxxbuildEntityTypesAccordion($entityTypesArray)                     //k8s
    {
        $outPutHtml="<!-- plGenerated::".__METHOD__."::line::".__LINE__."  -->";
        $htmlTemplate="\n<button class=\"accordion\">##heading##</button>\n
        <div class=\"panel\">\n
          <p>##description##\n
          <br><br>\n
          &nbsp;&minusb;&nbsp;<a href=\"showEntitiesForType.php/##linkCode##\">List tech for <strong>##heading##(s)</strong> here</a></b>.\n
          </p>\n
        </div>\n";
        for($i=0;$i<count($entityTypesArray);$i++){
            $itemHtml = $htmlTemplate;
            $itemHtml = str_replace("##heading##",$entityTypesArray[$i]['selector'],$itemHtml);
            $itemHtml = str_replace("##slug##",$entityTypesArray[$i]['slug'],$itemHtml);
            $itemHtml = str_replace("##description##",$entityTypesArray[$i]['descriptor'],$itemHtml);
            $itemHtml = str_replace("##linkCode##",$entityTypesArray[$i]['linkCode'],$itemHtml);
            $outPutHtml.=$itemHtml;
        }
        return $outPutHtml;
    }

    static public function buildPage(array $pageArray)                                         //k8s
    {
        $ht=file_get_contents("templates/{$pageArray['hddr_template']}");
        $ht.=file_get_contents("templates/{$pageArray['body_template']}");
        $ht.=file_get_contents("templates/{$pageArray['footer_template']}");
        {//style_include
            $value=file_get_contents("css/{$pageArray['style']}");
            $ht=str_replace("[[styleSheet]]",$value,$ht);
        }
        {//page level
            $pgArray=array('title','logo_txt');
            for ($i = 0; $i < count($pgArray); $i++) {
                $replace="[[{$pgArray[$i]}]]";
                $value=$pageArray[$pgArray[$i]];
                $ht=str_replace($replace,$value,$ht);
            }
        }
        {//statics
            $staticArray=$pageArray['static'];
            for ($i = 0; $i < count($staticArray); $i++) {
                $replace="[[{$staticArray[$i]['position_name']}]]";
                $value=$staticArray[$i]['ht'];
                $ht=str_replace($replace,$value,$ht);
            }
        }
        {//elements -- unconditionl
            $elementArray=$pageArray['elements'];
            for ($i = 0; $i < count($elementArray); $i++) {
                if($elementArray[$i]['conditional']==0){
                    $replace="[[{$elementArray[$i]['position_name']}]]";
                    $value=$elementArray[$i]['element_text'];
                    $ht=str_replace($replace,$value,$ht);
                }
            }
        }
        return $ht;
    }


    static public function handleAlerts($caller){
        if(isset($_GET['msg'])){
            $msg=$_GET['msg'];
            unset($_GET);
        }
        //alert from Session
        if(isset($_SESSION['alert'][$caller])){
            $msg=$_SESSION['alert'][$caller];
            unset($_SESSION['alert'][$caller]);
        }
        //alert from POST
        if(isset($_POST['alert'])){
            $msg=$_POST['alert'];
            unset($_POST['alert']);
        }
        return $msg;
    }
    static function evalGets(array $gets)
    {
        /*
            Must Have..
                [m] = message
            Optional
                [t] = topic
                [r] = response
        */
        $debug=getenv('debug');
        //<!--msgDate-->
        $gets['date']=date("D d M y");
        $errs=0;
        $plMsg="";
        //<!--MsgMessage-->
        if(!isset($gets['m'])){
            $gets['message']="Not sure why you are here.";
        }
        if(isset($gets['m'])){
            $gets['message']=$gets['m'];
        }
        //<!--msgTopic-->
        if(isset($gets['t'])){
            $gets['topic']=$gets['t'];
        }
        if(!isset($gets['t'])){
            $gets['topic']="No Topic";
        }
        //<!--MsgResponse-->
        if(isset($gets['r'])){
            $gets['response']="Use the menu or return to <a href=\"{$gets['r']}.php\"> {$gets['r']}</a>.";
        }
        if(!isset($gets['r'])){
            $gets['response']="Please Use the menu to navigate to the desired section.";
        }
        if($debug){
            $fl=$_SERVER['DOCUMENT_ROOT']."/gets.debug";
            if(file_exists($fl)){
                unlink($fl);
            }
            $contents="--<pre>\n";
            $contents.=print_r($gets,true);
            $contents.="\n</pre>\n";
            file_put_contents($fl,$contents);
            unset($contents);
        }
        $gets['errs']=$errs;
        $gets['plMsg']=$plMsg;
        return $gets;
    }
    static public function obMail(string $eMail)
    {
        $strArray = explode("@",$eMail);
        return "hidden@".$strArray[1];
    }

}