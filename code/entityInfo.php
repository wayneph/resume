<?php namespace presentation;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;
class Present extends BL
{
    private $myName="entityInfo";
    public function __construct()
    {
        $this->trace[]="({$this->myName}.php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        parent::__construct($this->myName);
        parent::executeAPICalls();
        parent::getHTML(); // gives $this->html - also does replaces
        parent::validateAuthentication();
        $formArray=parent::getArrayElement($this->pageArray['page']['elements'], "position_name", $this->pageArray['touchForm']);
        $formHTML=$formArray['element_text'];
        if(!isset($this->pageArray['information'][0]['entity'])){
            $this->trace[]="Error::<b>".$this->myName."</b>&rarr;Line::<b>".__LINE__."</b>::information <b>NotSet</b>";
            echo("<br>Array:pageArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($this->pageArray['information']); echo("</pre><hr>");exit();
            parent::pushToErrorPage("We are unable to find information for that Entity ({$this->myName})","data NotFound"); exit();
        }
        $heading="<b>{$this->pageArray['information'][0]['entity']}</b>";
        $typesAccordionHTML=parent::buildEntityInfoAccordion();
        $postsDetailCondition=parent::buildMessages($this->pageArray['messages']);
        //$pagination=parent::buildEntitiesPagination($this->myName);
        if($this->authenticated==0){
            parent::pushToErrorPage("We are unable to authenticate you at this time.<br>Yor PIN may have expired or Changed or Forgotten ({$this->myName})","data NotFound");
        }
        /* assign values to page */
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###touchOptions###",$this->pageArray['touchOptions'],$this->html);
        $this->html=str_replace("###postsDetailCondition###",$postsDetailCondition,$this->html);
        $this->html=str_replace("###feedback###",$formHTML,$this->html);
        $this->html=str_replace("###accordion###",$typesAccordionHTML,$this->html);
        $this->html=str_replace("###softMail###",$this->pageArray['emailText'],$this->html);
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###heading###",$heading,$this->html);
        $this->html=str_replace("###softSubMenus###",$this->addedMenu,$this->html);
        $this->html=str_replace("###login###",$this->pageArray['authMessage'],$this->html);
        $this->html=str_replace("###emailAddress###",$this->pageArray['email'],$this->html);
        $this->html=str_replace("###personName###",$this->pageArray['person'],$this->html);
        $this->html=str_replace("###validUser###",$this->pageArray['validUser'],$this->html);
        $this->html=str_replace("###logoutText###",$this->pageArray['logoutText'],$this->html);
        $this->html=str_replace("###myName###",$this->myName,$this->html);
        $this->html=str_replace("###scriptAdded###","",$this->html);
        $dBug=parent::setDebugger($this->pageArray['cookie']);
        $this->html=str_replace("###debugger###",$dBug,$this->html);
        echo($this->html);
        parent::writeLogs();
    }
}
new Present;