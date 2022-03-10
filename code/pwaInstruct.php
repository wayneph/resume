<?php namespace presentation;
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;
class Present extends BL
{
    private $myName="pwaInstruct";
    public function __construct()
    {
        $this->trace[]="({$this->myName}/php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        parent::__construct($this->myName);
        parent::executeAPICalls();
        parent::getHTML(); // gives $this->html - also does replaces
        parent::validateAuthentication();
        $postsDetailCondition=$this->buildMessages($this->pageArray['messages']);
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###softSubMenus###",$this->addedMenu,$this->html);
        $this->html=str_replace("###postsDetailCondition###",$postsDetailCondition,$this->html);
        $this->html=str_replace("###touchUs###","<!--..-->",$this->html);
        $this->html=str_replace("###feedback###","<!--..-->",$this->html);
        $this->html=str_replace("###myName###",$this->myName,$this->html);
        $dBug=parent::setDebugger($this->pageArray['cookie']);
        $this->html=str_replace("###debugger###",$dBug,$this->html);
        echo($this->html);
        parent::writeLogs();
    }
}
new Present;


