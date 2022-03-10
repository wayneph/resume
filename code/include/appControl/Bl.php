<?php namespace BL;
require 'vendor/autoload.php';
use GuzzleHttp\Client as GUZ;
use GuzzleHttp\Exception\ClientException;

use function GuzzleHttp\Psr7\_parse_message;

class BL
{
    /* privates*/
    private $apiArray;
    private $myName="BL";
    /*control vars */
    public $pageArray;
    public $apiUserArray;
    public $infoPathArray=array();
    public $addedMenu;
    public $html;
    public $addedAPIcalls=array();
    public $ToDo=array();
    public $trace;
    public $cookiePin="";
    public $moreEntitiesExist;
    public $authenticated=0;
    public function __construct(string $pageName)
    {
        $this->getEnv();
        $this->evalCookie();
        if(isset($_SERVER['PATH_INFO'])){
            $this->getInfoPathArray($_SERVER['PATH_INFO']);
        }
        $this->pageArray['apiVars']['getPage']=$pageName;
        $this->pageArray['touchForm']="loginForm";
        $this->pageArray['touchOptions']="Register & Login Here";
        $this->pageArray['authMessage']="Register or Login";
        $this->pageArray['apiCalls'][]='getPage';
        //$this->pageArray['apiCalls'][]='getAllEntityTypes';
        //$this->pageArray['apiCalls'][]='getMessages';
        /* page specifics */
        switch ($pageName) {
            case "showEntitiesForType":
                $this->pageArray['apiCalls'][]='getEntitiesForType';
                break;
            case "entityInfo":
                $this->pageArray['apiCalls'][]='getEntityInfo';
                break;
            break;
            case "entityRelate":
                $this->pageArray['apiCalls'][]='getEntityRelate';
                break;
        }
        $this->pageArray['emailText']="UUM-<input name=\"pstMail\" placeholder=\"Email\" type=\"text\" value=\"###emailAddress###\">";
        $this->pageArray['email']="UUM-eMail";
        $this->pageArray['person']="UUM-Name";
        $this->pageArray['validUser']="No Login -or- Registration";
        $this->pageArray['logoutText']="";
        if(isset($this->pageArray['cookie']['cHash'])){
            $this->pageArray['apiCalls'][]='setUserLogin';
            $this->cookiePin=$this->pageArray['cookie']['cPIN'];
            $this->pageArray['apiCalls'][]='setUserLogin';
        }
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
    }
    public function buildEntitiesPagination(string $caller)
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $defaultSize=getenv("defaultPageSize");
        $slug=$this->infoPathArray['slug'];
        $page=(int)$this->infoPathArray['page']+1;
        if($this->moreEntitiesExist==1){
            $retHTML="<a href=\"$caller.php/$slug/$page/$defaultSize\">Next $defaultSize ###entityType###&nbsp;&rarr;</a>";
            $slug=$this->infoPathArray['slug'];
            if($this->infoPathArray['page']>1){
                $retHTML.="<br><a href=\"$caller.php/$slug/1/$defaultSize\">&larr;&nbsp;First $defaultSize ###entityType###</a>";
            }
            return $retHTML;
        }
        return "<a href=\"$caller.php/$slug/1/$defaultSize\">No more ###entityType### Displayed</a>";;
    }
    public function buildEntityInfoAccordion()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $info=$this->pageArray['information'][0]['info'];
        $outPutHtml="<!-- plGenerated::".__METHOD__."::line::".__LINE__."  -->";
        $htmlTemplate="<p>##infoInternal##</p>\n";
        for($i=0;$i<count($info);$i++){
            $itemHtml = $htmlTemplate;
            $infoInternal="{$info[$i]['infoCategory']}&nbsp;&rarr;&nbsp;<b>{$info[$i]['info']}</b>";
            $itemHtml = str_replace("##infoInternal##",$infoInternal,$itemHtml);
            $outPutHtml.=$itemHtml;
        }
        $outPutHtml.="<a href=\"entityRelate.php/{$this->pageArray['information'][0]['slug']}\" class=\"button\">
            See Relationships for {$this->pageArray['information'][0]['entity']}</a>";
        $outPutHtml.="\n<!--EOF(EntityInformation)-->\n";
        return $outPutHtml;
    }
    public function buildEntityListsAccordion()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $entitiesList=$this->pageArray['entitiesList'];
        $outPutHtml="<!-- plGenerated::".__METHOD__."::line::".__LINE__."  -->";
        $htmlTemplate="\n<button class=\"accordion\">##heading## Information</button>\n
        <div class=\"panel\">\n
          <p>\n
            <br>
            <ul>\n
                <li>\n
                    See <b>Information Items</b> for &rarr; <a href=\"entityInfo.php/##slug##\">##heading##</a>\n
                </li>
                <li>
                    See <b>Related Items</b> to or from &rarr; <a href=\"entityRelate.php/##slug##\">##heading##</a>\n
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
    public function buildEntityRelateAccordion()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $fromRelate=$this->pageArray['entityRelate'][0]['relatedFromEntities'];
        $relatesTo=$this->pageArray['entityRelate'][0]['relatedEntities'];
        $htmlTemplate="<p>##infoInternal##</p>\n";
        $outPutHtml="\n<!--SOF(RelatedInfo)-->\n";
        if(count($fromRelate)>0){
            for($i=0;$i<count($fromRelate);$i++){
                $itemHtml = $htmlTemplate;
                $infoFromRelate="
                    <b>{$fromRelate[$i]['slaveEntityName']}</b>&nbsp;&rarr;{$fromRelate[$i]['relationship']}
                    &nbsp;&rarr;&nbsp;<b>{$this->pageArray['entityRelate'][0]['entity']}</b>
                    <br>
                    {$fromRelate[$i]['notes']}";
                $itemHtml = str_replace("##infoInternal##",$infoFromRelate,$itemHtml);
                $outPutHtml.=$itemHtml;
            }
        }
        if(count($relatesTo)>0){
            for($i=0;$i<count($relatesTo);$i++){
                $itemHtml = $htmlTemplate;
                $infoToRelate="<b>{$this->pageArray['entityRelate'][0]['entity']}</b>&nbsp;&rarr;&nbsp;{$relatesTo[$i]['relationship']}
                    &nbsp;&rarr;&nbsp;<b>{$relatesTo[$i]['slaveEntityName']}</b><br>{$relatesTo[$i]['notes']}";
                $itemHtml = str_replace("##infoInternal##",$infoToRelate,$itemHtml);
                $outPutHtml.=$itemHtml;
            }
        }
        $outPutHtml.="<a href=\"entityInfo.php/{$this->pageArray['entityRelate'][0]['slug']}\" class=\"button\">
            See Information for {$this->pageArray['entityRelate'][0]['entity']}</a>";
        $outPutHtml.="\n<!--EOF(RelatedInfo)-->\n";
        return $outPutHtml;
    }
    public function buildEntityTypesAccordion($entityTypesArray)
    {
        $outPutHtml="<!-- plGenerated::".__METHOD__."::line::".__LINE__."  -->";
        $htmlTemplate="\n<button class=\"accordion\">##heading##</button>\n
        <div class=\"panel\">\n
          <p>##description##\n
          <br><br>\n
          &nbsp;&minusb;&nbsp;<a href=\"showEntitiesForType.php/##slug##\">List Entities for <strong>##heading##(s)</strong> here</a></b>.\n
          </p>\n
        </div>\n";
        for($i=0;$i<count($entityTypesArray);$i++){
            $itemHtml = $htmlTemplate;
            $itemHtml = str_replace("##heading##",$entityTypesArray[$i]['selector'],$itemHtml);
            $itemHtml = str_replace("##slug##",$entityTypesArray[$i]['slug'],$itemHtml);
            $itemHtml = str_replace("##description##",$entityTypesArray[$i]['descriptor'],$itemHtml);
            $outPutHtml.=$itemHtml;
        }
        return $outPutHtml;
    }
    private function buildEntityTypesMenu()
    {
        $array=$this->pageArray['entityTypes'];
        $menuLeader=getenv('siteMenuLeader');
        $itemsPerPage=getenv('defaultPageSize');
        $this->addedMenu="\n<li><a href=\"listEntityTypes.php\"><span>$menuLeader<strong>&nbsp;&isin;</strong></span></a>\n<ul>\n";
        for($a=0;$a<count($array);$a++){
            $this->pageArray['getEntityTypes'][$a]['linkCode']=$array[$a]['slug']."/{$array[$a]['slug']}/1/$itemsPerPage";
            $this->addedMenu.="\n<li><a href=\"showEntitiesForType.php/{$array[$a]['slug']}/1/$itemsPerPage\">&#8714;&nbsp;{$array[$a]['selector']}</a></li>\n";
        }
        $this->addedMenu.="\n</ul>\n</li>\n";
    }
    public function buildMessages(array $messages)
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $returnString="";
        $template="
        <li>
            <article class=\"box excerpt\">
                <header>
                    <span class=\"date\">##monthYearPosted##</span>
                    <h2><a href=\"#\"><b>##headingPosted##</b></a></h2>
                </header>
                <p>##msgPosted##</p>
            </article>

        </li>";
        for($d=0;$d<count($messages);$d++){
            $tmp=$template;
            $site=getenv("siteSlug");
            $dt=strtotime($messages[$d]['created']);
            $dateSetting = date("M y",$dt);
            $commsBy=str_replace("@",rand(1,9),$messages[$d]['comms_by_slug']);
            $commsBy=str_replace(".",rand(1,9),$commsBy);
            $commsBy=str_replace("mail",rand(1,99),$commsBy);
            $commsBy=str_replace("com",rand(1,99),$commsBy);
            $msg=$messages[$d]['comms_text'];
            $msg.="<br><br><i> Posted by <b>$commsBy</b> on {$messages[$d]['created']}";
            $msg.="<br>Vetted by <b>$site</b> on {$messages[$d]['updated']}";
            $msg.="<br>ref:{$messages[$d]['slug']}</i>";
            $tmp=str_replace("##monthYearPosted##",$dateSetting,$tmp);
            $tmp=str_replace("##headingPosted##",$messages[$d]['comms_topic'],$tmp);
            $tmp=str_replace("##msgPosted##",$msg,$tmp);
            $returnString.="\n$tmp\n";
        }
        return $returnString;
    }

    private function deleteCookie()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $this->pageArray['cookie']['deleted']=1;
        $cookieName=getenv("siteSlug")."-pwa";
        setcookie($cookieName, json_encode($this->pageArray['cookie']), time() + (-3* 86400 * 30), "/");
        return;
    }

    public function executeAPICalls()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        //echo("<br>Array:pageArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($this->pageArray['apiCalls']); echo("</pre><hr>");
        $this->apiArray=array();
        $this->apiArray['apiExecuteStart']=time();
        $this->apiArray['caller']=getenv("api-host");
        $this->apiArray['siteSlug']=getenv("siteSlug");
        $this->apiArray['host']=getenv("api-host");
        $this->apiArray['headersIn']['api-key']=getenv("api-key");
        $this->apiArray['userUri']=$this->apiArray['host'].getenv("user-api-area");
        $this->apiArray['sitesUri']=$this->apiArray['host'].getenv("sites-api-area");
        $this->apiArray['entitiesUri']=$this->apiArray['host'].getenv("entities-api-area");
        $this->apiArray['entitiesInvocation']=$this->apiArray['host'].getenv("entities-api-area");
        $this->apiArray['mailUri']=$this->apiArray['host'].getenv("mail-api-area");
        $this->apiArray['userName']=getenv("api-user-name");
        $this->apiArray['userPin']=getenv("api-user-pin");
        $response=$this->apiLogInOwner();
        $response=$this->apiGetOwnerUser();
        $cnt=count($this->pageArray['apiCalls']);
        for($a=0;$a<$cnt;$a++){
            switch ($this->pageArray['apiCalls'][$a]){
                case 'getEntityRelate':
                    $this->apiLogInOwner();
                    $response=$this->apiGetEntityRelate();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['entityRelate']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'getEntityInfo':
                    $this->apiLogInOwner();
                    $response=$this->apiGetEntityInfo();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['information']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'getPage':
                    $response=$this->apiGetPage();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['page']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'getAllEntityTypes':
                    $this->apiLogInOwner();
                    $response=$this->apiGetAllEntityTypes();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['entityTypes']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'setUserLogin':
                    if(isset($this->pageArray['cookie']['cHash'])){
                        $this->apiLogInOwner();
                        $response=$this->apiFindUser($this->pageArray['cookie']['cHash']);
                        $data=json_decode($response['body'],true);
                        $this->pageArray['appUser']=$data['data'];
                        $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                        $this->setUserLoginVars();
                    }
                    break;
                case 'getMessages':
                    $this->apiLogInOwner();
                    $response=$this->apiGetMessages();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['messages']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'getEntitiesForType':
                    $this->apiLogInOwner();
                    $response=$this->apiGetEntitiesForType();
                    $data=json_decode($response['body'],true);
                    $this->moreEntitiesExist=$data['moreRecordsExist'];
                    $this->pageArray['entitiesList']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;
                case 'setTouchData':
                    if(!isset($this->pageArray['inputs']['posts'])){
                        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
                    }
                    $response=$this->apiSetTouchData();
                    $data=json_decode($response['body'],true);
                    $this->pageArray['comms']=$data['data'];
                    $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
                    break;

                default:
                $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
            }
        }
    }

    private function apiFindUser()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=microtime(true);
        if(!isset($this->pageArray['cookie']['cHash'])){
            $this->pushToErrorPage(__METHOD__."::".__LINE__,'cookie not set','this is an anomaly','Line::'.__LINE__);
        }
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."::".
            $this->apiArray['userUri']."/find/{$this->pageArray['cookie']['cHash']}</b>";
        $this->apiArray['apiFindUser']['url']=$this->apiArray['userUri']."/find/{$this->pageArray['cookie']['cHash']}";
        try{
            $r = $client->request("GET", $this->apiArray['apiFindUser']['url'],['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for the User was found","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }

    private function apiGetAllEntityTypes()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $r = $client->request("GET", $this->apiArray['entitiesUri']."/entityTypes",['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiGetAllEntityTypes']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=json_decode($response['body'],true);
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for For Entity types","This has been logged");
            }
            $this->pushToErrorPage(__METHOD__."::".__LINE__,$endArray);
        }
    }
    private function apiGetEntityInfo()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        if(!isset($this->infoPathArray['slug'])){
            $this->pushToErrorPage("Entity code not specified","Application call without required parameter");
        }
        $slug=$this->infoPathArray['slug'];
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $link="{$this->apiArray['entitiesUri']}/entity/$slug/info";
            $this->trace[]="API Getting Entity Information::Bl::".__LINE__." LinkCalled: &rarr;$link";
            $r = $client->request("GET", $link,['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiGetEntitiesForType']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xBlMethodCode']=__LINE__;
            $endArray=json_decode($response['body'],true);
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for $slug","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }

    private function apiGetEntityRelate()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        if(!isset($this->infoPathArray['slug'])){
            $this->pushToErrorPage("No Entity Type specified","Incorrect Call");
        }
        $slug=$this->infoPathArray['slug'];
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $link="{$this->apiArray['entitiesUri']}/entity/$slug/relate";
            $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>LinkCalled:$link";
            $r = $client->request("GET", $link,['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiGetEntitiesForType']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xBlMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for $slug","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }

    private function apiGetEntitiesForType()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        // this array is called & from end checked
        if(!isset($this->infoPathArray['slug'])){
            $this->pushToErrorPage("No Entity Type specified","Incorrect Call");
        }
        $slug=$this->infoPathArray['slug'];
        $page=$this->infoPathArray['page'];
        $size=$this->infoPathArray['size'];
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $r = $client->request("GET", $this->apiArray['entitiesUri']."/type/$slug/?page=$page&size=$size",['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiGetEntitiesForType']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xBlMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for $slug","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }
    private function apiGetMessages(string $pagination="1/15")
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $arrayPagination=explode("/",$pagination);
        $page=$arrayPagination[0];
        $size=$arrayPagination[1];
        $slug=getenv('siteSlug');
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $r = $client->request("GET", $this->apiArray['userUri']."/comms/$slug/?page=$page&size=$size",['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['apiCalls']['apiGetMessages']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for $slug","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }

    private function apiGetOwnerUser()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=time();
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        $response['callWas']=__METHOD__;
        try{
            $r = $client->request("GET", $this->apiArray['userUri']."/show", ['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiGetOwnerUser']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }

    private function apiGetPage()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        try{
            $r = $client->request("GET", $this->apiArray['sitesUri']."/site/{$this->apiArray['siteSlug']}/{$this->pageArray['apiVars']['getPage']}", ['http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['apiCalls']['apiGetPage']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for the context","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }
    private function apiLogInOwner()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=microtime(true);
        $loginJsonArray['user_id']=$this->apiArray['userName'];
        $loginJsonArray['pin']=$this->apiArray['userPin'];
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        $bodyInJson=json_encode($loginJsonArray);
        try{
            $r = $client->request("POST", $this->apiArray['userUri']."/login", [
                'body' => $bodyInJson,'http_errors' => true
            ]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['headersIn']['usageToken']=$response['headersOut']['token'][0];
            $this->apiArray['apiCalls']['apiLogInOwner']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("No Information Data for API Owner Login","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }
    private function apiSetTouchData()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $started=microtime(true);
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        $topic=getenv('siteSlug')."-".$this->pageArray['inputs']['posts']['pstCategory'];
        $this->apiArray['postTouchStart']=time();
        $commitArray['site_slug']=getenv('siteSlug');
        $commitArray['comms_by_slug']=$this->pageArray['inputs']['posts']['pstMail'];
        $commitArray['source_slug']=$this->pageArray['inputs']['posts']['pstSource'];
        $commitArray['for_slug']=getenv('api-user-name');
        $commitArray['comms_log']="\nTouch Created @ ".date("Y-n-d H:i:s");;
        $commitArray['comms_topic']=$topic;
        $commitArray['comms_text']=$this->pageArray['inputs']['posts']['pstMessage'];
        $bodyInJson=json_encode($commitArray);
        try{
            $r = $client->request("POST", $this->apiArray['userUri']."/comms",['body' => $bodyInJson, 'http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
            $this->apiArray['apiCalls']['apiSetTouchData']['executionTime']=microtime(true)-$started;
            return $response;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
            $endArray=$response['body'];
            if($endArray['status']==404){
                $this->pushToErrorPage("Could not write the data","This has been logged");
            }
            $this->pushToErrorPage("API error - The api May be down","::".__METHOD__."&nbsp;was called and could not complete");
        }
    }
    public function deEncrypt(string $data)
    {
        for ($i = 1000; $i < 1010; $i++) {
            $extract=md5($i);
            $data=str_replace($extract,"",$data);
        }
        return $data;
    }
    function enCryp(string $data, $leaveData=0)
    {
        $randPos1=rand(1000,1007);
        $randPos2=$randPos1+1;
        $randPos3=$randPos1+2;
        if($leaveData==0){
            $data=md5($data);
        }
        $x=rand(3,5);
        if(($x % 2) ==0)
        {
            return md5($randPos2).$data.md5($randPos3);
        }
        if(($x % 3) ==0)
        {
            return md5($randPos3).md5($randPos1).$data;
        }
        if(($x % 5) ==0)
        {
            return md5($randPos2).md5($randPos3).$data.md5($randPos1);
        }
        return md5($randPos3).md5($randPos1).$data.md5($randPos2);
    }
    private function evalCookie()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $this->pageArray['cookie']=array();
        $cookieName=getenv("siteSlug")."-pwa";
        if(isset($_COOKIE[$cookieName]))
        {
            $this->pageArray['cookie']=json_decode($_COOKIE[$cookieName], true);
        }
        return;
    }

    public function evalInputs()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $errors=0;
        if(!isset($_POST['pstSource'])){
            $errorArray['postSource']="missing - or not set";
            $errorArray['line']=__LINE__;
            $outIt=print_r($errorArray,true);
            $this->pushToErrorPage(__METHOD__."::".__LINE__,$outIt);
        }
        $this->pageArray['inputs']['type']='post';
        $posts=$_POST;
        unset($_POST);
        if(!isset($posts['pstType'])){
            $posts['pstType']="No posting Origen";
        }
        $posts['pstMailValid']=1;
        if(!isset($posts['pstMail'])){
            $posts['pstMail']="Not set";
            $posts['pstMailValid']=0;
        }
        if (!filter_var($posts['pstMail'], FILTER_VALIDATE_EMAIL)) {
            if(substr($posts['pstMail'],0,5)!='eMAil'){
                $posts['pstMail']="Invalid email captured";
                $posts['pstMailValid']=0;
            }
        }
        $posts['eMailDisplay']=$this->setEmailDisplay($posts['pstMail']);
        $posts['date']=date("D d M y");
        $category="<u>Regarding:</u>&rarr;<b>".$posts['pstCategory']."</b>";
        if(!isset($posts['pstMessage'])){
            $posts['pstMessage']="No Message";
        }
        if(strlen($posts['pstMessage'])<2){
            $posts['pstMessage']="No Message";
        }
        $posts['message']=$posts['pstMessage']."\n\nFrom\n".$posts['message']=$posts['pstFrom'];
        $posts['topic']="UnKnown (pstType)";
        if(isset($posts['pstType'])){
            $posts['topic']=$category." on ".$posts['pstType'];
        }
        $posts['response']="";
        $posts['response'].="<br><br>Thank you <b>{$posts['pstFrom']}</b> for the <b>touch !</b><br>";
        if(isset($posts['pstSource'])){
            $posts['response'].="<hr><br>Use the menu or return to <a href=\"{$posts['pstSource']}.php\"> {$posts['pstSource']}</a>.";
        }
        $posts['response'].="<br>An email was attempted to an address like::<b>{$posts['eMailDisplay']}</b>::<br>";
        if(strlen($posts['pstSource'])<2){
            $posts['response']="Please Use the menu to navigate to the desired section.";
        }
        $this->pageArray['inputs']['posts']=$posts;
        $this->pageArray['inputs']['posts']['cBlLine']=__LINE__;
        //echo("<br>Array:pageArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($this->pageArray['inputs']['posts']); echo("</pre><hr>");
        $this->writeCookie($this->pageArray['inputs']['posts']);
        $this->evalCookie();
        $this->pageArray['postSwitchElements']['date']=date("D d M Y");
        if(isset($this->pageArray['cookie']['cSiteValidation'])){
            if($this->pageArray['cookie']['cSiteValidation']==1){
                $this->pageArray['postSwitchElements']['specificHeader']="<b>Registered eMail</b>";
                $this->pageArray['postSwitchElements']['topic']='Authentication';
                $this->pageArray['postSwitchElements']['message']='Authenticated - Thank you &nbsp;&nbsp;&#128524;';
                $this->pageArray['postSwitchElements']['response']='Navigate  <a href ="index.php">Home</a>.';
                $this->pageArray['postSwitchElements']['heading']='Touch Acknowledged';
                return;
            }
        }
        $this->pageArray['postSwitchElements']['specificHeader']="<b>eMail Not Validated</b>";
        $this->pageArray['postSwitchElements']['topic']='Authentication Pending';
        $this->pageArray['postSwitchElements']['message']='Thank you &nbsp;&nbsp;&#128524;';
        $this->pageArray['postSwitchElements']['response']='Navigate  <a href ="index.php">Home</a>.';
        $this->pageArray['postSwitchElements']['heading']='Touch Acknowledged';
        return;
    }
    public function getArrayElement(array $searchArray, string $searchField, string $searchText)
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        for($n=0;$n<count($searchArray);$n++){
            $elementArray=$searchArray[$n];
            if($elementArray[$searchField]==$searchText){
                return $elementArray;
            }
        }
        return null;
    }
    public function getEnv(){
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $file=$_SERVER['DOCUMENT_ROOT']."/.env";
        $contents=file_get_contents($file);
        $arrayContents=explode("\n",$contents);
        foreach ($arrayContents as $key => $value) {
            $value=trim($value);
            $findEq=strpos($value,"=");
            if($findEq>0){
                $lineItemArray=explode("=",$value);
                $putEnvStr=trim($lineItemArray[0])."=".trim($lineItemArray[1]);
                putenv($putEnvStr);
            }
        }
        date_default_timezone_set(getenv('tz'));
        return;
    }

    private function getInfoPathArray(string $path)
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $pathExploded=explode("/",$path);
        //echo("<br>Array:pageArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($pathExploded); echo("</pre><hr>");
        $defaultPageSize=getenv("defaultPageSize");
        $this->infoPathArray['slug']=$pathExploded[1];
        $this->infoPathArray['page']=1;
        $this->infoPathArray['size']=$defaultPageSize;
        if(isset($pathExploded[2])){
            $this->infoPathArray['page']=$pathExploded[2];
        }
        if(isset($pathExploded[3])){
            $this->infoPathArray['size']=$pathExploded[3];
        }
    }

    public function getHTML()
    {
        $this->html=file_get_contents("templates/{$this->pageArray['page']['page']['hddr_template']}");
        $this->html.=file_get_contents("templates/{$this->pageArray['page']['page']['body_template']}");
        $this->html.=file_get_contents("templates/{$this->pageArray['page']['page']['footer_template']}");
        $this->replaceSiteStatics();
        $this->replacePageElements();
        return;
    }

    public function pushToErrorPage(string $method="none", string $type="data Inconsistency")
    {
        $this->trace['message']="$method ~<b>$type</b>~";
        $this->writeLogs($method,$type); // 1 = Unique Log
        //domain
        $domain=getenv('domain');
        $url="$domain/inconsistency.php";
        header("Location: $url",301);
        exit();
    }
    private function xxsendMailAdmin(string $msg, array $addMessageArray)
    {
        if(count($addMessageArray)){
            $msg.="<pre>";
            $msg.=print_r($addMessageArray,true);
            $msg.="</pre>";
        }
        $from=getenv('api-user-name');
        $site=getenv('siteSlug');
        $inArray['to']=getenv('adminMail');
        $inArray['to_person']=$from;
        $inArray['subject']="AdminMail - $site";
        $inArray['body']="
            {$site} message for {$from},
            <br><br>
            {$msg}";
        $this->apiArray['postMailAdmin']=time();
        $client = new GUZ([
            'headers' => $this->apiArray["headersIn"]
        ]);
        $bodyInJson=json_encode($inArray);
        try{
            $r = $client->request("POST", $this->apiArray['mailUri']."/send",['body' => $bodyInJson, 'http_errors' => true]);
            $response['body']=$r->getBody()->getContents();
            $response['status']=$r->getStatusCode();
            $response['headersOut']=$r->getHeaders();
            $response['20xMethodCode']=__LINE__;
        } catch (ClientException $e) {
            $exception = $e->getResponse();
            $response['body'] = $exception->getBody()->getContents();
            $response['status'] = $exception->getStatusCode();
            $response['headersOut'] = $exception->getHeaders();
            $response['40xMethodCode']=__LINE__;
        }
        $this->apiArray['postMailAdmin']=time();
        return;
    }
    private function replacePageElements()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $elementsArray=$this->pageArray['page']['elements'];
        usort($elementsArray, function($a, $b) {
            return $a['seq'] <=> $b['seq'];
        });
        for($e=0;$e<count($elementsArray);$e++){
            $replaceMe=$elementsArray[$e]['position_name'];
            if($elementsArray[$e]['conditional']==0){
                $replaceWith=$elementsArray[$e]['element_text'];
            }
            if($elementsArray[$e]['conditional']==1){
                $caseArray=explode("|",$elementsArray[$e]['element_text']);
                switch ($caseArray[0]) {
                    case "rand":
                        $rangeSplit=explode("~",$caseArray[1]);
                        $replaceWith=rand((int)$rangeSplit[0],(int)$rangeSplit[1]);
                        break;
                    case "arrayLimitedOutput":
                        $template=$caseArray[3];
                        $replaceWith="";
                        for($r=0;$r<$caseArray[2];$r++){
                            $replaceArray=$this->pageArray['getMessages']['data'][$r];  //buggggggg
                            $replaceWith.=$template;
                            foreach($replaceArray as $key => $value) {
                                $replaceWith=str_replace("###$key###",$value,$replaceWith);
                            }
                        }
                        break;
                    default:
                        $this->pushToErrorPage(__METHOD__."..".__LINE__,$elementsArray[$e]);
                        break;
                }
            }
            $this->html=str_replace("###$replaceMe###",$replaceWith,$this->html);
        }
        return;
    }

    private function replaceSiteStatics()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $staticsArray=$this->pageArray['page']['static'];
        for($e=0;$e<count($staticsArray);$e++){
            $replaceMe=$staticsArray[$e]['position_name'];
            $replaceWith=$staticsArray[$e]['ht'];
            $this->html=str_replace("###$replaceMe###",$replaceWith,$this->html);
        }
        return;
    }

    private function setUserLoginVars()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        if(!isset($this->pageArray['appUser'])){
           $this->trace[]="ToDo::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
           $this->pushToErrorPage(__METHOD__."::".__LINE__, "App user Not set");

        }
        if(($this->pageArray['appUser']['status']<4) AND ($this->pageArray['appUser']['status']>0)){
            /*  1=skunks access
                2=PWA only access
            */
            $this->pageArray['appUser']['action']=json_decode($this->pageArray['appUser']['action_json'],true);
            $findFor=getenv('siteSlug');
            $accessArray=array();
            for($i=0;$i<count($this->pageArray['appUser']['action']);$i++){
                if($this->pageArray['appUser']['action'][$i]['slug']==$findFor){
                    $accessArray=$this->pageArray['appUser']['action'][$i];
                }
            }
            $isPin=md5($accessArray['PIN']);
            $wasPin=$this->deEncrypt($this->cookiePin);
            if($isPin!=$wasPin){
                $this->authenticated=0;
                $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>::Pin Changed";
                $this->deleteCookie();
                return;
            }
            $this->authenticated=1;
            $value['cUserName']=$this->pageArray['appUser']['fullname'];
            //$value['cUserStatus']=$this->pageArray['appUser']['status'];
            if(count($accessArray)>0){
                $value['cSiteAccess']=$accessArray['access'];
                $value['cSiteValidation']=$accessArray['validate'];
            }
            $value['calledLine']=__LINE__;
            $this->writeCookie($value);
        }
        return;
    }
    public function setDebugger(array $array)
    {
        $debug=getenv('debug');
        $returnContents="<center> ~~~~~~~~ </center>";
        if($debug){
            $returnContents=print_r($array,true);
        }
        $returnContents.="<br>.. Auth = {$this->authenticated}";
        return $returnContents;
    }
    public function setEmailDisplay(string $email)
    {
        if(strpos("@",$email)>1){
            $splits=explode("@",$email);
            return "UUM-**@".$splits[1];
        }
        return "$email-notValid";
    }

    public function validateAuthentication()
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        if($this->authenticated==1){
            $this->buildEntityTypesMenu();
            $this->pageArray['validUser']="Valid App User";
            $this->pageArray['logoutText']="<br><a href=\"logout.php\">Remove Login</a>";
            $this->pageArray['touchOptions']="Leave a comment or suggestion here";
            $this->pageArray['touchForm']="contactForm";
            $this->pageArray['authMessage']="Registered Member";
        }
        return;
    }

    public function writeCookie(array $values)
    {
        $this->trace[]="method::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b>";
        $wantCookieArray=array('cHash','cUserName','cSiteAccess','cSiteValidation'.'cPIN');
        $cookieName=getenv("siteSlug")."-pwa";
        $cookieValues=array();
        if(isset($_COOKIE[$cookieName]))
        {
            $cookieValues=json_decode($_COOKIE[$cookieName], true);
        }
        if(isset($values['action'])){
            $slug=getenv("siteSlug");
            $pin=null;
            for($i=0;$i<count($values['action']);$i++){
                if($values['action'][$i]['slug']==$slug){
                    $pin=$values['action'][$i]['PIN'];
                }
            }
            if(!is_null($pin)){
                $cookieValues['cPIN']=$this->enCryp($pin);
            }
        }
        for($n=0;$n<count($wantCookieArray);$n++){
            if(isset($values[$wantCookieArray[$n]])){
                $cookieValues[$wantCookieArray[$n]]=$values[$wantCookieArray[$n]];
            }
        }
        $cookieValues['cCreatedAt']=date("Y-m-d H:i:s");
        if(isset($values['pstFrom'])){  //we have inputs
            if(isset($values['pstMail'])){
                if($values['pstMailValid']==1){
                    $cookieValues['cHash']=$this->enCryp($values['pstMail']);
                }
            }
            if(isset($values['pstPIN'])){
                if($values['pstMailValid']==1){
                    $cookieValues['cPIN']=$this->enCryp($values['pstPIN']);
                }
            }
            if(isset($valuesArray['pstFrom'])){
                $cookieValues['cUserName']=$values['pstFrom'];
            }
        }
        setcookie($cookieName, json_encode($cookieValues), time() + (3* 86400 * 30), "/");
        return;
    }
    public function writeLogs()
    {
        $debug=getenv('debug');
        $dte=date("YmdHis");
        $flAdded="$dte-page.log.json";
        $fl="page.log.json";
        $outArray['calls']=$this->trace;
        $outArray['page']=$this->pageArray;
        $outArray['toDos']=$this->ToDo;
        $contents= json_encode($outArray);
        file_put_contents($fl,$contents);
        if($debug==0){
            file_put_contents($flAdded, $contents);
        }
        return;
    }

    private function xxxpostTouchMail(array $touchArray,string $jsonReg)
    {
        // $checkArray=json_decode($jsonReg, true);
        // // echo("<br>Array:checkArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($checkArray); echo("</pre><hr>");
        // // exit();
        // $linkBits=rand(1000,9999)."rg";
        // if(isset($checkArray['data']['comms_ref'])){
        //     $linkBits=substr($checkArray['data']['comms_ref'],0,6);
        //     $linkBits.="rg"; //used for register url key
        // }
        // $from=getenv('api-user-name');
        // $site=getenv('siteName');
        // $linkBack=getenv('domain');
        // $inArray['to']=$touchArray['pstMail'];
        // $inArray['to_person']=$touchArray['pstFrom'];
        // $inArray['subject']=$touchArray['pstType']."-".$touchArray['pstType'];
        // $inArray['body']="
        //     Welcome to {$site} {$touchArray['pstFrom']},
        //     <br><br>
        //     Thank you for the touch!. This is your reference: <b>{$linkBits}</b>.
        //     <br><br>
        //     Your Touch Message:
        //     <br><br>
        //     <b>{$touchArray['pstMessage']}</b>
        //     <br><br>
        //     Regards,
        //     <br><br>
        //     $from
        //     <br>";
        // $this->apiArray['postMailStart']=time();
        // $client = new GUZ([
        //     'headers' => $this->apiArray["headersIn"]
        // ]);
        // $bodyInJson=json_encode($inArray);
        // try{
        //     $r = $client->request("POST", $this->apiArray['mailUri']."/send",['body' => $bodyInJson, 'http_errors' => true]);
        //     $response['body']=$r->getBody()->getContents();
        //     $response['status']=$r->getStatusCode();
        //     $response['headersOut']=$r->getHeaders();
        //     $response['20xMethodCode']=__LINE__;
        // } catch (ClientException $e) {
        //     $exception = $e->getResponse();
        //     $response['body']['error'][] = "error:\n";
        //     $response['body']['error'][] = $bodyInJson;
        //     $response['status'] = $exception->getStatusCode();
        //     $response['headersOut'] = $exception->getHeaders();
        //     $response['40xMethodCode']=__LINE__;
        // }
        // $this->apiArray['postMailEnd']=time();
        // return;
    }
}
