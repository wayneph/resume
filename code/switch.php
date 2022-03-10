<?php namespace presentation;
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;

class Present extends BL
{
    private $myName="switch";
    public function __construct()
    {
        $this->trace[]="({$this->myName}/php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        parent::__construct($this->myName);
        parent::executeAPICalls();
        parent::getHTML(); // gives $this->html - also does replaces
        parent::evalInputs();
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###specificHeader###",$this->pageArray['postSwitchElements']['specificHeader'],$this->html);
        $this->html=str_replace("###header###",$this->pageArray['postSwitchElements']['heading'],$this->html);
        $this->html=str_replace("###messageDate###",$this->pageArray['postSwitchElements']['date'],$this->html);
        $this->html=str_replace("###messageTopic###",$this->pageArray['postSwitchElements']['topic'],$this->html);
        $this->html=str_replace("###message###",$this->pageArray['postSwitchElements']['message'],$this->html);
        $this->html=str_replace("###messageResponse###",$this->pageArray['postSwitchElements']['response'],$this->html);
        // this page bits
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###myName###",$this->myName,$this->html);
        // default for script added
        $this->html=str_replace("###scriptAdded###","<!-- No Script This Page-->",$this->html);
        $dBug=parent::setDebugger($this->pageArray['cookie']);
        $this->html=str_replace("###debugger###",$dBug,$this->html);
        echo($this->html);
        parent::writeLogs();
    }
}
new Present;


