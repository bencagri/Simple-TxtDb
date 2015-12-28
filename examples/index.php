<?php
include('../txtdb.class.php');

$db = new TxtDb();

//Our Data
$data = array(
	'name' => 'John-insert',
	'surname' => 'Doe',
	'age'	=> '45',
	'email'	=> 'test@test.com'
);


//Insert
//$db->insert('teachers',$data);


//Select All
echo '<h1>Select all</h1>';
$teachers = $db->select('teachers'); //or $db->select_all('teachers');
foreach ($teachers as $teacher) {
	echo 'name : ' . $teacher['name'] . '<br>';
	echo 'surname : ' . $teacher['surname'] . '<br>';
	echo 'age : ' . $teacher['age'] . '<br>';
	echo 'email : ' . $teacher['email'] . '<br>';
	echo '<br>';
}

echo '<hr>';

echo '<h1>Select row by id</h1>';
//Select Row by id
$teacher = $db->select('teachers',1);
	echo 'name : '.$teacher['name'] . '<br>';
	echo 'surname : '.$teacher['surname'] . '<br>';
	echo 'age : '.$teacher['age'] . '<br>';
	echo 'email : '.$teacher['email'] . '<br>';

echo '<hr>';

echo '<h1>Select with where situation</h1>';
//Select with where situation
$teachers = $db->select('teachers',array('name' =>'John-2'));
foreach ($teachers as $teacher) {
	echo 'name : '.$teacher['name'] . '<br>';
	echo 'surname : '.$teacher['surname'] . '<br>';
	echo 'age : '.$teacher['age'] . '<br>';
	echo 'email : '.$teacher['email'] . '<br>';
}


//Delete All
//$db->delete('teachers');


//Delete Row by id
//$db->delete('teachers',1);


//Update
//$update = array(
//	'name' => 'Jehn',
//	'surname' => 'Doe',
//	'age'	=> '30',
//	'email'	=> 'jehn@jehn.com');
//$db->update('teachers',$update,0);
