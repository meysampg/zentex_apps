<!doctype html>
<html>
<head>
	<title>CSV Translate - ZenTex</title>
	<meta charset="utf-8" />
	
	<style type="text/css">
		.transForm{
			width:600px;
			margin:10px auto 0;
		}
		.transForm textarea{
			width:280px;
			resize:none;
			height:65px;
			color:#000;
		}
		.rtlInput{
			direction:rtl;
		}
		.row{
			margin-bottom:5px;
		}
		.saveBtn{
			position:fixed;
			right:5px;
			bottom:5px;
			width:190px;
			height:90px;
			font-size:19px;
		}
		.dwnBtn{
			position:fixed;
			left:5px;
			bottom:5px;
			width:190px;
			height:90px;
			font-size:19px;
		}
		.hidden{
			display:none;
		}
		.state{
			font-family:tahoma;
			font-size:11px;
			text-align:center;
			position:fixed;
			top:3px;
			left:3px;
			background-color:#bff5e3;
		}
	</style>
</head>
<body>
<?php
if(isset($_POST['uploadBtn']) || isset($_GET['resume']))
{
	// Upload files to files folder.
	if(isset($_POST['uploadBtn']))
	{
		$nameOfFile = $_FILES['uFile']['name'];
		$uploadFolder = './files/';
		move_uploaded_file($_FILES['uFile']['tmp_name'], $uploadFolder.$nameOfFile);
	}
	else
	{
		$nameOfFile = $_GET['resume'];
		$uploadFolder = './files/';
	}
	
	// Read file and create things that we need ;)
	$fp = fopen($uploadFolder.$nameOfFile, 'r');
	
	$row = 1;
	$translated = 0;
	
	echo '<form method="POST" name="transForm" class="transForm" action="'.htmlentities($_SERVER['PHP_SELF']).'?action=edit">';
	echo "\n";
	
	echo '<input class="hidden" name="uploadFolder" value="'.htmlentities($uploadFolder).'" />';
	echo '<input class="hidden" name="nameOfFile" value="'.htmlentities($nameOfFile).'" />';
	
	while( ($data = fgetcsv($fp)) && !feof($fp) )
	{
		if(isset($data[3]))
		{
			$persianString = $data[3];
		}
		else 
		{
			$persianString = '';
		}
		
		if(strlen(trim($persianString)))
		{
			++$translated;
		}
		
		echo '<div class="row">';
		echo '<!-- '.$row.' -->';
		echo '<input type="hidden" name="id[]" value="'.htmlentities($data[0]).'" />';
		echo '<input type="hidden" name="category[]" value="'.htmlentities($data[1]).'" />';
		echo '<textarea name="source[]" readonly>'. htmlentities($data[2]) . '</textarea>';
		echo '<textarea name="translate[]" class="rtlInput">'.htmlentities($persianString).'</textarea>';
		echo "</div>\n";
		
		++$row;
	}
	
	echo '<input type="submit" name="saveBtn" value="Save" class="saveBtn" />';
	echo '<input type="button" value="Download" class="dwnBtn" onClick="parent.location=\'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?dwn='.$nameOfFile.'\'" />';
	echo '</form>';
	echo '<div class="state">' . $translated . ' of ' . ($row-1) .' (' . substr((($translated/($row-1))*100),0,5) . '%)</div>';
	
	fclose($fp);
}
elseif(isset($_GET['action']))
{		
	
	$fileName = html_entity_decode($_POST['uploadFolder']).html_entity_decode($_POST['nameOfFile']);
	
	$fp = fopen($fileName, "w");
	
	for($i=0; $i<count($_POST['id']); ++$i)
	{
		fputcsv($fp, array(html_entity_decode($_POST['id'][$i]), html_entity_decode($_POST['category'][$i]), html_entity_decode($_POST['source'][$i]), html_entity_decode($_POST['translate'][$i])));	
	}
	
	fclose($fp);
	
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?resume='.$_POST['nameOfFile']);
}
elseif(isset($_GET['dwn']))
{
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$_GET['dwn']);
	header('Location: ./files/'.$_GET['dwn']);
}
else
{
	echo '<form method="POST" enctype="multipart/form-data">
		<label for="uFile">Please select your file</label>:
		<input type="file" name="uFile" /><br />
		<input type="submit" name="uploadBtn" value="GO!" />
	</form>';
}
?>
</body>
</html>