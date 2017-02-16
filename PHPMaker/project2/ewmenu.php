<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mi_book", $Language->MenuPhrase("1", "MenuText"), "booklist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mi_check_in", $Language->MenuPhrase("2", "MenuText"), "check_inlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(3, "mi_check_out", $Language->MenuPhrase("3", "MenuText"), "check_outlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(4, "mi_department", $Language->MenuPhrase("4", "MenuText"), "departmentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mi_student", $Language->MenuPhrase("5", "MenuText"), "studentlist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->AddMenuItem(6, "mi_teachers", $Language->MenuPhrase("6", "MenuText"), "teacherslist.php", -1, "", TRUE, FALSE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
