<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "bookinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$book_edit = NULL; // Initialize page object first

class cbook_edit extends cbook {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'book';

	// Page object name
	var $PageObjName = 'book_edit';

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

		// Table object (book)
		if (!isset($GLOBALS["book"]) || get_class($GLOBALS["book"]) == "cbook") {
			$GLOBALS["book"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["book"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'book', TRUE);

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
		$this->book_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->IMSA_id->SetVisibility();
		$this->title->SetVisibility();
		$this->ISBN->SetVisibility();
		$this->cost->SetVisibility();
		$this->fee->SetVisibility();
		$this->dept_id->SetVisibility();

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
		global $EW_EXPORT, $book;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($book);
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
		if (@$_GET["book_id"] <> "") {
			$this->book_id->setQueryStringValue($_GET["book_id"]);
		}

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->book_id->CurrentValue == "") {
			$this->Page_Terminate("booklist.php"); // Invalid key, return to list
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
					$this->Page_Terminate("booklist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "booklist.php")
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
		if (!$this->book_id->FldIsDetailKey)
			$this->book_id->setFormValue($objForm->GetValue("x_book_id"));
		if (!$this->IMSA_id->FldIsDetailKey) {
			$this->IMSA_id->setFormValue($objForm->GetValue("x_IMSA_id"));
		}
		if (!$this->title->FldIsDetailKey) {
			$this->title->setFormValue($objForm->GetValue("x_title"));
		}
		if (!$this->ISBN->FldIsDetailKey) {
			$this->ISBN->setFormValue($objForm->GetValue("x_ISBN"));
		}
		if (!$this->cost->FldIsDetailKey) {
			$this->cost->setFormValue($objForm->GetValue("x_cost"));
		}
		if (!$this->fee->FldIsDetailKey) {
			$this->fee->setFormValue($objForm->GetValue("x_fee"));
		}
		if (!$this->dept_id->FldIsDetailKey) {
			$this->dept_id->setFormValue($objForm->GetValue("x_dept_id"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->book_id->CurrentValue = $this->book_id->FormValue;
		$this->IMSA_id->CurrentValue = $this->IMSA_id->FormValue;
		$this->title->CurrentValue = $this->title->FormValue;
		$this->ISBN->CurrentValue = $this->ISBN->FormValue;
		$this->cost->CurrentValue = $this->cost->FormValue;
		$this->fee->CurrentValue = $this->fee->FormValue;
		$this->dept_id->CurrentValue = $this->dept_id->FormValue;
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
		$this->IMSA_id->setDbValue($rs->fields('IMSA_id'));
		$this->title->setDbValue($rs->fields('title'));
		$this->ISBN->setDbValue($rs->fields('ISBN'));
		$this->cost->setDbValue($rs->fields('cost'));
		$this->fee->setDbValue($rs->fields('fee'));
		$this->dept_id->setDbValue($rs->fields('dept_id'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->book_id->DbValue = $row['book_id'];
		$this->IMSA_id->DbValue = $row['IMSA_id'];
		$this->title->DbValue = $row['title'];
		$this->ISBN->DbValue = $row['ISBN'];
		$this->cost->DbValue = $row['cost'];
		$this->fee->DbValue = $row['fee'];
		$this->dept_id->DbValue = $row['dept_id'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// book_id
		// IMSA_id
		// title
		// ISBN
		// cost
		// fee
		// dept_id

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// book_id
		$this->book_id->ViewValue = $this->book_id->CurrentValue;
		$this->book_id->ViewCustomAttributes = "";

		// IMSA_id
		$this->IMSA_id->ViewValue = $this->IMSA_id->CurrentValue;
		$this->IMSA_id->ViewCustomAttributes = "";

		// title
		$this->title->ViewValue = $this->title->CurrentValue;
		$this->title->ViewCustomAttributes = "";

		// ISBN
		$this->ISBN->ViewValue = $this->ISBN->CurrentValue;
		$this->ISBN->ViewCustomAttributes = "";

		// cost
		$this->cost->ViewValue = $this->cost->CurrentValue;
		$this->cost->ViewCustomAttributes = "";

		// fee
		$this->fee->ViewValue = $this->fee->CurrentValue;
		$this->fee->ViewCustomAttributes = "";

		// dept_id
		if (strval($this->dept_id->CurrentValue) <> "") {
			$sFilterWrk = "`dept_id`" . ew_SearchString("=", $this->dept_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `dept_id`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `department`";
		$sWhereWrk = "";
		$this->dept_id->LookupFilters = array();
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->dept_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->dept_id->ViewValue = $this->dept_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->dept_id->ViewValue = $this->dept_id->CurrentValue;
			}
		} else {
			$this->dept_id->ViewValue = NULL;
		}
		$this->dept_id->ViewCustomAttributes = "";

			// book_id
			$this->book_id->LinkCustomAttributes = "";
			$this->book_id->HrefValue = "";
			$this->book_id->TooltipValue = "";

			// IMSA_id
			$this->IMSA_id->LinkCustomAttributes = "";
			$this->IMSA_id->HrefValue = "";
			$this->IMSA_id->TooltipValue = "";

			// title
			$this->title->LinkCustomAttributes = "";
			$this->title->HrefValue = "";
			$this->title->TooltipValue = "";

			// ISBN
			$this->ISBN->LinkCustomAttributes = "";
			$this->ISBN->HrefValue = "";
			$this->ISBN->TooltipValue = "";

			// cost
			$this->cost->LinkCustomAttributes = "";
			$this->cost->HrefValue = "";
			$this->cost->TooltipValue = "";

			// fee
			$this->fee->LinkCustomAttributes = "";
			$this->fee->HrefValue = "";
			$this->fee->TooltipValue = "";

			// dept_id
			$this->dept_id->LinkCustomAttributes = "";
			$this->dept_id->HrefValue = "";
			$this->dept_id->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// book_id
			$this->book_id->EditAttrs["class"] = "form-control";
			$this->book_id->EditCustomAttributes = "";
			$this->book_id->EditValue = $this->book_id->CurrentValue;
			$this->book_id->ViewCustomAttributes = "";

			// IMSA_id
			$this->IMSA_id->EditAttrs["class"] = "form-control";
			$this->IMSA_id->EditCustomAttributes = "";
			$this->IMSA_id->EditValue = ew_HtmlEncode($this->IMSA_id->CurrentValue);
			$this->IMSA_id->PlaceHolder = ew_RemoveHtml($this->IMSA_id->FldCaption());

			// title
			$this->title->EditAttrs["class"] = "form-control";
			$this->title->EditCustomAttributes = "";
			$this->title->EditValue = ew_HtmlEncode($this->title->CurrentValue);
			$this->title->PlaceHolder = ew_RemoveHtml($this->title->FldCaption());

			// ISBN
			$this->ISBN->EditAttrs["class"] = "form-control";
			$this->ISBN->EditCustomAttributes = "";
			$this->ISBN->EditValue = ew_HtmlEncode($this->ISBN->CurrentValue);
			$this->ISBN->PlaceHolder = ew_RemoveHtml($this->ISBN->FldCaption());

			// cost
			$this->cost->EditAttrs["class"] = "form-control";
			$this->cost->EditCustomAttributes = "";
			$this->cost->EditValue = ew_HtmlEncode($this->cost->CurrentValue);
			$this->cost->PlaceHolder = ew_RemoveHtml($this->cost->FldCaption());

			// fee
			$this->fee->EditAttrs["class"] = "form-control";
			$this->fee->EditCustomAttributes = "";
			$this->fee->EditValue = ew_HtmlEncode($this->fee->CurrentValue);
			$this->fee->PlaceHolder = ew_RemoveHtml($this->fee->FldCaption());

			// dept_id
			$this->dept_id->EditCustomAttributes = "";
			if (trim(strval($this->dept_id->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`dept_id`" . ew_SearchString("=", $this->dept_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `dept_id`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `department`";
			$sWhereWrk = "";
			$this->dept_id->LookupFilters = array();
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->dept_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->dept_id->ViewValue = $this->dept_id->DisplayValue($arwrk);
			} else {
				$this->dept_id->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			$this->dept_id->EditValue = $arwrk;

			// Edit refer script
			// book_id

			$this->book_id->LinkCustomAttributes = "";
			$this->book_id->HrefValue = "";

			// IMSA_id
			$this->IMSA_id->LinkCustomAttributes = "";
			$this->IMSA_id->HrefValue = "";

			// title
			$this->title->LinkCustomAttributes = "";
			$this->title->HrefValue = "";

			// ISBN
			$this->ISBN->LinkCustomAttributes = "";
			$this->ISBN->HrefValue = "";

			// cost
			$this->cost->LinkCustomAttributes = "";
			$this->cost->HrefValue = "";

			// fee
			$this->fee->LinkCustomAttributes = "";
			$this->fee->HrefValue = "";

			// dept_id
			$this->dept_id->LinkCustomAttributes = "";
			$this->dept_id->HrefValue = "";
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
		if (!$this->title->FldIsDetailKey && !is_null($this->title->FormValue) && $this->title->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->title->FldCaption(), $this->title->ReqErrMsg));
		}
		if (!$this->ISBN->FldIsDetailKey && !is_null($this->ISBN->FormValue) && $this->ISBN->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->ISBN->FldCaption(), $this->ISBN->ReqErrMsg));
		}
		if (!$this->dept_id->FldIsDetailKey && !is_null($this->dept_id->FormValue) && $this->dept_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->dept_id->FldCaption(), $this->dept_id->ReqErrMsg));
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

			// title
			$this->title->SetDbValueDef($rsnew, $this->title->CurrentValue, "", $this->title->ReadOnly);

			// ISBN
			$this->ISBN->SetDbValueDef($rsnew, $this->ISBN->CurrentValue, "", $this->ISBN->ReadOnly);

			// cost
			$this->cost->SetDbValueDef($rsnew, $this->cost->CurrentValue, NULL, $this->cost->ReadOnly);

			// fee
			$this->fee->SetDbValueDef($rsnew, $this->fee->CurrentValue, NULL, $this->fee->ReadOnly);

			// dept_id
			$this->dept_id->SetDbValueDef($rsnew, $this->dept_id->CurrentValue, 0, $this->dept_id->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("booklist.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_dept_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `dept_id` AS `LinkFld`, `name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `department`";
			$sWhereWrk = "";
			$this->dept_id->LookupFilters = array();
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`dept_id` = {filter_value}', "t0" => "19", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->dept_id, $sWhereWrk); // Call Lookup selecting
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
if (!isset($book_edit)) $book_edit = new cbook_edit();

// Page init
$book_edit->Page_Init();

// Page main
$book_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$book_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fbookedit = new ew_Form("fbookedit", "edit");

// Validate form
fbookedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $book->IMSA_id->FldCaption(), $book->IMSA_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_title");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $book->title->FldCaption(), $book->title->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_ISBN");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $book->ISBN->FldCaption(), $book->ISBN->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_dept_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $book->dept_id->FldCaption(), $book->dept_id->ReqErrMsg)) ?>");

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
fbookedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbookedit.ValidateRequired = true;
<?php } else { ?>
fbookedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbookedit.Lists["x_dept_id"] = {"LinkField":"x_dept_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_name","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"department"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$book_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $book_edit->ShowPageHeader(); ?>
<?php
$book_edit->ShowMessage();
?>
<form name="fbookedit" id="fbookedit" class="<?php echo $book_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($book_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $book_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="book">
<?php if ($book->CurrentAction == "F") { // Confirm page ?>
<input type="hidden" name="a_edit" id="a_edit" value="U">
<input type="hidden" name="a_confirm" id="a_confirm" value="F">
<?php } else { ?>
<input type="hidden" name="a_edit" id="a_edit" value="F">
<?php } ?>
<?php if ($book_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($book->book_id->Visible) { // book_id ?>
	<div id="r_book_id" class="form-group">
		<label id="elh_book_book_id" class="col-sm-2 control-label ewLabel"><?php echo $book->book_id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $book->book_id->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_book_id">
<span<?php echo $book->book_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->book_id->EditValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_book_id" name="x_book_id" id="x_book_id" value="<?php echo ew_HtmlEncode($book->book_id->CurrentValue) ?>">
<?php } else { ?>
<span id="el_book_book_id">
<span<?php echo $book->book_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->book_id->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_book_id" name="x_book_id" id="x_book_id" value="<?php echo ew_HtmlEncode($book->book_id->FormValue) ?>">
<?php } ?>
<?php echo $book->book_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->IMSA_id->Visible) { // IMSA_id ?>
	<div id="r_IMSA_id" class="form-group">
		<label id="elh_book_IMSA_id" for="x_IMSA_id" class="col-sm-2 control-label ewLabel"><?php echo $book->IMSA_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $book->IMSA_id->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_IMSA_id">
<input type="text" data-table="book" data-field="x_IMSA_id" name="x_IMSA_id" id="x_IMSA_id" size="30" maxlength="15" placeholder="<?php echo ew_HtmlEncode($book->IMSA_id->getPlaceHolder()) ?>" value="<?php echo $book->IMSA_id->EditValue ?>"<?php echo $book->IMSA_id->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_book_IMSA_id">
<span<?php echo $book->IMSA_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->IMSA_id->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_IMSA_id" name="x_IMSA_id" id="x_IMSA_id" value="<?php echo ew_HtmlEncode($book->IMSA_id->FormValue) ?>">
<?php } ?>
<?php echo $book->IMSA_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->title->Visible) { // title ?>
	<div id="r_title" class="form-group">
		<label id="elh_book_title" for="x_title" class="col-sm-2 control-label ewLabel"><?php echo $book->title->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $book->title->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_title">
<input type="text" data-table="book" data-field="x_title" name="x_title" id="x_title" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($book->title->getPlaceHolder()) ?>" value="<?php echo $book->title->EditValue ?>"<?php echo $book->title->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_book_title">
<span<?php echo $book->title->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->title->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_title" name="x_title" id="x_title" value="<?php echo ew_HtmlEncode($book->title->FormValue) ?>">
<?php } ?>
<?php echo $book->title->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->ISBN->Visible) { // ISBN ?>
	<div id="r_ISBN" class="form-group">
		<label id="elh_book_ISBN" for="x_ISBN" class="col-sm-2 control-label ewLabel"><?php echo $book->ISBN->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $book->ISBN->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_ISBN">
<input type="text" data-table="book" data-field="x_ISBN" name="x_ISBN" id="x_ISBN" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($book->ISBN->getPlaceHolder()) ?>" value="<?php echo $book->ISBN->EditValue ?>"<?php echo $book->ISBN->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_book_ISBN">
<span<?php echo $book->ISBN->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->ISBN->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_ISBN" name="x_ISBN" id="x_ISBN" value="<?php echo ew_HtmlEncode($book->ISBN->FormValue) ?>">
<?php } ?>
<?php echo $book->ISBN->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->cost->Visible) { // cost ?>
	<div id="r_cost" class="form-group">
		<label id="elh_book_cost" for="x_cost" class="col-sm-2 control-label ewLabel"><?php echo $book->cost->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $book->cost->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_cost">
<input type="text" data-table="book" data-field="x_cost" name="x_cost" id="x_cost" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->cost->getPlaceHolder()) ?>" value="<?php echo $book->cost->EditValue ?>"<?php echo $book->cost->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_book_cost">
<span<?php echo $book->cost->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->cost->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_cost" name="x_cost" id="x_cost" value="<?php echo ew_HtmlEncode($book->cost->FormValue) ?>">
<?php } ?>
<?php echo $book->cost->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->fee->Visible) { // fee ?>
	<div id="r_fee" class="form-group">
		<label id="elh_book_fee" for="x_fee" class="col-sm-2 control-label ewLabel"><?php echo $book->fee->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $book->fee->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_fee">
<input type="text" data-table="book" data-field="x_fee" name="x_fee" id="x_fee" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->fee->getPlaceHolder()) ?>" value="<?php echo $book->fee->EditValue ?>"<?php echo $book->fee->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_book_fee">
<span<?php echo $book->fee->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->fee->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_fee" name="x_fee" id="x_fee" value="<?php echo ew_HtmlEncode($book->fee->FormValue) ?>">
<?php } ?>
<?php echo $book->fee->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($book->dept_id->Visible) { // dept_id ?>
	<div id="r_dept_id" class="form-group">
		<label id="elh_book_dept_id" for="x_dept_id" class="col-sm-2 control-label ewLabel"><?php echo $book->dept_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $book->dept_id->CellAttributes() ?>>
<?php if ($book->CurrentAction <> "F") { ?>
<span id="el_book_dept_id">
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $book->dept_id->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_dept_id" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $book->dept_id->RadioButtonListHtml(TRUE, "x_dept_id") ?>
		</div>
	</div>
	<div id="tp_x_dept_id" class="ewTemplate"><input type="radio" data-table="book" data-field="x_dept_id" data-value-separator="<?php echo $book->dept_id->DisplayValueSeparatorAttribute() ?>" name="x_dept_id" id="x_dept_id" value="{value}"<?php echo $book->dept_id->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x_dept_id" id="s_x_dept_id" value="<?php echo $book->dept_id->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el_book_dept_id">
<span<?php echo $book->dept_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->dept_id->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_dept_id" name="x_dept_id" id="x_dept_id" value="<?php echo ew_HtmlEncode($book->dept_id->FormValue) ?>">
<?php } ?>
<?php echo $book->dept_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$book_edit->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<?php if ($book->CurrentAction <> "F") { // Confirm page ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit" onclick="this.form.a_edit.value='F';"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $book_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } else { ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("ConfirmBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="submit" onclick="this.form.a_edit.value='X';"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } ?>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fbookedit.Init();
</script>
<?php
$book_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$book_edit->Page_Terminate();
?>
