<?php

//This script was created for the specific purpose of repopulating some some user names on the darkroom after we deleted them. 
//Before running it, we exported all the old users' posts from an old backup, dumping the post ids into a csv along with the post_title and post_author fields.
//We also created users 60 (Stokely), 62(Nick) and 63(Olivia) with the role of "subscriber."
//Finally, after some testing on a cloned version of the database, we ran the script. It seems to have worked well, but please read the caveat below about auth info!

//error_reporting(E_ALL);
//ini_set('display_errors','1');

//I removed authentication information for the database. You'll of course need to replace that by defining $connectionstring, $user and $pass variables.

$db = new PDO($connectionstring, $user, $pass);

$csv = file_get_contents('stokesandnickandolivia.csv');

//$csv = str_replace('","','"|||"', $csv);

$fullsql = "";
$html = '<table><tr><td>post_author</td><td>post_title</td><td>id</td></tr>';

foreach(explode("\n", $csv) as $row){
	$fields = explode('","',$row);
	$post_author = substr($fields[3],0,-2); //<the call to substr() gets rid of a quote we hadn't removed yet, since this field is on the end of a row.
	$post_title = $fields[1];
	$id = substr($fields[0],1);
	$html .= '<tr><td>'.$post_author.'</td><td>'.$post_title.'</td><td>'.$id.'</td></tr>';
	switch($post_author){
		case 2:
			$post_author = 60; //For Stokely's new "subscriber" user
			break;
		case 1: 
			$post_author = 62; //For Nick's new "subscriber" user
			break;
		case 20:
			$post_author = 63; //For Olivia's new "subscriber" user
			break;
	}
	$sql = 'update wp_posts SET post_author = '.$post_author.' where post_title like "%'.$post_title.'%" and ID = '.$id.' and post_type like "post" ';
	$db->exec($sql);
	$fullsql .= $sql.'<hr>';
}

$html .= '</table><hr>'.$fullsql.'<hr>';

print($html);


print('<hr>All done!');

?>
