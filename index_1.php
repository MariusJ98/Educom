<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = 'marius_webshop';
  // Create connection
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn)
  {
    die("Connection failed: " . mysqli_connect_error($conn));
  }
  else
  {
    echo "Connected successfully <br>" . PHP_EOL;
  }

// add something to user_table
//$sql = 'INSERT INTO users (user_email, user_name, user_password) VALUES ("marius@email.com", "marius", "12345"), ( "daan@doe.com",  "Daan", "0987")';


//$query = mysqli_query($conn, $sql);
//$result = mysqli_fetch_assoc($query);
$sql = "SELECT * FROM users ";
$query = mysqli_query($conn, $sql);
//$result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
while ($row = mysqli_fetch_assoc($query)){
    echo $row['user_name'];
}


$email_input = "john@doe.com";
$email_query = mysqli_prepare($conn, 'SELECT * FROM users WHERE user_email = ?');
mysqli_stmt_bind_param($email_query, "s", $email_input);
mysqli_stmt_execute($email_query); 

$email_result = mysqli_stmt_get_result($email_query);

while($row = mysqli_fetch_assoc($email_result)){
    echo $row['user_email'];
}


function checkEmail(mysqli $conn, string $email): bool{
$email_verification_query = mysqli_prepare($conn, 'SELECT user_email FROM users WHERE user_email =?');
mysqli_stmt_bind_param($email_verification_query, "s", $email);
mysqli_stmt_execute($email_verification_query);
$email_verification_result = mysqli_stmt_get_result($email_verification_query);
return mysqli_fetch_assoc($email_verification_result) != false;
}


function handleUserLogin(mysqli $conn, string $email): bool{
$email_verification_query = mysqli_prepare($conn, 'SELECT user_email FROM users WHERE user_email =?');
mysqli_stmt_bind_param($email_verification_query, "s", $email);
mysqli_stmt_execute($email_verification_query);
$email_verification_result = mysqli_stmt_get_result($email_verification_query);
return mysqli_fetch_assoc($email_verification_result) != false;
}



















