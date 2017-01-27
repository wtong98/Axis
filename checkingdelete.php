<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "checkinginfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$checking_delete = NULL; // Initialize page object first

class cchecking_delete extends cchecking {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'checking';

	// Page object name
	var $PageObjName = 'checking_delete';

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

		// Table object (checking)
		if (!isset($GLOBALS["checking"]) || get_class($GLOBALS["checking"]) == "cchecking") {
			$GLOBALS["checking"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["checking"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'checking', TRUE);

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
		$this->book_id->SetVisibility();
		$this->book_title->SetVisibility();
		$this->student_id->SetVisibility();
		$this->date_checkedout->SetVisibility();
		$this->date_checkedin->SetVisibility();
		$this->year_checkedout->SetVisibility();
		$this->semester_checkedout->SetVisibility();

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
		global $EW_EXPORT, $checking;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($checking);
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
			$this->Page_Terminate("checkinglist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in checking class, checkinginfo.php

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
				$this->Page_Terminate("checkinglist.php"); // Return to list
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
		$this->book_id->setDbValue($rs->fields('book_id'));
		$this->book_title->setDbValue($rs->fields('book_title'));
		$this->student_id->setDbValue($rs->fields('student_id'));
		$this->date_checkedout->setDbValue($rs->fields('date_checkedout'));
		$this->date_checkedin->setDbValue($rs->fields('date_checkedin'));
		$this->year_checkedout->setDbValue($rs->fields('year_checkedout'));
		$this->semester_checkedout->setDbValue($rs->fields('semester_checkedout'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->book_id->DbValue = $row['book_id'];
		$this->book_title->DbValue = $row['book_title'];
		$this->student_id->DbValue = $row['student_id'];
		$this->date_checkedout->DbValue = $row['date_checkedout'];
		$this->date_checkedin->DbValue = $row['date_checkedin'];
		$this->year_checkedout->DbValue = $row['year_checkedout'];
		$this->semester_checkedout->DbValue = $row['semester_checkedout'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// book_id
		// book_title
		// student_id
		// date_checkedout
		// date_checkedin
		// year_checkedout
		// semester_checkedout

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// book_id
		$this->book_id->ViewValue = $this->book_id->CurrentValue;
		$this->book_id->ViewCustomAttributes = "";

		// book_title
		$this->book_title->ViewValue = $this->book_title->CurrentValue;
		if (strval($this->book_title->CurrentValue) <> "") {
			$sFilterWrk = "`title`" . ew_SearchString("=", $this->book_title->CurrentValue, EW_DATATYPE_STRING, "");
		$sSqlWrk = "SELECT `title`, `title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `book`";
		$sWhereWrk = "";
		$this->book_title->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->book_title, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->book_title->ViewValue = $this->book_title->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->book_title->ViewValue = $this->book_title->CurrentValue;
			}
		} else {
			$this->book_title->ViewValue = NULL;
		}
		$this->book_title->ViewCustomAttributes = "";

		// student_id
		$this->student_id->ViewValue = $this->student_id->CurrentValue;
		$this->student_id->ViewCustomAttributes = "";

		// date_checkedout
		$this->date_checkedout->ViewValue = $this->date_checkedout->CurrentValue;
		$this->date_checkedout->ViewValue = ew_FormatDateTime($this->date_checkedout->ViewValue, 0);
		$this->date_checkedout->ViewCustomAttributes = "";

		// date_checkedin
		$this->date_checkedin->ViewValue = $this->date_checkedin->CurrentValue;
		$this->date_checkedin->ViewValue = ew_FormatDateTime($this->date_checkedin->ViewValue, 0);
		$this->date_checkedin->ViewCustomAttributes = "";

		// year_checkedout
		$this->year_checkedout->ViewValue = $this->year_checkedout->CurrentValue;
		$this->year_checkedout->ViewCustomAttributes = "";

		// semester_checkedout
		$this->semester_checkedout->ViewValue = $this->semester_checkedout->CurrentValue;
		$this->semester_checkedout->ViewCustomAttributes = "";

			// book_id
			$this->book_id->LinkCustomAttributes = "";
			$this->book_id->HrefValue = "";
			$this->book_id->TooltipValue = "";

			// book_title
			$this->book_title->LinkCustomAttributes = "";
			$this->book_title->HrefValue = "";
			$this->book_title->TooltipValue = "";

			// student_id
			$this->student_id->LinkCustomAttributes = "";
			$this->student_id->HrefValue = "";
			$this->student_id->TooltipValue = "";

			// date_checkedout
			$this->date_checkedout->LinkCustomAttributes = "";
			$this->date_checkedout->HrefValue = "";
			$this->date_checkedout->TooltipValue = "";

			// date_checkedin
			$this->date_checkedin->LinkCustomAttributes = "";
			$this->date_checkedin->HrefValue = "";
			$this->date_checkedin->TooltipValue = "";

			// year_checkedout
			$this->year_checkedout->LinkCustomAttributes = "";
			$this->year_checkedout->HrefValue = "";
			$this->year_checkedout->TooltipValue = "";

			// semester_checkedout
			$this->semester_checkedout->LinkCustomAttributes = "";
			$this->semester_checkedout->HrefValue = "";
			$this->semester_checkedout->TooltipValue = "";
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
				$sThisKey .= $row['book_id'];
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['student_id'];
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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("checkinglist.php"), "", $this->TableVar, TRUE);
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
if (!isset($checking_delete)) $checking_delete = new cchecking_delete();

// Page init
$checking_delete->Page_Init();

// Page main
$checking_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$checking_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fcheckingdelete = new ew_Form("fcheckingdelete", "delete");

// Form_CustomValidate event
fcheckingdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcheckingdelete.ValidateRequired = true;
<?php } else { ?>
fcheckingdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcheckingdelete.Lists["x_book_title"] = {"LinkField":"x_title","Ajax":true,"AutoFill":false,"DisplayFields":["x_title","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"book"};

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
<?php $checking_delete->ShowPageHeader(); ?>
<?php
$checking_delete->ShowMessage();
?>
<form name="fcheckingdelete" id="fcheckingdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($checking_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $checking_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="checking">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($checking_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $checking->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($checking->book_id->Visible) { // book_id ?>
		<th><span id="elh_checking_book_id" class="checking_book_id"><?php echo $checking->book_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->book_title->Visible) { // book_title ?>
		<th><span id="elh_checking_book_title" class="checking_book_title"><?php echo $checking->book_title->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->student_id->Visible) { // student_id ?>
		<th><span id="elh_checking_student_id" class="checking_student_id"><?php echo $checking->student_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->date_checkedout->Visible) { // date_checkedout ?>
		<th><span id="elh_checking_date_checkedout" class="checking_date_checkedout"><?php echo $checking->date_checkedout->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->date_checkedin->Visible) { // date_checkedin ?>
		<th><span id="elh_checking_date_checkedin" class="checking_date_checkedin"><?php echo $checking->date_checkedin->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->year_checkedout->Visible) { // year_checkedout ?>
		<th><span id="elh_checking_year_checkedout" class="checking_year_checkedout"><?php echo $checking->year_checkedout->FldCaption() ?></span></th>
<?php } ?>
<?php if ($checking->semester_checkedout->Visible) { // semester_checkedout ?>
		<th><span id="elh_checking_semester_checkedout" class="checking_semester_checkedout"><?php echo $checking->semester_checkedout->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$checking_delete->RecCnt = 0;
$i = 0;
while (!$checking_delete->Recordset->EOF) {
	$checking_delete->RecCnt++;
	$checking_delete->RowCnt++;

	// Set row properties
	$checking->ResetAttrs();
	$checking->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$checking_delete->LoadRowValues($checking_delete->Recordset);

	// Render row
	$checking_delete->RenderRow();
?>
	<tr<?php echo $checking->RowAttributes() ?>>
<?php if ($checking->book_id->Visible) { // book_id ?>
		<td<?php echo $checking->book_id->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_book_id" class="checking_book_id">
<span<?php echo $checking->book_id->ViewAttributes() ?>>
<?php echo $checking->book_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->book_title->Visible) { // book_title ?>
		<td<?php echo $checking->book_title->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_book_title" class="checking_book_title">
<span<?php echo $checking->book_title->ViewAttributes() ?>>
<?php echo $checking->book_title->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->student_id->Visible) { // student_id ?>
		<td<?php echo $checking->student_id->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_student_id" class="checking_student_id">
<span<?php echo $checking->student_id->ViewAttributes() ?>>
<?php echo $checking->student_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->date_checkedout->Visible) { // date_checkedout ?>
		<td<?php echo $checking->date_checkedout->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_date_checkedout" class="checking_date_checkedout">
<span<?php echo $checking->date_checkedout->ViewAttributes() ?>>
<?php echo $checking->date_checkedout->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->date_checkedin->Visible) { // date_checkedin ?>
		<td<?php echo $checking->date_checkedin->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_date_checkedin" class="checking_date_checkedin">
<span<?php echo $checking->date_checkedin->ViewAttributes() ?>>
<?php echo $checking->date_checkedin->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->year_checkedout->Visible) { // year_checkedout ?>
		<td<?php echo $checking->year_checkedout->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_year_checkedout" class="checking_year_checkedout">
<span<?php echo $checking->year_checkedout->ViewAttributes() ?>>
<?php echo $checking->year_checkedout->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($checking->semester_checkedout->Visible) { // semester_checkedout ?>
		<td<?php echo $checking->semester_checkedout->CellAttributes() ?>>
<span id="el<?php echo $checking_delete->RowCnt ?>_checking_semester_checkedout" class="checking_semester_checkedout">
<span<?php echo $checking->semester_checkedout->ViewAttributes() ?>>
<?php echo $checking->semester_checkedout->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$checking_delete->Recordset->MoveNext();
}
$checking_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $checking_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fcheckingdelete.Init();
</script>
<?php
$checking_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$checking_delete->Page_Terminate();
?>
