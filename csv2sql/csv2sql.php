<?php
if( $argc < 3 )
{
	echo "Use in correct format:\n";
	echo "\tphp csv2sql.php inputFileName.csv outputFileName.sql [verbose (0/1)]\n";
	exit();
}
elseif( !file_exists($argv[1]) || !is_file($argv[1]) )
{
	echo "File '" . $argv[1] . "' does not exists or is a folder name.\n";
	exit();
}
elseif( !is_readable($argv[1]) )
{
	echo "I can't read '" . $argv[1] . "', please check permission of file.\n";
	exit();
}
elseif( ($outFP = fopen($argv[2], 'w+')) === false )
{
	echo "I can't write to file with '" . $argv[2] . "' name.\n";
	exit();
}

if( ($fp = fopen($argv[1], 'r')) !== false )
{
	$id = 1;
	$lang = 'fa';
	$buffer = '';
	
	$headOfSqlCommand = 'INSERT INTO `messagetranslation` (`id`, `translation`, `language`, `messagesource_id`) VALUES ';
	
	while( ( ($line = fgetcsv($fp)) !== false ) && !feof($fp)  )
	{
		$buffer .= $headOfSqlCommand . "('" . $id . "', '" . $line[3] . "', '" . $lang . "', '" . $line[0] . "');\n";
		
		++$id;
		
		if( $id % 100 === 0 )
		{
			fwrite($outFP, $buffer);
			$buffer = '';
			
			if( isset($argv[3]) && (int)$argv[3] )
			{
				echo "Buffer flushed.\n";
			}
		}
	}
	
	if( $id % 100 !== 0 )
	{
	        fwrite($outFP, $buffer);
			
		if( isset($argv[3]) && (int)$argv[3] )
		{
			echo "Buffer flushed.\n";
		}
	}
			
	fclose($fp);
	fclose($outFP);
	
	echo "Mission Completed! :)\n";
}
?>
