<?php namespace presentation;
class Present
{
    private $myName="Inconsistency";
    private $debug=1;
    public function __construct()
    {
        $this->html=file_get_contents("templates/hddr.html");
        $this->html.=file_get_contents("templates/inconsistency.html");
        $this->html=str_replace("###addedStyles###","",$this->html);
        $this->html=str_replace("###title###","TSC Data",$this->html);
        $this->html=str_replace("###header###","Inconsistency",$this->html);
        $arrayLog=json_decode(file_get_contents("page.log.json"),true);
        unset($arrayLog['page']['page']['static']);
        unset($arrayLog['page']['page']['elements']);
        unset($arrayLog['page']['messages']);
        if(!isset($arrayLog['calls']['message'])){
            $arrayLog['calls']['message']="No Specific message assigned to the inconsistency";
        }
        $this->html=str_replace("###messageResponse###",$arrayLog['calls']['message'],$this->html);
        $logOutput="Apologies, Detailed Log saved -- We will investigate";
        if($this->debug){
            $logOutput.="<br>Debug:<pre>".print_r($arrayLog,true)."</pre><hr>";
        }
        $this->html=str_replace("###debugger###",$logOutput,$this->html);
        echo($this->html);
        exit();
    }
}
new Present;



