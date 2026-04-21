<?php

  global $session;

  $qry = "1";

  // FIELDS ORDER
  $sortby_cmd = gr('$sortby_cmd');
  if (!$sortby_cmd) {
   $sortby_cmd=isset($session->data['vk_sort_cmd'])?$session->data['vk_sort_cmd']:'';
  } else {
   if (isset($session->data['vk_sort_cmd']) && $session->data['vk_sort_cmd']==$sortby_cmd) {
    if (Is_Integer(strpos($sortby_cmd, ' DESC'))) {
     $sortby_cmd=str_replace(' DESC', '', $sortby_cmd);
    } else {
     $sortby_cmd=$sortby_cmd." DESC";
    }
   }
   $session->data['vk_sort_cmd']=$sortby_cmd;
  }

  if (empty($sortby_cmd)) $sortby_cmd="PRIORITY";
  $out['SORTBY']=$sortby_cmd;
  
  // SEARCH RESULTS  
  $res=SQLSelect("SELECT * FROM vk_cmd WHERE $qry ORDER BY ".$sortby_cmd);
  if (!empty($res[0]['ID'])) {   
    paging($res, 20, $out); // search result paging
    colorizeArray($res);
    $total=count($res);
    for($i=0;$i<$total;$i++) {
     // some action for every record if required
    }
    $out['RESULT']=$res;
  }  
?>
