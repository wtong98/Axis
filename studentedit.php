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

$student_edit = NULL; // Initialize page object first

class cstudent_edit extends cstudent {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'student';

	// Page object name
	var $PageObjName = 'student_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $IsModal = FALSE;
	var $DbMasterFilter;
	var $DbDetailFilter;

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

		// Load key from QueryString
		if (@$_GET["stud_id"] <> "") {
			$this->stud_id->setQueryStringValue($_GET["stud_id"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->stud_id->CurrentValue == "") {
			$this->Page_Terminate("studentlist.php"); // Invalid key, return to list
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("studentlist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "studentlist.php")
					$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Render the record
		if ($this->CurrentAction == "F") { // Confirm page
			$this->RowType = EW_ROWTYPE_VIEW; // Render as View
		} else {
			$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		}
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->stud_id->FldIsDetailKey)
			$this->stud_id->setFormValue($objForm->GetValue("x_stud_id"));
		if (!$this->IMSA_id->FldIsDetailKey) {
			$this->IMSA_id->setFormValue($objForm->GetValue("x_IMSA_id"));
		}
		if (!$this->name->FldIsDetailKey) {
			$this->name->setFormValue($objForm->GetValue("x_name"));
		}
		if (!$this->grade->FldIsDetailKey) {
			$this->grade->setFormValue($objForm->GetValue("x_grade"));
		}
		if (!$this->_email->FldIsDetailKey) {
			$this->_email->setFormValue($objForm->GetValue("x__email"));
		}
		if (!$this->password->FldIsDetailKey) {
			$this->password->setFormValue($objForm->GetValue("x_password"));
		}
		if (!$this->owed->FldIsDetailKey) {
			$this->owed->setFormValue($objForm->GetValue("x_owed"));
		}
		if (!$this->parent_email->FldIsDetailKey) {
			$this->parent_email->setFormValue($objForm->GetValue("x_parent_email"));
		}
		if (!$this->parent_number->FldIsDetailKey) {
			$this->parent_number->setFormValue($objForm->GetValue("x_parent_number"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->stud_id->CurrentValue = $this->stud_id->FormValue;
		$this->IMSA_id->CurrentValue = $this->IMSA_id->FormValue;
		$this->name->CurrentValue = $this->name->FormValue;
		$this->grade->CurrentValue = $this->grade->FormValue;
		$this->_email->CurrentValue = $this->_email->FormValue;
		$this->password->CurrentValue = $this->password->FormValue;
		$this->owed->CurrentValue = $this->owed->FormValue;
		$this->parent_email->CurrentValue = $this->parent_email->FormValue;
		$this->parent_number->CurrentValue = $this->parent_number->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// stud_id
			$this->stud_id->EditAttrs["class"] = "form-control";
			$this->stud_id->EditCustomAttributes = "";
			$this->stud_id->EditValue = $this->stud_id->CurrentValue;
			$this->stud_id->ViewCustomAttributes = "";

			// IMSA_id
			$this->IMSA_id->EditAttrs["class"] = "form-control";
			$this->IMSA_id->EditCustomAttributes = "";
			$this->IMSA_id->EditValue = ew_HtmlEncode($this->IMSA_id->CurrentValue);
			$this->IMSA_id->PlaceHolder = ew_RemoveHtml($this->IMSA_id->FldCaption());

			// name
			$this->name->EditAttrs["class"] = "form-control";
			$this->name->EditCustomAttributes = "";
			$this->name->EditValue = ew_HtmlEncode($this->name->CurrentValue);
			$this->name->PlaceHolder = ew_RemoveHtml($this->name->FldCaption());

			// grade
			$this->grade->EditAttrs["class"] = "form-control";
			$this->grade->EditCustomAttributes = "";
			$this->grade->EditValue = ew_HtmlEncode($this->grade->CurrentValue);
			$this->grade->PlaceHolder = ew_RemoveHtml($this->grade->FldCaption());

			// email
			$this->_email->EditAttrs["class"] = "form-control";
			$this->_email->EditCustomAttributes = "";
			$this->_email->EditValue = ew_HtmlEncode($this->_email->CurrentValue);
			$this->_email->PlaceHolder = ew_RemoveHtml($this->_email->FldCaption());

			// password
			$this->password->EditAttrs["class"] = "form-control";
			$this->password->EditCustomAttributes = "";
			$this->password->EditValue = ew_HtmlEncode($this->password->CurrentValue);
			$this->password->PlaceHolder = ew_RemoveHtml($this->password->FldCaption());

			// owed
			$this->owed->EditAttrs["class"] = "form-control";
			$this->owed->EditCustomAttributes = "";
			$this->owed->EditValue = ew_HtmlEncode($this->owed->CurrentValue);
			$this->owed->PlaceHolder = ew_RemoveHtml($this->owed->FldCaption());

			// parent_email
			$this->parent_email->EditAttrs["class"] = "form-control";
			$this->parent_email->EditCustomAttributes = "";
			$this->parent_email->EditValue = ew_HtmlEncode($this->parent_email->CurrentValue);
			$this->parent_email->PlaceHolder = ew_RemoveHtml($this->parent_email->FldCaption());

			// parent_number
			$this->parent_number->EditAttrs["class"] = "form-control";
			$this->parent_number->EditCustomAttributes = "";
			$this->parent_number->EditValue = ew_HtmlEncode($this->parent_number->CurrentValue);
			$this->parent_number->PlaceHolder = ew_RemoveHtml($this->parent_number->FldCaption());

			// Edit refer script
			// stud_id

			$this->stud_id->LinkCustomAttributes = "";
			$this->stud_id->HrefValue = "";

			// IMSA_id
			$this->IMSA_id->LinkCustomAttributes = "";
			$this->IMSA_id->HrefValue = "";

			// name
			$this->name->LinkCustomAttributes = "";
			$this->name->HrefValue = "";

			// grade
			$this->grade->LinkCustomAttributes = "";
			$this->grade->HrefValue = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";

			// password
			$this->password->LinkCustomAttributes = "";
			$this->password->HrefValue = "";

			// owed
			$this->owed->LinkCustomAttributes = "";
			$this->owed->HrefValue = "";

			// parent_email
			$this->parent_email->LinkCustomAttributes = "";
			$this->parent_email->HrefValue = "";

			// parent_number
			$this->parent_number->LinkCustomAttributes = "";
			$this->parent_number->HrefValue = "";
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
		if (!$this->IMSA_id->FldIsDetailKey && !is_null($this->IMSA_id->FormValue) && $this->IMSA_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->IMSA_id->FldCaption(), $this->IMSA_id->ReqErrMsg));
		}
		if (!$this->name->FldIsDetailKey && !is_null($this->name->FormValue) && $this->name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->name->FldCaption(), $this->name->ReqErrMsg));
		}
		if (!$this->grade->FldIsDetailKey && !is_null($this->grade->FormValue) && $this->grade->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->grade->FldCaption(), $this->grade->ReqErrMsg));
		}
		if (!$this->_email->FldIsDetailKey && !is_null($this->_email->FormValue) && $this->_email->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->_email->FldCaption(), $this->_email->ReqErrMsg));
		}
		if (!ew_CheckEmail($this->_email->FormValue)) {
			ew_AddMessage($gsFormError, $this->_email->FldErrMsg());
		}
		if (!ew_CheckEmail($this->parent_email->FormValue)) {
			ew_AddMessage($gsFormError, $this->parent_email->FldErrMsg());
		}
		if (!ew_CheckPhone($this->parent_number->FormValue)) {
			ew_AddMessage($gsFormError, $this->parent_number->FldErrMsg());
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

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// IMSA_id
			$this->IMSA_id->SetDbValueDef($rsnew, $this->IMSA_id->CurrentValue, "", $this->IMSA_id->ReadOnly);

			// name
			$this->name->SetDbValueDef($rsnew, $this->name->CurrentValue, "", $this->name->ReadOnly);

			// grade
			$this->grade->SetDbValueDef($rsnew, $this->grade->CurrentValue, "", $this->grade->ReadOnly);

			// email
			$this->_email->SetDbValueDef($rsnew, $this->_email->CurrentValue, "", $this->_email->ReadOnly);

			// password
			$this->password->SetDbValueDef($rsnew, $this->password->CurrentValue, NULL, $this->password->ReadOnly);

			// owed
			$this->owed->SetDbValueDef($rsnew, $this->owed->CurrentValue, NULL, $this->owed->ReadOnly);

			// parent_email
			$this->parent_email->SetDbValueDef($rsnew, $this->parent_email->CurrentValue, NULL, $this->parent_email->ReadOnly);

			// parent_number
			$this->parent_number->SetDbValueDef($rsnew, $this->parent_number->CurrentValue, NULL, $this->parent_number->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("studentlist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($student_edit)) $student_edit = new cstudent_edit();

// Page init
$student_edit->Page_Init();

// Page main
$student_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$student_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fstudentedit = new ew_Form("fstudentedit", "edit");

// Validate form
fstudentedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_IMSA_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->IMSA_id->FldCaption(), $student->IMSA_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->name->FldCaption(), $student->name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_grade");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->grade->FldCaption(), $student->grade->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__email");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $student->_email->FldCaption(), $student->_email->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "__email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->_email->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_parent_email");
			if (elm && !ew_CheckEmail(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->parent_email->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_parent_number");
			if (elm && !ew_CheckPhone(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($student->parent_number->FldErrMsg()) ?>");

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
fstudentedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fstudentedit.ValidateRequired = true;
<?php } else { ?>
fstudentedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$student_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $student_edit->ShowPageHeader(); ?>
<?php
$student_edit->ShowMessage();
?>
<form name="fstudentedit" id="fstudentedit" class="<?php echo $student_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($student_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $student_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="student">
<?php if ($student->CurrentAction == "F") { // Confirm page ?>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<input type="hidden" name="a_confirm" id="a_confirm" value="F">
<?php } else { ?>
<input type="hidden" name="a_edit" id="a_edit" value="F">
<?php } ?>
<?php if ($student_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($student->stud_id->Visible) { // stud_id ?>
	<div id="r_stud_id" class="form-group">
		<label id="elh_student_stud_id" class="col-sm-2 control-label ewLabel"><?php echo $student->stud_id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->stud_id->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_stud_id">
<span<?php echo $student->stud_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->stud_id->EditValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_stud_id" name="x_stud_id" id="x_stud_id" value="<?php echo ew_HtmlEncode($student->stud_id->CurrentValue) ?>">
<?php } else { ?>
<span id="el_student_stud_id">
<span<?php echo $student->stud_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->stud_id->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_stud_id" name="x_stud_id" id="x_stud_id" value="<?php echo ew_HtmlEncode($student->stud_id->FormValue) ?>">
<?php } ?>
<?php echo $student->stud_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->IMSA_id->Visible) { // IMSA_id ?>
	<div id="r_IMSA_id" class="form-group">
		<label id="elh_student_IMSA_id" for="x_IMSA_id" class="col-sm-2 control-label ewLabel"><?php echo $student->IMSA_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->IMSA_id->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_IMSA_id">
<input type="text" data-table="student" data-field="x_IMSA_id" name="x_IMSA_id" id="x_IMSA_id" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($student->IMSA_id->getPlaceHolder()) ?>" value="<?php echo $student->IMSA_id->EditValue ?>"<?php echo $student->IMSA_id->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_IMSA_id">
<span<?php echo $student->IMSA_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->IMSA_id->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_IMSA_id" name="x_IMSA_id" id="x_IMSA_id" value="<?php echo ew_HtmlEncode($student->IMSA_id->FormValue) ?>">
<?php } ?>
<?php echo $student->IMSA_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->name->Visible) { // name ?>
	<div id="r_name" class="form-group">
		<label id="elh_student_name" for="x_name" class="col-sm-2 control-label ewLabel"><?php echo $student->name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->name->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_name">
<input type="text" data-table="student" data-field="x_name" name="x_name" id="x_name" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($student->name->getPlaceHolder()) ?>" value="<?php echo $student->name->EditValue ?>"<?php echo $student->name->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_name">
<span<?php echo $student->name->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->name->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_name" name="x_name" id="x_name" value="<?php echo ew_HtmlEncode($student->name->FormValue) ?>">
<?php } ?>
<?php echo $student->name->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->grade->Visible) { // grade ?>
	<div id="r_grade" class="form-group">
		<label id="elh_student_grade" for="x_grade" class="col-sm-2 control-label ewLabel"><?php echo $student->grade->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->grade->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_grade">
<input type="text" data-table="student" data-field="x_grade" name="x_grade" id="x_grade" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($student->grade->getPlaceHolder()) ?>" value="<?php echo $student->grade->EditValue ?>"<?php echo $student->grade->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_grade">
<span<?php echo $student->grade->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->grade->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_grade" name="x_grade" id="x_grade" value="<?php echo ew_HtmlEncode($student->grade->FormValue) ?>">
<?php } ?>
<?php echo $student->grade->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->_email->Visible) { // email ?>
	<div id="r__email" class="form-group">
		<label id="elh_student__email" for="x__email" class="col-sm-2 control-label ewLabel"><?php echo $student->_email->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $student->_email->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student__email">
<input type="text" data-table="student" data-field="x__email" name="x__email" id="x__email" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->_email->getPlaceHolder()) ?>" value="<?php echo $student->_email->EditValue ?>"<?php echo $student->_email->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student__email">
<span<?php echo $student->_email->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->_email->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x__email" name="x__email" id="x__email" value="<?php echo ew_HtmlEncode($student->_email->FormValue) ?>">
<?php } ?>
<?php echo $student->_email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->password->Visible) { // password ?>
	<div id="r_password" class="form-group">
		<label id="elh_student_password" for="x_password" class="col-sm-2 control-label ewLabel"><?php echo $student->password->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->password->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_password">
<input type="text" data-table="student" data-field="x_password" name="x_password" id="x_password" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->password->getPlaceHolder()) ?>" value="<?php echo $student->password->EditValue ?>"<?php echo $student->password->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_password">
<span<?php echo $student->password->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->password->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_password" name="x_password" id="x_password" value="<?php echo ew_HtmlEncode($student->password->FormValue) ?>">
<?php } ?>
<?php echo $student->password->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->owed->Visible) { // owed ?>
	<div id="r_owed" class="form-group">
		<label id="elh_student_owed" for="x_owed" class="col-sm-2 control-label ewLabel"><?php echo $student->owed->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->owed->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_owed">
<input type="text" data-table="student" data-field="x_owed" name="x_owed" id="x_owed" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->owed->getPlaceHolder()) ?>" value="<?php echo $student->owed->EditValue ?>"<?php echo $student->owed->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_owed">
<span<?php echo $student->owed->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->owed->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_owed" name="x_owed" id="x_owed" value="<?php echo ew_HtmlEncode($student->owed->FormValue) ?>">
<?php } ?>
<?php echo $student->owed->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->parent_email->Visible) { // parent_email ?>
	<div id="r_parent_email" class="form-group">
		<label id="elh_student_parent_email" for="x_parent_email" class="col-sm-2 control-label ewLabel"><?php echo $student->parent_email->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->parent_email->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_parent_email">
<input type="text" data-table="student" data-field="x_parent_email" name="x_parent_email" id="x_parent_email" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($student->parent_email->getPlaceHolder()) ?>" value="<?php echo $student->parent_email->EditValue ?>"<?php echo $student->parent_email->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_parent_email">
<span<?php echo $student->parent_email->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->parent_email->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_parent_email" name="x_parent_email" id="x_parent_email" value="<?php echo ew_HtmlEncode($student->parent_email->FormValue) ?>">
<?php } ?>
<?php echo $student->parent_email->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($student->parent_number->Visible) { // parent_number ?>
	<div id="r_parent_number" class="form-group">
		<label id="elh_student_parent_number" for="x_parent_number" class="col-sm-2 control-label ewLabel"><?php echo $student->parent_number->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $student->parent_number->CellAttributes() ?>>
<?php if ($student->CurrentAction <> "F") { ?>
<span id="el_student_parent_number">
<input type="text" data-table="student" data-field="x_parent_number" name="x_parent_number" id="x_parent_number" size="30" maxlength="15" placeholder="<?php echo ew_HtmlEncode($student->parent_number->getPlaceHolder()) ?>" value="<?php echo $student->parent_number->EditValue ?>"<?php echo $student->parent_number->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_student_parent_number">
<span<?php echo $student->parent_number->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $student->parent_number->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="student" data-field="x_parent_number" name="x_parent_number" id="x_parent_number" value="<?php echo ew_HtmlEncode($student->parent_number->FormValue) ?>">
<?php } ?>
<?php echo $student->parent_number->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$student_edit->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<?php if ($student->CurrentAction <> "F") { // Confirm page ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit" onclick="this.form.a_edit.value='F';"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $student_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } else { ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("ConfirmBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="submit" onclick="this.form.a_edit.value='X';"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } ?>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fstudentedit.Init();
</script>
<?php
$student_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$student_edit->Page_Terminate();
?>