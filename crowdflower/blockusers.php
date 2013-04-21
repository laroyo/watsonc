<html>
<head>
<script >

var worker_id = "16357142";
var reason = "Your answers were not good!";
var job_id = "185404";

function loadXMLDoc()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }


var url = "https://crowdflower.com/jobs/" + job_id + "/workers/" + worker_id;
var parameters = "_method=put&persist=true&flag=" + reason;
xmlhttp.open("POST", url, true);
 
//Send the proper header information along with the request
xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xmlhttp.setRequestHeader("Content-length", parameters .length);
xmlhttp.setRequestHeader("Connection", "close");
 
xmlhttp.onreadystatechange = function() {//Handler function for call back on state change.
    if(xmlhttp.readyState == 4) {
        alert(xmlhttp.responseText);
    }
}
xmlhttp.send(parameters);

}


/*
function blockWorker() {
	data = {flag:reason};
	data.persist=true;
	new Request({url:"https://crowdflower.com/jobs/"+job_id+"/contributors/"+worker_id,onComplete:function(text){
			var flag_link=$("w"+worker_id+"_flag_link");
			flash(text)}.bind(this)
		}).put(data);
}
*/
//blockWorker();
//loadXMLDoc();
</script>
</head>
<body>

<?php
$content_type = "application/json";
$api_key = "c6b735ba497e64428c6c61b488759583298c2cf3";
$job_id = "185404";
$worker_id = "16357142";
$reason = "Your answers were not correct!";
$url = "http://api.crowdflower.com/v1/jobs.json?key=".$api_key;

/* useful functions for printing the results from the web server */
function objectToArray($obj) {
	if (is_object($obj)) {
		$obj = get_object_vars($obj);
	}
	if (is_array($obj)) {
		return array_map(__FUNCTION__, $obj);
	}
	else {
		return $obj;
	}
}

function arrayToObject($d) {
	if (is_array($d)) {
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

/* wrap the attributes with the prefix "worker" */
function prefixDataKeys($data, $prefix) {
      $newdata = array();

      foreach ($data as $key => $value) {
          $newkey = "$prefix" . '[' . $key . ']';
          $newdata[$newkey] = $value;
      }

      return $newdata;
}


//$data['reason'] = $reason;
$url = "https://api.crowdflower.com/v1/jobs/$job_id/workers/$worker_id/flag?key=$api_key";
//$query = http_build_query($data, '', '&');

$data = array('flag' => $reason, 'persist' => 'true'); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
curl_setopt($ch, CURLOPT_URL, $url);

$response = json_decode(curl_exec($ch));
$info = curl_getinfo($ch);
print_r($info);
$array = objectToArray($response);
print_r($array);
print_r($response);
?>


</body>
</html>
