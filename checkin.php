<?php

$servername = "localhost";
$username = "grandpaa";
$password = "a912w921";

$conn = new PDO("mysql:host=$servername;dbname=axis", $username, $password);

$book_name = $_POST["book_name"];
$student_id = $_POST["student_id"];

$sql = "DELETE FROM records WHERE book_name = '" . $book_name . "' AND student_id = " . $student_id . ";";
echo $sql;
$conn->exec($sql);
echo "Yay!";

$conn = null;
?>
