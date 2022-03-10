<?php
    $addr="https://".$_SERVER['SERVER_NAME']."/switch.php?m=you landed here by mistake";
    header("Location:$addr",301);
    exit();