<?php namespace presentation;
$file=$_SERVER['DOCUMENT_ROOT']."/include/appControl/Bl.php";
include_once($file);
use BL\BL;
class Present extends BL
{
    private $myName="pn";
    public function __construct()
    {
        $this->trace[]="({$this->myName}/php)::<b>".__METHOD__."</b>->Line::<b>".__LINE__."</b> @".date("H:i:s");
        parent::__construct($this->myName);
        parent::executeAPICalls();
        parent::getHTML();
        parent::buildEntityTypesMenu();
        // 	scriptAdded
        $this->html=str_replace("###title###",$this->pageArray['page']['page']['title'],$this->html);
        $this->html=str_replace("###addedStyles###",$this->pageArray['page']['page']['styles_added'],$this->html);
        $this->html=str_replace("###softMenu###",$this->addedMenu,$this->html);
        // $this->html=str_replace("###myName###",$this->myName,$this->html);
        echo($this->html);
        parent::writeLogs();
    }
}
new Present;


