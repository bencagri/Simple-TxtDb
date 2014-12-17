<?php
include('../txtdb.class.php');

$db = new TxtDb();

//Our Data
$data = array(
	'Name' => 'John',
	'Surname' => 'Doe',
	'Age'	=> '45',
	'Email'	=> 'test@test.com');


//Insert
$db->insert('teachers',$data);


//Select All
$teachers = $db->select('teachers'); //or $db->selectAll('teachers');

foreach ($teachers as $teacher) {
	echo 'Name : '.$teacher->Name.'<br>';
	echo 'Surname : '.$teacher->Surname.'<br>';
	echo 'Age : '.$teacher->Age.'<br>';
	echo 'Email : '.$teacher->Email.'<br>';
}

//Select Row by id
$teacher = $db->select('teachers',1);
	echo 'Name : '.$teacher->Name.'<br>';
	echo 'Surname : '.$teacher->Surname.'<br>';
	echo 'Age : '.$teacher->Age.'<br>';
	echo 'Email : '.$teacher->Email.'<br>';


//Delete All
$db->delete('teachers');


//Delete Row by id
$db->delete('teachers',1);


//Update
$update = array(
	'Name' => 'Jehn',
	'Surname' => 'Doe',
	'Age'	=> '30',
	'Email'	=> 'jehn@jehn.com');
$db->update('teachers',$update,1);
