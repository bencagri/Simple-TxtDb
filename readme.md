PHP TxtDb Class
================
I have written this class for simple usage for txt files as database. This is a simple way to store datas in txt files. If you dont want to use Mysql, Mssql, SQLite etc. use this class.

Logic is simple;
Your directory = Your database directory,
Your files     = Your tables,
File contents  = Table rows

#Update
===============
* v2.0.1 - Km.Van optimize update method
* v2.0 - Km.Van rewrite all codes and optimize the IO operation. And uses more stringent API.
* v1.7 - You can use select method with 'where' situation. Check examples.

#Usage
===============

    require("txtdb.class.php");

    $db = new TxtDb();
    
thats it. Or;

	$db = new TxtDb([
		'dir'      => 'db/',
		'extension' => 'txtdb',
		'encrypt'   => false,
	]);


Encryption is used to encrypt file names.

###Inserting Data
This method gets two parameters. 
First is "table name", second is our data in array.

	$db->insert("teachers", [
		"name" => "John Doe",
		"email" => "john@google.com"
	]);


###Update
This method gets three parameters.
First is "table name", second is our data in array and third is "where situation"

    $db->update("teachers", [
		"name" => "John Doe",
		"email" => "john@google.com"
	],1);

###Delete

    $db->delete("students",1);
    
Important : If you dont set second param, this method deletes all contents in file!

  
###Select
This method returns array object.

     $students =  $db->select('students');
     
     foreach($students as $student){
        echo $student->name;
     }
     
    
###Select One Row
This method lists one row by id

    $user = $db->select('students',1);
    
    echo $user->name;
    //output
    John Doe


###Select with where situation
With this method you can select data with where situation, second param should be array

    $teachers =$db->select('teachers',array('name' =>'Test-1'));
    foreach ($teachers as $teacher) {
      echo 'Name : '.$teacher->Name.'<br>';
      echo 'Surname : '.$teacher->Surname.'<br>';
      echo 'Age : '.$teacher->Age.'<br>';
      echo 'Email : '.$teacher->Email.'<br>';
    }

Easy? huh.

## Contributing

1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D


## TODOS

 - Where situation on update method
 - Where situation on delete method



