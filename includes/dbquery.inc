<?php 

/***
 * Library with some simple, commonly used query templates or methods (including one for insertion). 
 **/


/**
 * Returns the results of $query as an array. Each array entry corresponds to a row. 
 **/
function simpleQuery($query){

  //echo $query; 
  $rs = mysql_query($query); 
 
  while($row = mysql_fetch_assoc($rs)){       
    $res[] = $row;     
  }  
  return $res; 
}

/**
 * Query for obtaining one single value (for instance, a counter)
 **/
function queryOne($query){
  $rs = mysql_query($query);   
  $row = mysql_fetch_assoc($rs);
  $keys = array_keys($row);
  return $row[$keys[0]];
}

/**
 * Returns the results of $query as an associative array [$key => $value]
 * Both $key and $value should be attributes in the query (usually: columns on the queried table). 
 **/
function queryKeyValue($query, $key, $value){

  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $res[$row[$key]] = $row[$value]; 
  }  
  return $res; 
}

/**
 * 
 **/
function queryKeyList($query, $key){
  
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){
    $k = $row[$key]; 
    unset($row[$key]); 
    $res[$k] = $row; 
  }  
  return $res; 
}

/**
 * Returns the results of $query as a list. Each entry of the list is a single value (instead of a row). 
 * Used, for instance, to obtain a list of keys (object id's), or a list of values ("all the cosine values for a job")
 * To prevent misuse, the query shouldn't contain more than 1 attribute: select <attribute> from <table>; 
 **/
function queryList($query){
  $rs = mysql_query($query); 
  while($row = mysql_fetch_assoc($rs)){       
    
    if(sizeof(array_keys($row)) > 1){
      throw new Exception("The result set contains more than 1 field"); 
    } else {
      $keys = array_keys($row);       
      $res[] = $row[$keys[0]];     
    }    
  }  
  return $res; 
}

/**
 * Groups values within the result by $group_key. 
 * Ex: retrieve a list with all the filters of a particular worker (see ex. in analytics/job.php).
 * @param  $value: indicate the field of the row to be listed. If any, the entire rowset will be added. 
 **/
function queryGroup($query,$group_key, $value=NULL){
  
  $rs = mysql_query($query); 
  $group_id = NULL;
  while($row = mysql_fetch_assoc($rs)){
    if($row[$group_key] != $group_id) 
      $group_id = $row[$group_key]; 
    
    unset($row[$group_key]);
    if($value != NULL)
      $res[$group_id][] = $row[$value];              
    else
      $res[$group_id][] = $row;                 
  }
  return $res; 
}

/**
 * Simple query insert (no parameters). If it's an AUTO_INCREMENT, returns the ID of the inserted element (0 otherwise). 
 **/
function simpleInsert ($query){
  $rs = mysql_query($query); 
  return mysql_insert_id(); 
}