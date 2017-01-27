<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mmi_book", $Language->MenuPhrase("1", "MenuText"), "booklist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mmi_checking", $Language->MenuPhrase("2", "MenuText"), "checkinglist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(3, "mmi_department", $Language->MenuPhrase("3", "MenuText"), "departmentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(4, "mmi_student", $Language->MenuPhrase("4", "MenuText"), "studentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mmi_teachers", $Language->MenuPhrase("5", "MenuText"), "teacherslist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
