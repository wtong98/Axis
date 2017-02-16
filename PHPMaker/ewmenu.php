<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mi_book", $Language->MenuPhrase("1", "MenuText"), "booklist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mi_checking", $Language->MenuPhrase("2", "MenuText"), "checkinglist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(3, "mi_department", $Language->MenuPhrase("3", "MenuText"), "departmentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(4, "mi_student", $Language->MenuPhrase("4", "MenuText"), "studentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mi_teachers", $Language->MenuPhrase("5", "MenuText"), "teacherslist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
