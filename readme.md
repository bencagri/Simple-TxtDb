Php TxtDb Class
================
I have written this class for simple usage for txt files as database. This is a simple way to store datas in txt files. If you dont want to use Mysql, Mssql, SQLite etc. use this class.

Logic is simple;
Your directory = Your Database,
Your files     = Your Tables,
File contents  = Table Rows


#Usage
===============

I have written this class for simple usage for txt files as database.

    require("txtdb.class.php");

    $db = new TxtDb();
    
thats it. Or;

    $db = new TxtDb('name' => 'TABLE-NAME',
      'path'      => 'db/',
      'extension' => '.txtdb',
      'encrypt'   => FALSE);
      


###Inserting Data
This method gets two parameters. 
First is "table name", second is our data in array.
@param Array Data

    $db->insert("users", 
    array("user_name" => "John Doe",
          "user_email" => "john@google.com")
          );
    

###Update
This method gets three parameters.
First is "table name", second is our data in array and third is "where situation"

    $db->update("users", 
    array("user_name" => "Jen Doe",
          "user_email" => "jen@google.com),1);
    

###Delete

    $db->delete("users",1);
    
Important : If you dont set second param, this method deletes all contents in file!

  
###Select
This method returns array object.

     $students =  $db->select('students');
     
     foreach($students as $student){
        echo $student->name;
     }
     
    
###Select One Row
This method list one row by your query.

    $user = $db->select('students',1);
    
    echo $user->name;
    //output
    John Doe


Easy? huh.
