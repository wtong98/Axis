<?php

$servername = "localhost";
$username = "grandpaa";
$password = "a912w921";

$conn = new PDO("mysql:host=$servername;dbname=axis", $username, $password);

$department = $_POST["department"];
$book_id = $_POST["book_id"];
$book_name = $_POST["book_name"];
$student_name = $_POST["student_name"];
$student_id = $_POST["student_id"];
$teacher_name = $_POST["teacher_name"];

$sql = "INSERT INTO records (department, book_id, book_name, student_name, student_id, teacher_name) VALUES ('" . $department . "', " . $book_id . ",'" . $book_name . "','" . $student_name . "'," . $student_id . ",'" . $teacher_name . "')";
echo $sql;
$temp = "INSERT INTO records (department, book_id, book_name, student_name, student_id, teacher_name) VALUES ('magic', 1234, 'death to weasels', 'fladoop', 124124, 'shortbottom')";
echo "<br>" . $temp . "<br>";
$conn->exec($sql);
echo "Yay!";

$conn = null;
?>
