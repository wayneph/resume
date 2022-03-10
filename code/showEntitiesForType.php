<?php namespace presentation;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;
class Present extends BL
{
    private $myName="showEntitiesForType";
    public function __construct()
    {
        $this->trace[]="({$this->myName}/php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        parent::__construct($this->myName);
        parent::executeAPICalls();
        parent::getHTML(); // gives $this->html - also does replaces
        parent::validateAuthentication();
        $formArray=parent::getArrayElement($this->pageArray['page']['elements'], "position_name", $this->pageArray['touchForm']);
        $arr=parent::getArrayElement($this->pageArray['entityTypes'], "slug",$this->infoPathArray['slug']);
        $headingBit=$arr['selector'];
        $formHTML=$formArray['element_text'];
        $typesAccordionHTML=parent::buildEntityTypesAccordion($this->pageArray['entityTypes']);
        $postsDetailCondition=parent::buildMessages($this->pageArray['messages']);
        $typesAccordionHTML=parent::buildEntityListsAccordion();
        $pagination=parent::buildEntitiesPagination($this->myName);
        if($this->authenticated==0){
            header("Location: index.php",301);
            exit();
        }
        /* assign values to page */
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###touchOptions###",$this->pageArray['touchOptions'],$this->html);
        $this->html=str_replace("###postsDetailCondition###",$postsDetailCondition,$this->html);
        $this->html=str_replace("###feedback###",$formHTML,$this->html);
        $this->html=str_replace("###accordion###",$typesAccordionHTML,$this->html);
        $this->html=str_replace("###pagination###",$pagination,$this->html);
        $this->html=str_replace("###softMail###",$this->pageArray['emailText'],$this->html);
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###entityType###",$headingBit,$this->html);
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
        // $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        // $this->html=str_replace("###softMail###",$emailText,$this->html);
        // $this->html=str_replace("###postsDetailCondition###",$postsDetailCondition,$this->html);
        // $this->html=str_replace("###accordion###",$typesAccordionHTML,$this->html);
        // $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        // $this->html=str_replace("###softSubMenus###",$this->addedMenu,$this->html);
        //
        // $this->html=str_replace("###personName###",$person,$this->html);
        // $this->html=str_replace("###validUser###",$validUser,$this->html);
        // $this->html=str_replace("###emailAddress###",$email,$this->html);
        // $this->html=str_replace("###logoutText###",$logoutText,$this->html);
        // $dBug=parent::setDebugger($this->pageArray['cookie']);
        // $this->html=str_replace("###debugger###",$dBug,$this->html);
        // echo($this->html);
        // parent::writeLogs();
    }
}
new Present;