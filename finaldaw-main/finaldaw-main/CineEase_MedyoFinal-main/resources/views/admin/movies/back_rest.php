<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup, Restore, and Management</title>
</head>
<body>
    <h2>Database Backup</h2>
    <form method="post" action="">
        <button type="submit" name="download_backup">Download Backup</button>
    </form>

    <h2>Database Restore</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="file" name="restore_file" accept=".sql">
        <button type="submit" name="restore">Restore</button>
    </form>

    <h2>Database Management</h2>
	<h3>Drop the backed up databse</h3>
    <form method="post" action="">
        <button type="submit" name="drop_database">Drop Database</button>
    </form>
	<h3>Create a new database for recovery</h3>
    <form method="post" action="">
        <button type="submit" name="create_database">Create Database</button>
    </form>
</body>
</html>

<?php

$mysqlUserName = 'root';
$mysqlPassword = '';
$mysqlHostName = 'localhost';
$DbName = 'finals';

// Backup database
if(isset($_POST['download_backup'])){
    backupDatabase($mysqlHostName, $mysqlUserName, $mysqlPassword, $DbName);
}

// Restore database
if(isset($_POST['restore'])){
    restoreDatabase($mysqlHostName, $mysqlUserName, $mysqlPassword, $DbName);
}

// Drop database
if(isset($_POST['drop_database'])){
    dropDatabase($mysqlHostName, $mysqlUserName, $mysqlPassword, $DbName);
}

// Create database
if(isset($_POST['create_database'])){
    createDatabase($mysqlHostName, $mysqlUserName, $mysqlPassword, $DbName);
}

function backupDatabase($host, $user, $pass, $name)
{
    $mysqli = new mysqli($host, $user, $pass, $name); 
    $mysqli->select_db($name); 
    $mysqli->query("SET NAMES 'utf8'");

    $queryTables = $mysqli->query('SHOW TABLES'); 
    while($row = $queryTables->fetch_row()) 
    { 
        $target_tables[] = $row[0]; 
    }   

    $content = '';

    foreach($target_tables as $table)
    {
        $result = $mysqli->query('SELECT * FROM '.$table);  
        $fields_amount = $result->field_count;  
        $rows_num = $mysqli->affected_rows;     
        $res = $mysqli->query('SHOW CREATE TABLE '.$table); 
        $TableMLine = $res->fetch_row();
        $content .= "\n\n".$TableMLine[1].";\n\n";

        for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) 
        {
            while($row = $result->fetch_row())  
            { 
                if ($st_counter % 100 == 0 || $st_counter == 0)  
                {
                    $content .= "\nINSERT INTO ".$table." VALUES";
                }
                $content .= "\n(";
                for($j = 0; $j < $fields_amount; $j++)  
                { 
                    $row[$j] = str_replace("\n", "\\n", addslashes($row[$j])); 
                    if (isset($row[$j]))
                    {
                        $content .= '"'.$row[$j].'"'; 
                    }
                    else 
                    {   
                        $content .= '""';
                    }     
                    if ($j < ($fields_amount - 1))
                    {
                        $content .= ',';
                    }      
                }
                $content .= ")";
                if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) 
                {   
                    $content .= ";";
                } 
                else 
                {
                    $content .= ",";
                } 
                $st_counter++;
            }
        }
        $content .= "\n\n\n";
    }
    
    $backup_name = $name . "_backup_" . date("Y-m-d_H-i-s") . ".sql";
    $backup_folder = 'C:/xampp/htdocs/CineEase/';
    $backup_path = $backup_folder . $backup_name;
    
    file_put_contents($backup_path, $content);
}

function restoreDatabase($host, $user, $pass, $name)
{
    $backup_path = $_FILES['restore_file']['tmp_name'];
    if ($backup_path) {
        $file_content = file_get_contents($backup_path);
        
        $mysqli = new mysqli($host, $user, $pass, $name);
        $mysqli->multi_query($file_content);
        $mysqli->close();
        
        echo "Database restored successfully.";
    } else {
        echo "Please select a file to restore.";
    }
}

function dropDatabase($host, $user, $pass, $name)
{
    $mysqli = new mysqli($host, $user, $pass);

    // Drop database
    $sql_drop = "DROP DATABASE IF EXISTS $name";
    if ($mysqli->query($sql_drop) === TRUE) {
        echo "Database dropped successfully<br>";
    } else {
        echo "Error dropping database: " . $mysqli->error . "<br>";
    }

    // Close connection
    $mysqli->close();
}

function createDatabase($host, $user, $pass, $name)
{
    $mysqli = new mysqli($host, $user, $pass);

    // Create database
    $sql_create = "CREATE DATABASE $name";
    if ($mysqli->query($sql_create) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $mysqli->error . "<br>";
    }

    // Close connection
    $mysqli->close();
}

?>
