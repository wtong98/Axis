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

$checking_add = NULL; // Initialize page object first

class cchecking_add extends cchecking {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'checking';

	// Page object name
	var $PageObjName = 'checking_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
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

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
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

			// Handle modal response
			if ($this->IsModal) {
				$row = array();
				$row["url"] = $url;
				echo ew_ArrayToJson(array($row));
			} else {
				header("Location: " . $url);
			}
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $IsModal = FALSE;
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["book_id"] != "") {
				$this->book_id->setQueryStringValue($_GET["book_id"]);
				$this->setKey("book_id", $this->book_id->CurrentValue); // Set up key
			} else {
				$this->setKey("book_id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if (@$_GET["student_id"] != "") {
				$this->student_id->setQueryStringValue($_GET["student_id"]);
				$this->setKey("student_id", $this->student_id->CurrentValue); // Set up key
			} else {
				$this->setKey("student_id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		} else {
			if ($this->CurrentAction == "I") // Load default values for blank record
				$this->LoadDefaultValues();
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("checkinglist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "checkinglist.php")
						$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
					elseif (ew_GetPageName($sReturnUrl) == "checkingview.php")
						$sReturnUrl = $this->GetViewUrl(); // View page, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->book_id->CurrentValue = NULL;
		$this->book_id->OldValue = $this->book_id->CurrentValue;
		$this->book_title->CurrentValue = NULL;
		$this->book_title->OldValue = $this->book_title->CurrentValue;
		$this->student_id->CurrentValue = NULL;
		$this->student_id->OldValue = $this->student_id->CurrentValue;
		$this->date_checkedout->CurrentValue = NULL;
		$this->date_checkedout->OldValue = $this->date_checkedout->CurrentValue;
		$this->date_checkedin->CurrentValue = NULL;
		$this->date_checkedin->OldValue = $this->date_checkedin->CurrentValue;
		$this->year_checkedout->CurrentValue = NULL;
		$this->year_checkedout->OldValue = $this->year_checkedout->CurrentValue;
		$this->semester_checkedout->CurrentValue = NULL;
		$this->semester_checkedout->OldValue = $this->semester_checkedout->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->book_id->FldIsDetailKey) {
			$this->book_id->setFormValue($objForm->GetValue("x_book_id"));
		}
		if (!$this->book_title->FldIsDetailKey) {
			$this->book_title->setFormValue($objForm->GetValue("x_book_title"));
		}
		if (!$this->student_id->FldIsDetailKey) {
			$this->student_id->setFormValue($objForm->GetValue("x_student_id"));
		}
		if (!$this->date_checkedout->FldIsDetailKey) {
			$this->date_checkedout->setFormValue($objForm->GetValue("x_date_checkedout"));
			$this->date_checkedout->CurrentValue = ew_UnFormatDateTime($this->date_checkedout->CurrentValue, 0);
		}
		if (!$this->date_checkedin->FldIsDetailKey) {
			$this->date_checkedin->setFormValue($objForm->GetValue("x_date_checkedin"));
			$this->date_checkedin->CurrentValue = ew_UnFormatDateTime($this->date_checkedin->CurrentValue, 0);
		}
		if (!$this->year_checkedout->FldIsDetailKey) {
			$this->year_checkedout->setFormValue($objForm->GetValue("x_year_checkedout"));
		}
		if (!$this->semester_checkedout->FldIsDetailKey) {
			$this->semester_checkedout->setFormValue($objForm->GetValue("x_semester_checkedout"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->book_id->CurrentValue = $this->book_id->FormValue;
		$this->book_title->CurrentValue = $this->book_title->FormValue;
		$this->student_id->CurrentValue = $this->student_id->FormValue;
		$this->date_checkedout->CurrentValue = $this->date_checkedout->FormValue;
		$this->date_checkedout->CurrentValue = ew_UnFormatDateTime($this->date_checkedout->CurrentValue, 0);
		$this->date_checkedin->CurrentValue = $this->date_checkedin->FormValue;
		$this->date_checkedin->CurrentValue = ew_UnFormatDateTime($this->date_checkedin->CurrentValue, 0);
		$this->year_checkedout->CurrentValue = $this->year_checkedout->FormValue;
		$this->semester_checkedout->CurrentValue = $this->semester_checkedout->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("book_id")) <> "")
			$this->book_id->CurrentValue = $this->getKey("book_id"); // book_id
		else
			$bValidKey = FALSE;
		if (strval($this->getKey("student_id")) <> "")
			$this->student_id->CurrentValue = $this->getKey("student_id"); // student_id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// book_id
			$this->book_id->EditAttrs["class"] = "form-control";
			$this->book_id->EditCustomAttributes = "";
			$this->book_id->EditValue = ew_HtmlEncode($this->book_id->CurrentValue);
			$this->book_id->PlaceHolder = ew_RemoveHtml($this->book_id->FldCaption());

			// book_title
			$this->book_title->EditAttrs["class"] = "form-control";
			$this->book_title->EditCustomAttributes = "";
			$this->book_title->EditValue = ew_HtmlEncode($this->book_title->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->book_title->EditValue = $this->book_title->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->book_title->EditValue = ew_HtmlEncode($this->book_title->CurrentValue);
				}
			} else {
				$this->book_title->EditValue = NULL;
			}
			$this->book_title->PlaceHolder = ew_RemoveHtml($this->book_title->FldCaption());

			// student_id
			$this->student_id->EditAttrs["class"] = "form-control";
			$this->student_id->EditCustomAttributes = "";
			$this->student_id->EditValue = ew_HtmlEncode($this->student_id->CurrentValue);
			$this->student_id->PlaceHolder = ew_RemoveHtml($this->student_id->FldCaption());

			// date_checkedout
			$this->date_checkedout->EditAttrs["class"] = "form-control";
			$this->date_checkedout->EditCustomAttributes = "";
			$this->date_checkedout->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->date_checkedout->CurrentValue, 8));
			$this->date_checkedout->PlaceHolder = ew_RemoveHtml($this->date_checkedout->FldCaption());

			// date_checkedin
			$this->date_checkedin->EditAttrs["class"] = "form-control";
			$this->date_checkedin->EditCustomAttributes = "";
			$this->date_checkedin->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->date_checkedin->CurrentValue, 8));
			$this->date_checkedin->PlaceHolder = ew_RemoveHtml($this->date_checkedin->FldCaption());

			// year_checkedout
			$this->year_checkedout->EditAttrs["class"] = "form-control";
			$this->year_checkedout->EditCustomAttributes = "";
			$this->year_checkedout->EditValue = ew_HtmlEncode($this->year_checkedout->CurrentValue);
			$this->year_checkedout->PlaceHolder = ew_RemoveHtml($this->year_checkedout->FldCaption());

			// semester_checkedout
			$this->semester_checkedout->EditAttrs["class"] = "form-control";
			$this->semester_checkedout->EditCustomAttributes = "";
			$this->semester_checkedout->EditValue = ew_HtmlEncode($this->semester_checkedout->CurrentValue);
			$this->semester_checkedout->PlaceHolder = ew_RemoveHtml($this->semester_checkedout->FldCaption());

			// Add refer script
			// book_id

			$this->book_id->LinkCustomAttributes = "";
			$this->book_id->HrefValue = "";

			// book_title
			$this->book_title->LinkCustomAttributes = "";
			$this->book_title->HrefValue = "";

			// student_id
			$this->student_id->LinkCustomAttributes = "";
			$this->student_id->HrefValue = "";

			// date_checkedout
			$this->date_checkedout->LinkCustomAttributes = "";
			$this->date_checkedout->HrefValue = "";

			// date_checkedin
			$this->date_checkedin->LinkCustomAttributes = "";
			$this->date_checkedin->HrefValue = "";

			// year_checkedout
			$this->year_checkedout->LinkCustomAttributes = "";
			$this->year_checkedout->HrefValue = "";

			// semester_checkedout
			$this->semester_checkedout->LinkCustomAttributes = "";
			$this->semester_checkedout->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->book_id->FldIsDetailKey && !is_null($this->book_id->FormValue) && $this->book_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->book_id->FldCaption(), $this->book_id->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->book_id->FormValue)) {
			ew_AddMessage($gsFormError, $this->book_id->FldErrMsg());
		}
		if (!$this->student_id->FldIsDetailKey && !is_null($this->student_id->FormValue) && $this->student_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->student_id->FldCaption(), $this->student_id->ReqErrMsg));
		}
		if (!$this->date_checkedout->FldIsDetailKey && !is_null($this->date_checkedout->FormValue) && $this->date_checkedout->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->date_checkedout->FldCaption(), $this->date_checkedout->ReqErrMsg));
		}
		if (!ew_CheckDateDef($this->date_checkedout->FormValue)) {
			ew_AddMessage($gsFormError, $this->date_checkedout->FldErrMsg());
		}
		if (!ew_CheckDateDef($this->date_checkedin->FormValue)) {
			ew_AddMessage($gsFormError, $this->date_checkedin->FldErrMsg());
		}
		if (!ew_CheckInteger($this->year_checkedout->FormValue)) {
			ew_AddMessage($gsFormError, $this->year_checkedout->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// book_id
		$this->book_id->SetDbValueDef($rsnew, $this->book_id->CurrentValue, 0, FALSE);

		// book_title
		$this->book_title->SetDbValueDef($rsnew, $this->book_title->CurrentValue, NULL, FALSE);

		// student_id
		$this->student_id->SetDbValueDef($rsnew, $this->student_id->CurrentValue, "", FALSE);

		// date_checkedout
		$this->date_checkedout->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_checkedout->CurrentValue, 0), ew_CurrentDate(), FALSE);

		// date_checkedin
		$this->date_checkedin->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_checkedin->CurrentValue, 0), NULL, FALSE);

		// year_checkedout
		$this->year_checkedout->SetDbValueDef($rsnew, $this->year_checkedout->CurrentValue, NULL, FALSE);

		// semester_checkedout
		$this->semester_checkedout->SetDbValueDef($rsnew, $this->semester_checkedout->CurrentValue, NULL, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['book_id']) == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && strval($rsnew['student_id']) == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check for duplicate key
		if ($bInsertRow && $this->ValidateKey) {
			$sFilter = $this->KeyFilter();
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sKeyErrMsg = str_replace("%f", $sFilter, $Language->Phrase("DupKey"));
				$this->setFailureMessage($sKeyErrMsg);
				$rsChk->Close();
				$bInsertRow = FALSE;
			}
		}
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("checkinglist.php"), "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_book_title":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `title` AS `LinkFld`, `title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `book`";
			$sWhereWrk = "{filter}";
			$this->book_title->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`title` = {filter_value}', "t0" => "200", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->book_title, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_book_title":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `title`, `title` AS `DispFld` FROM `book`";
			$sWhereWrk = "`title` LIKE '{query_value}%'";
			$this->book_title->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->book_title, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($checking_add)) $checking_add = new cchecking_add();

// Page init
$checking_add->Page_Init();

// Page main
$checking_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$checking_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fcheckingadd = new ew_Form("fcheckingadd", "add");

// Validate form
fcheckingadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_book_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $checking->book_id->FldCaption(), $checking->book_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_book_id");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($checking->book_id->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_student_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $checking->student_id->FldCaption(), $checking->student_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_date_checkedout");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $checking->date_checkedout->FldCaption(), $checking->date_checkedout->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_date_checkedout");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($checking->date_checkedout->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_date_checkedin");
			if (elm && !ew_CheckDateDef(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($checking->date_checkedin->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_year_checkedout");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($checking->year_checkedout->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fcheckingadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fcheckingadd.ValidateRequired = true;
<?php } else { ?>
fcheckingadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fcheckingadd.Lists["x_book_title"] = {"LinkField":"x_title","Ajax":true,"AutoFill":false,"DisplayFields":["x_title","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"book"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$checking_add->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $checking_add->ShowPageHeader(); ?>
<?php
$checking_add->ShowMessage();
?>
<form name="fcheckingadd" id="fcheckingadd" class="<?php echo $checking_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($checking_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $checking_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="checking">
<input type="hidden" name="a_add" id="a_add" value="A">
<?php if ($checking_add->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($checking->book_id->Visible) { // book_id ?>
	<div id="r_book_id" class="form-group">
		<label id="elh_checking_book_id" for="x_book_id" class="col-sm-2 control-label ewLabel"><?php echo $checking->book_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $checking->book_id->CellAttributes() ?>>
<span id="el_checking_book_id">
<input type="text" data-table="checking" data-field="x_book_id" name="x_book_id" id="x_book_id" size="30" placeholder="<?php echo ew_HtmlEncode($checking->book_id->getPlaceHolder()) ?>" value="<?php echo $checking->book_id->EditValue ?>"<?php echo $checking->book_id->EditAttributes() ?>>
</span>
<?php echo $checking->book_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->book_title->Visible) { // book_title ?>
	<div id="r_book_title" class="form-group">
		<label id="elh_checking_book_title" class="col-sm-2 control-label ewLabel"><?php echo $checking->book_title->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $checking->book_title->CellAttributes() ?>>
<span id="el_checking_book_title">
<?php
$wrkonchange = trim(" " . @$checking->book_title->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$checking->book_title->EditAttrs["onchange"] = "";
?>
<span id="as_x_book_title" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_book_title" id="sv_x_book_title" value="<?php echo $checking->book_title->EditValue ?>" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($checking->book_title->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($checking->book_title->getPlaceHolder()) ?>"<?php echo $checking->book_title->EditAttributes() ?>>
</span>
<input type="hidden" data-table="checking" data-field="x_book_title" data-value-separator="<?php echo $checking->book_title->DisplayValueSeparatorAttribute() ?>" name="x_book_title" id="x_book_title" value="<?php echo ew_HtmlEncode($checking->book_title->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x_book_title" id="q_x_book_title" value="<?php echo $checking->book_title->LookupFilterQuery(true) ?>">
<script type="text/javascript">
fcheckingadd.CreateAutoSuggest({"id":"x_book_title","forceSelect":false});
</script>
</span>
<?php echo $checking->book_title->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->student_id->Visible) { // student_id ?>
	<div id="r_student_id" class="form-group">
		<label id="elh_checking_student_id" for="x_student_id" class="col-sm-2 control-label ewLabel"><?php echo $checking->student_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $checking->student_id->CellAttributes() ?>>
<span id="el_checking_student_id">
<input type="text" data-table="checking" data-field="x_student_id" name="x_student_id" id="x_student_id" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($checking->student_id->getPlaceHolder()) ?>" value="<?php echo $checking->student_id->EditValue ?>"<?php echo $checking->student_id->EditAttributes() ?>>
</span>
<?php echo $checking->student_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->date_checkedout->Visible) { // date_checkedout ?>
	<div id="r_date_checkedout" class="form-group">
		<label id="elh_checking_date_checkedout" for="x_date_checkedout" class="col-sm-2 control-label ewLabel"><?php echo $checking->date_checkedout->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $checking->date_checkedout->CellAttributes() ?>>
<span id="el_checking_date_checkedout">
<input type="text" data-table="checking" data-field="x_date_checkedout" name="x_date_checkedout" id="x_date_checkedout" placeholder="<?php echo ew_HtmlEncode($checking->date_checkedout->getPlaceHolder()) ?>" value="<?php echo $checking->date_checkedout->EditValue ?>"<?php echo $checking->date_checkedout->EditAttributes() ?>>
</span>
<?php echo $checking->date_checkedout->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->date_checkedin->Visible) { // date_checkedin ?>
	<div id="r_date_checkedin" class="form-group">
		<label id="elh_checking_date_checkedin" for="x_date_checkedin" class="col-sm-2 control-label ewLabel"><?php echo $checking->date_checkedin->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $checking->date_checkedin->CellAttributes() ?>>
<span id="el_checking_date_checkedin">
<input type="text" data-table="checking" data-field="x_date_checkedin" name="x_date_checkedin" id="x_date_checkedin" placeholder="<?php echo ew_HtmlEncode($checking->date_checkedin->getPlaceHolder()) ?>" value="<?php echo $checking->date_checkedin->EditValue ?>"<?php echo $checking->date_checkedin->EditAttributes() ?>>
</span>
<?php echo $checking->date_checkedin->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->year_checkedout->Visible) { // year_checkedout ?>
	<div id="r_year_checkedout" class="form-group">
		<label id="elh_checking_year_checkedout" for="x_year_checkedout" class="col-sm-2 control-label ewLabel"><?php echo $checking->year_checkedout->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $checking->year_checkedout->CellAttributes() ?>>
<span id="el_checking_year_checkedout">
<input type="text" data-table="checking" data-field="x_year_checkedout" name="x_year_checkedout" id="x_year_checkedout" size="30" placeholder="<?php echo ew_HtmlEncode($checking->year_checkedout->getPlaceHolder()) ?>" value="<?php echo $checking->year_checkedout->EditValue ?>"<?php echo $checking->year_checkedout->EditAttributes() ?>>
</span>
<?php echo $checking->year_checkedout->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($checking->semester_checkedout->Visible) { // semester_checkedout ?>
	<div id="r_semester_checkedout" class="form-group">
		<label id="elh_checking_semester_checkedout" for="x_semester_checkedout" class="col-sm-2 control-label ewLabel"><?php echo $checking->semester_checkedout->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $checking->semester_checkedout->CellAttributes() ?>>
<span id="el_checking_semester_checkedout">
<input type="text" data-table="checking" data-field="x_semester_checkedout" name="x_semester_checkedout" id="x_semester_checkedout" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($checking->semester_checkedout->getPlaceHolder()) ?>" value="<?php echo $checking->semester_checkedout->EditValue ?>"<?php echo $checking->semester_checkedout->EditAttributes() ?>>
</span>
<?php echo $checking->semester_checkedout->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$checking_add->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $checking_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fcheckingadd.Init();
</script>
<?php
$checking_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$checking_add->Page_Terminate();
?>
