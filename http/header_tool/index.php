<?
include "Date.class.php";
include "FriendlyDate.class.php";
session_start();
$ch = curl_init();

if(!isset($_SESSION['history']) || isset($_REQUEST['clear'])) $_SESSION['history'] = array();

$default = (isset($_REQUEST['swomped'])) ? "http://www.swomped.com/twcom/" . date("M-d-Y/h-i-s/") : "http://www.google.com";

$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : $default;
$method = isset($_REQUEST['method']) ? strtolower($_REQUEST['method']) : "get";
if(isset($_REQUEST['key']) && isset($_REQUEST['value']))
{
	$data = array();
	$key = $_REQUEST['key'];
	$value = $_REQUEST['value'];
	if(is_array($key) && is_array($value))
	{
		if(count($key) == count($value))
		{
			foreach($key as $k=>$v)
			{
				if($v && $value[$k]) $data[$v] = $value[$k]; 
			}
		}
		
	}
	else
	{
		$data[$key] = $value;
	}
	$vars = $data;
	if(count($_FILES))
	{
		if(isset($_FILES['file']["tmp_name"]) && $_FILES['file']["tmp_name"])
		{

		
		
			$data[$_REQUEST['filevar']] = "@" . $_FILES['file']["tmp_name"];
			
			if(strtolower($_REQUEST['method']) == "put")
			{
				
				curl_setopt($ch, CURLOPT_UPLOAD, 1);
				$localfile = $_FILES['file']["tmp_name"];
				
			 	$fp = fopen($localfile, 'r');
		
			 	//curl_setopt($ch, CURLOPT_UPLOAD, 1);
			 	curl_setopt($ch, CURLOPT_INFILE, $fp);
			 	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
			 	curl_setopt($ch, CURLOPT_USERPWD, 'swomped:blAck1181'); 
		 	}
		}
		
		
	}
}
if(isset($_REQUEST['sent811911']) && session_id() != "")
{
	$found = false;
	foreach($_SESSION['history'] as $k=>$link)
	{
		if($link['uri'] == $_SERVER['REQUEST_URI']) 
		{
			
			unset($_SESSION['history'][$k]);
		}
		
	}
	if(!$found)
	{
		$arr = array();
		$arr["link"] = $url;
		$arr["uri"] = $_SERVER['REQUEST_URI'];
		$arr['time'] = time();
		$arr['method'] = $_REQUEST['method'];
		$arr['data'] = $vars;
		if(isset($_FILES['file']["tmp_name"]) && $_FILES['file']["tmp_name"]) $arr['file'] = $_FILES['file']["tmp_name"];
		$_SESSION['history'][] = $arr;
	}
	
	unset($link);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>A Simple Header Response Tool - by Travis Weerts</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<script type="text/javascript">

	var div = document.getElementById("variables");
	var ids = 2;
	
	function add(id)
	{
		var e = document.getElementById(id);
		var r = document.getElementById(id + "_remove");
		var a = document.getElementById(id + "_add");
		a.style.display = "none";
		r.style.display = "block";
		newElement(id);
		
	}
	function newElement(ide) {
	  var ni = document.getElementById('variables');
	  var newdiv = document.createElement('div');
	  var n = 'newvar' + (ids++);
	   newdiv.setAttribute('class','variable');
	  newdiv.setAttribute('id',n);
	 
	  newdiv.innerHTML = '<div class="input"><input type="text" name="key[]" class="text" /><input type="text" name="value[]" class="text" /></div><div class="actions"><a href="javascript:add(\''+n+'\')" class="add" id="'+n+'_add"></a><a href="javascript:remove(\''+n+'\')" class="delete hidden" id="'+n+'_remove"></a></div>';
	  ni.appendChild(newdiv);
	}
	function remove(id)
	{
		removeElement(id);
	}
	function removeElement(divNum) {
	  var d = document.getElementById('variables');
	  var olddiv = document.getElementById(divNum);
	  d.removeChild(olddiv);
	}
	function changeMethod(frm)
	{
		method = document.getElementById("method");
		
		method = method.value;
		if(method == "GET" || method == "POST")
		{
			frm.method = method;
		}
		else
		{
			frm.method = "POST";
		}
		
		frm.submit();
		
	}
	function updateButton(method)
	{
		
		document.getElementById("button").value = method;
	}

</script>
<body>
<div class="container2">
<a href="http://www.travisweerts.com" class="menu">Back to Travis's Blog</a>
<a href="http://www.travisweerts.com/header_tool/headertool.zip" class="menu">Download this Script</a>
</div>
<div class=" container">
<div class="content">
<div class="box">
<h1>A Header Grabbing Tool</h1>
<p class="desc">This tool was created to give me a simple place to go to actually find what kind of headers are being returned by a URL</p>
</div>
<div class="box form">
<h2>Your Request</h2>
<form action="" method="post" enctype="multipart/form-data" id="form" onsubmit="changeMethod(this);">
	<fieldset>
		<label>URL</label>
		<input type="text" name="url" class="text url" value="<?= $url ?>"/>
	</fieldset>
	<fieldset>
		<label>Variable/Value Pairs</label>
		<div id="variables">
			<?
			if(isset($vars) && count($vars))
			{
			foreach($vars as $key=>$value)
			{
			?>
			<div class="variable" id="<?= $key ?>">
				<div class="input">
					<input type="text" name="key[]" class="text" value="<?= $key ?>" />
					<input type="text" name="value[]" class="text" value="<?= $value ?>" />
				</div>
				<div class="actions">
					<a href="javascript:add('<?= $key ?>')" class="add hidden" id="<?= $key ?>_add"></a>
					<a href="javascript:remove('<?= $key ?>')" class="delete" id="<?= $key ?>_remove"></a>
				</div>
			</div>
			<?
			}
			}
			?>
			<div class="variable" id="newvar1">
				<div class="input">
					<input type="text" name="key[]" class="text" />
					<input type="text" name="value[]" class="text" />
				</div>
				<div class="actions">
					<a href="javascript:add('newvar1')" class="add" id="newvar1_add"></a>
					<a href="javascript:remove('newvar1')" class="delete hidden" id="newvar1_remove"></a>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<label>Upload a file</label>
		<input type="text" name="filevar" value="file_var_name" class="text" />
		<input type="file" name="file" />
		
	</fieldset>
	<fieldset>
		<label>Request Method</label>
		<select name="method"  id="method" onchange="updateButton(this.value)">
			<option <? if($method == "get") echo "selected=true"?>>GET</option>
			<option <? if($method == "post") echo "selected=true"?>>POST</option>
			<option <? if($method == "put") echo "selected=true"?>>PUT</option>
			<option <? if($method == "delete") echo "selected=true"?>>DELETE</option>
		</select>
	</fieldset>
	<fieldset>
		<input type="hidden" name="sent811911" value="1" />
		<input type="submit" value="<?= strtoupper($method) ?>" id="button" />
	</fieldset>
	
</form>
</div>


<?
if(session_id() != "" && count($_SESSION['history']))
{
	?>
	<div class="box history">
	<h2>History</h2>
	<ul class='links'>
	<?
	$arr = array_reverse($_SESSION['history']);
	$arr = array_slice($arr, 0, 6);
	foreach($arr as $link)
	{
		$d = NULL;
		$m = NULL;
		$p = NULL;
		if(isset($link['time']))
		{
			$f = new FriendlyDate($link['time']);
			$time = "<span class='time'>" . $f->create() . "</span>";
		}
		else
		{
			$time = NULL;
		}
		if(isset($link['data']))
		{
			$d = count($link['data']) ? "<span class='vars'>" . count($link['data']) . " vars</span>" : NULL ;
			if(!$d) $d = NULL;
		}
		if(isset($link['data']))
		{
			$m = "<span class='method'>" . $link['method'] . "</span>";
		}
		if(isset($link['file']))
		{
			$p = "<span class='method'>" . "w/ upload" . "</span>";
		}
		?>
		<li><a href="<?= $link['uri'] ?>"><?= $link['link'] ?></a><?= $m ?><?= $d ?><?= $p ?><?= $time ?></li>
		<?
	}
	?>
	</ul>
	<a href="?clear=1" class="clear">clear history</a>
	</div>
	<?
}
?>
<div class="clear"></div>
<?



curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
switch($method)
{
	case "post":
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	break;
	
	case "get":
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	break;
	
	case "put":
	print "setting it to put";
	//curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_PUT, 1);
	break;
	
	default:
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    break;
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);

// grab URL and pass it to the browser
$ret = curl_exec($ch);


if(!curl_errno($ch))
{
    $info = curl_getinfo($ch);

	echo '<div class="box"><h2>Response from: <a href="'.$info['url'].'" target="_blank">'.$info['url'].'</a></h2>';
    echo '<p>Took <strong>' . $info['total_time'] . '</strong> seconds<p></div>';
    
    echo '<div class="box"><pre>';
    
    echo htmlspecialchars($ret);
    
    echo '</pre></div>';
    
    
    echo "<div class='box'><h2>More Header Info</h2><table>";
    
    echo "<tr><th>Header</th><th>Value</th>";
    
    foreach($info as $key=>$value)
    {
    	print "<tr><td class='key'><strong>$key</strong></td><td> $value</td></tr>";
    }
    
    echo "</table></div>";
    
    
    
}
else
{
	?>
	<h2><?= curl_error($ch) ?></h2>
	<?
}

// close cURL resource, and free up system resources
curl_close($ch);
if(isset($fp)) fclose($fp);
?>
</div>
</div>
<div class="container">

<span class="copy">&copy; <?= date("Y") ?> Travis Weerts. All rights reserved.</span>
<a href="http://www.blackpulp.com" title="Blackpulp Website" target="_blank" style="float:right;"><img src="http://www.travisweerts.com/images/blackpulp.png" alt="Blackpulp Design" /></a>

</div>
</body>
</html>