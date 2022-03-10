<?php namespace presentation;
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;
class Present extends BL
{
    private $myName="index";
    public function __construct()
    {
        $this->trace[]="({$this->myName}/php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        //$addedBit="";
        parent::__construct($this->myName);
        parent::executeAPICalls();
        // echo("<br>Array:pageArray(".__LINE__."({$this->myName}))<br><pre>"); print_r($this->pageArray); echo("</pre><hr>");
        // exit();
        parent::getHTML(); // gives $this->html - also does replaces
        // parent::validateAuthentication();
        // $formArray=parent::getArrayElement($this->pageArray['page']['elements'], "position_name", $this->pageArray['touchForm']);
        // $formHTML=$formArray['element_text'];
        /* assign values to page */
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###touchOptions###",$this->pageArray['touchOptions'],$this->html);
        //$this->html=str_replace("###theAppropriateForm###",$formHTML,$this->html);
        $this->html=str_replace("###softMail###",$this->pageArray['emailText'],$this->html);
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###softSubMenus###",$this->addedMenu,$this->html);
        $this->html=str_replace("###login###",$this->pageArray['authMessage'],$this->html);
        //$this->html=str_replace("###emailAddress###",$this->pageArray['email'],$this->html);
        $this->html=str_replace("###personName###",$this->pageArray['person'],$this->html);
        $this->html=str_replace("###validUser###",$this->pageArray['validUser'],$this->html);
        $this->html=str_replace("###logoutText###",$this->pageArray['logoutText'],$this->html);
        $this->html=str_replace("###myName###",$this->myName,$this->html);
        $this->html=str_replace("###scriptAdded###","",$this->html);
        // $dBug=parent::setDebugger($this->pageArray['cookie']);
        // $this->html=str_replace("###debugger###",$dBug,$this->html);
        echo($this->html);
        parent::writeLogs();
    }
}
new Present;


