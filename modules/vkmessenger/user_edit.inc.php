<?php

if ($this->mode=='setvalue') {
   global $prop_id;
   global $new_value;
   global $id;
   $this->setProperty($prop_id, $new_value, 1);   
   $this->redirect("?id=".$id."&view_mode=".$this->view_mode."&edit_mode=".$this->edit_mode."&tab=".$this->tab);
} 

if ($this->mode=='cmd') {
    global $data;
    $this->cmd($data);
}


  
if ($this->owner->name=='panel') {
  $out['CONTROLPANEL']=1;
}
$id = $this->id;
$table_name='vk_user';
$rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
    $res = SQLSelect("SELECT * FROM users");
    if ($res[0]) {
        $out['LIST_MEMBER'] = $res;
    }

if ($this->mode=='update') {
  $ok=1;
  if ($this->tab=='') {
    $rec['ADMIN']=gr('admin')!=1 ? 0:1;
	$rec['SILENT']=gr('silent')!=1 ? 0:1;
    $rec['HISTORY']=gr('history')!=1 ? 0:1;
    $rec['HISTORY_LEVEL']=gr('history_level');
	if($rec['HISTORY_LEVEL'] == '') $rec['HISTORY_LEVEL'] = 0;
    $rec['HISTORY_SILENT']=gr('history_silent');
	if($rec['HISTORY_SILENT'] == '') $rec['HISTORY_SILENT'] = 0;
    $rec['CMD']=gr('cmd')!=1 ? 0:1;
    $rec['PATTERNS']=gr('patterns')!=1 ? 0:1;
    $rec['DOWNLOAD']=gr('download')!=1 ? 0:1;
	$rec['PLAY']=gr('play')!=1 ? 0:1;;
    $rec['MEMBER_ID']=gr('select_member');
    
    //UPDATING RECORD
    if ($ok) {
      if ($rec['ID']) {
        SQLUpdate($table_name, $rec); // update
      } else {
        $new_rec=1; 
        $rec['ID']=SQLInsert($table_name, $rec); // adding new record
        $id=$rec['ID'];
      }  
      $out['OK']=1;
    } else {
      $out['ERR']=1;
    }
  }
    $ok=1;
}
 
outHash($rec, $out);
  
?>
