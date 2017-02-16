<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "studentinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$student_delete = NULL; // Initialize page object first

class cstudent_delete extends cstudent {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (student)
		if (!isset($GLOBALS["student"]) || get_class($GLOBALS["student"]) == "cstudent") {
			$GLOBALS["student"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["student"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'student', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->stud_id->SetVisibility();
		$this->stud_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->IMSA_id->SetVisibility();
		$this->name->SetVisibility();
		$this->grade->SetVisibility();
		$this->_email->SetVisibility();
		$this->password->SetVisibility();
		$this->owed->SetVisibility();
		$this->parent_email->SetVisibility();
		$this->parent_number->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $student;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($student);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("studentlist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in student class, studentinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} elseif (@$_GET["a_delete"] == "1") {
			$this->CurrentAction = "D"; // Delete record directly
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		if ($this->CurrentAction == "D") {
			$this->SendEmail = TRUE; // Send email on delete success
			if ($this->DeleteRows()) { // Delete rows
				if ($this->getSuccessMessage() == "")
					$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
				$this->Page_Terminate($this->getReturnUrl()); // Return to caller
			} else { // Delete failed
				$this->CurrentAction = "I"; // Display record
			}
		}
		if ($this->CurrentAction == "I") { // Load records for display
			if ($this->Recordset = $this->LoadRecordset())
				$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
			if ($this->TotalRecs <= 0) { // No record found, exit
				if ($this->Recordset)
					$this->Recordset->Close();
				$this->Page_Terminate("studentlist.php"); // Return to list
			}
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->stud_id->setDbValue($rs->fields('stud_id'));
		$this->IMSA_id->setDbValue($rs->fields('IMSA_id'));
		$this->name->setDbValue($rs->fields('name'));
		$this->grade->setDbValue($rs->fields('grade'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->password->setDbValue($rs->fields('password'));
		$this->owed->setDbValue($rs->fields('owed'));
		$this->parent_email->setDbValue($rs->fields('parent_email'));
		$this->parent_number->setDbValue($rs->fields('parent_number'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->stud_id->DbValue = $row['stud_id'];
		$this->IMSA_id->DbValue = $row['IMSA_id'];
		$this->name->DbValue = $row['name'];
		$this->grade->DbValue = $row['grade'];
		$this->_email->DbValue = $row['email'];
		$this->password->DbValue = $row['password'];
		$this->owed->DbValue = $row['owed'];
		$this->parent_email->DbValue = $row['parent_email'];
		$this->parent_number->DbValue = $row['parent_number'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// stud_id
		// IMSA_id
		// name
		// grade
		// email
		// password
		// owed
		// parent_email
		// parent_number

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// stud_id
		$this->stud_id->ViewValue = $this->stud_id->CurrentValue;
		$this->stud_id->ViewCustomAttributes = "";

		// IMSA_id
		$this->IMSA_id->ViewValue = $this->IMSA_id->CurrentValue;
		$this->IMSA_id->ViewCustomAttributes = "";

		// name
		$this->name->ViewValue = $this->name->CurrentValue;
		$this->name->ViewCustomAttributes = "";

		// grade
		$this->grade->ViewValue = $this->grade->CurrentValue;
		$this->grade->ViewCustomAttributes = "";

		// email
		$this->_email->ViewValue = $this->_email->CurrentValue;
		$this->_email->ViewCustomAttributes = "";

		// password
		$this->password->ViewValue = $this->password->CurrentValue;
		$this->password->ViewCustomAttributes = "";

		// owed
		$this->owed->ViewValue = $this->owed->CurrentValue;
		$this->owed->ViewCustomAttributes = "";

		// parent_email
		$this->parent_email->ViewValue = $this->parent_email->CurrentValue;
		$this->parent_email->ViewCustomAttributes = "";

		// parent_number
		$this->parent_number->ViewValue = $this->parent_number->CurrentValue;
		$this->parent_number->ViewCustomAttributes = "";

			// stud_id
			$this->stud_id->LinkCustomAttributes = "";
			$this->stud_id->HrefValue = "";
			$this->stud_id->TooltipValue = "";

			// IMSA_id
			$this->IMSA_id->LinkCustomAttributes = "";
			$this->IMSA_id->HrefValue = "";
			$this->IMSA_id->TooltipValue = "";

			// name
			$this->name->LinkCustomAttributes = "";
			$this->name->HrefValue = "";
			$this->name->TooltipValue = "";

			// grade
			$this->grade->LinkCustomAttributes = "";
			$this->grade->HrefValue = "";
			$this->grade->TooltipValue = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";
			$this->_email->TooltipValue = "";

			// password
			$this->password->LinkCustomAttributes = "";
			$this->password->HrefValue = "";
			$this->password->TooltipValue = "";

			// owed
			$this->owed->LinkCustomAttributes = "";
			$this->owed->HrefValue = "";
			$this->owed->TooltipValue = "";

			// parent_email
			$this->parent_email->LinkCustomAttributes = "";
			$this->parent_email->HrefValue = "";
			$this->parent_email->TooltipValue = "";

			// parent_number
			$this->parent_number->LinkCustomAttributes = "";
			$this->parent_number->HrefValue = "";
			$this->parent_number->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		$conn->BeginTrans();

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['stud_id'];
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("studentlist.php"), "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($student_delete)) $student_delete = new cstudent_delete();

// Page init
$student_delete->Page_Init();

// Page main
$student_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fstudentdelete = new ew_Form("fstudentdelete", "delete");

// Form_CustomValidate event
fstudentdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentdelete.ValidateRequired = true;
<?php } else { ?>
fstudentdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $student_delete->ShowPageHeader(); ?>
<?php
$student_delete->ShowMessage();
?>
<form name="fstudentdelete" id="fstudentdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($student_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $student->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($student->stud_id->Visible) { // stud_id ?>
		<th><span id="elh_student_stud_id" class="student_stud_id"><?php echo $student->stud_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->IMSA_id->Visible) { // IMSA_id ?>
		<th><span id="elh_student_IMSA_id" class="student_IMSA_id"><?php echo $student->IMSA_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->name->Visible) { // name ?>
		<th><span id="elh_student_name" class="student_name"><?php echo $student->name->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->grade->Visible) { // grade ?>
		<th><span id="elh_student_grade" class="student_grade"><?php echo $student->grade->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->_email->Visible) { // email ?>
		<th><span id="elh_student__email" class="student__email"><?php echo $student->_email->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->password->Visible) { // password ?>
		<th><span id="elh_student_password" class="student_password"><?php echo $student->password->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->owed->Visible) { // owed ?>
		<th><span id="elh_student_owed" class="student_owed"><?php echo $student->owed->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->parent_email->Visible) { // parent_email ?>
		<th><span id="elh_student_parent_email" class="student_parent_email"><?php echo $student->parent_email->FldCaption() ?></span></th>
<?php } ?>
<?php if ($student->parent_number->Visible) { // parent_number ?>
		<th><span id="elh_student_parent_number" class="student_parent_number"><?php echo $student->parent_number->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$student_delete->RecCnt = 0;
$i = 0;
while (!$student_delete->Recordset->EOF) {
	$student_delete->RecCnt++;
	$student_delete->RowCnt++;

	// Set row properties
	$student->ResetAttrs();
	$student->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$student_delete->LoadRowValues($student_delete->Recordset);

	// Render row
	$student_delete->RenderRow();
?>
	<tr<?php echo $student->RowAttributes() ?>>
<?php if ($student->stud_id->Visible) { // stud_id ?>
		<td<?php echo $student->stud_id->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_stud_id" class="student_stud_id">
<span<?php echo $student->stud_id->ViewAttributes() ?>>
<?php echo $student->stud_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->IMSA_id->Visible) { // IMSA_id ?>
		<td<?php echo $student->IMSA_id->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_IMSA_id" class="student_IMSA_id">
<span<?php echo $student->IMSA_id->ViewAttributes() ?>>
<?php echo $student->IMSA_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->name->Visible) { // name ?>
		<td<?php echo $student->name->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_name" class="student_name">
<span<?php echo $student->name->ViewAttributes() ?>>
<?php echo $student->name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->grade->Visible) { // grade ?>
		<td<?php echo $student->grade->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_grade" class="student_grade">
<span<?php echo $student->grade->ViewAttributes() ?>>
<?php echo $student->grade->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->_email->Visible) { // email ?>
		<td<?php echo $student->_email->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student__email" class="student__email">
<span<?php echo $student->_email->ViewAttributes() ?>>
<?php echo $student->_email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->password->Visible) { // password ?>
		<td<?php echo $student->password->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_password" class="student_password">
<span<?php echo $student->password->ViewAttributes() ?>>
<?php echo $student->password->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->owed->Visible) { // owed ?>
		<td<?php echo $student->owed->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_owed" class="student_owed">
<span<?php echo $student->owed->ViewAttributes() ?>>
<?php echo $student->owed->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->parent_email->Visible) { // parent_email ?>
		<td<?php echo $student->parent_email->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_parent_email" class="student_parent_email">
<span<?php echo $student->parent_email->ViewAttributes() ?>>
<?php echo $student->parent_email->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($student->parent_number->Visible) { // parent_number ?>
		<td<?php echo $student->parent_number->CellAttributes() ?>>
<span id="el<?php echo $student_delete->RowCnt ?>_student_parent_number" class="student_parent_number">
<span<?php echo $student->parent_number->ViewAttributes() ?>>
<?php echo $student->parent_number->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$student_delete->Recordset->MoveNext();
}
$student_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $student_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fstudentdelete.Init();
</script>
<?php
$student_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$student_delete->Page_Terminate();
?>
