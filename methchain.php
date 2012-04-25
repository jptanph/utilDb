<?php
	
	$connection = mysql_connect('localhost', 'root', '');
	$db			= mysql_select_db('movie_db',$connection);
	
	class utilDb
	{
		private $str;
		
		function sql($sql)
		{
			$this->str .= $sql;
			return $this;
		}

		function exec()
		{
			$query	= mysql_query($this->str);
			// echo '<pre>';
			while($row = mysql_fetch_array($query))
			{
				echo $row['title'].'<br />';
			}
		}

		function debug()
		{
			return $this->str .= ' | Debug mode';
		}
	}


	// $db = new utilDb();
	// $db->sql('SELECT * FROM movie')->exec();
	
	// $docRoot = $_SERVER['DOCUMENT_ROOT'];
	// require($docRoot.'/utildb/lib/Common.php');
	
	// $asd = new utilDb();
	
	// $array[]	= array(
					// 'asd'=>'asd',
					// 'rtyr'=>'dgdf',
					// );
	// $array[]	= array(
					// 'asd'=>'asd',
					// 'rtyr'=>'dgdf',
					// );
	// echo $asd->getInsertQueryLoop('asd',$array);
	
	
	
	
	
	
	
	
	
	
	