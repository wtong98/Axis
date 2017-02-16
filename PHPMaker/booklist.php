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

$book_list = NULL; // Initialize page object first

class cbook_list extends cbook {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{8f9e4799-e991-49ac-a0be-7b3f0a30522b}";

	// Table name
	var $TableName = 'book';

	// Page object name
	var $PageObjName = 'book_list';

	// Grid form hidden field names
	var $FormName = 'fbooklist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "bookadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "bookdelete.php";
		$this->MultiUpdateUrl = "bookupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'book', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fbooklistsrch";

		// List actions
		$this->ListActions = new cListActions();
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
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

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
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
			header("Location: " . $url);
		}
		exit();
	}

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Check QueryString parameters
			if (@$_GET["a"] <> "") {
				$this->CurrentAction = $_GET["a"];

				// Clear inline mode
				if ($this->CurrentAction == "cancel")
					$this->ClearInlineMode();

				// Switch to inline edit mode
				if ($this->CurrentAction == "edit")
					$this->InlineEditMode();

				// Switch to inline add mode
				if ($this->CurrentAction == "add" || $this->CurrentAction == "copy")
					$this->InlineAddMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

					// Inline Update
					if (($this->CurrentAction == "update" || $this->CurrentAction == "overwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "edit")
						$this->InlineUpdate();

					// Insert Inline
					if ($this->CurrentAction == "insert" && @$_SESSION[EW_SESSION_INLINE_MODE] == "add")
						$this->InlineInsert();
				}
			}

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Process filter list
			$this->ProcessFilterList();

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	//  Exit inline mode
	function ClearInlineMode() {
		$this->setKey("book_id", ""); // Clear inline edit key
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Inline Edit mode
	function InlineEditMode() {
		global $Security, $Language;
		$bInlineEdit = TRUE;
		if (@$_GET["book_id"] <> "") {
			$this->book_id->setQueryStringValue($_GET["book_id"]);
		} else {
			$bInlineEdit = FALSE;
		}
		if ($bInlineEdit) {
			if ($this->LoadRow()) {
				$this->setKey("book_id", $this->book_id->CurrentValue); // Set up inline edit key
				$_SESSION[EW_SESSION_INLINE_MODE] = "edit"; // Enable inline edit
			}
		}
	}

	// Perform update to Inline Edit record
	function InlineUpdate() {
		global $Language, $objForm, $gsFormError;
		$objForm->Index = 1; 
		$this->LoadFormValues(); // Get form values

		// Validate form
		$bInlineUpdate = TRUE;
		if (!$this->ValidateForm()) {	
			$bInlineUpdate = FALSE; // Form error, reset action
			$this->setFailureMessage($gsFormError);
		} else {
			$bInlineUpdate = FALSE;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			if ($this->SetupKeyValues($rowkey)) { // Set up key values
				if ($this->CheckInlineEditKey()) { // Check key
					$this->SendEmail = TRUE; // Send email on update success
					$bInlineUpdate = $this->EditRow(); // Update record
				} else {
					$bInlineUpdate = FALSE;
				}
			}
		}
		if ($bInlineUpdate) { // Update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
			$this->EventCancelled = TRUE; // Cancel event
			$this->CurrentAction = "edit"; // Stay in edit mode
		}
	}

	// Check Inline Edit key
	function CheckInlineEditKey() {

		//CheckInlineEditKey = True
		if (strval($this->getKey("book_id")) <> strval($this->book_id->CurrentValue))
			return FALSE;
		return TRUE;
	}

	// Switch to Inline Add mode
	function InlineAddMode() {
		global $Security, $Language;
		if ($this->CurrentAction == "copy") {
			if (@$_GET["book_id"] <> "") {
				$this->book_id->setQueryStringValue($_GET["book_id"]);
				$this->setKey("book_id", $this->book_id->CurrentValue); // Set up key
			} else {
				$this->setKey("book_id", ""); // Clear key
				$this->CurrentAction = "add";
			}
		}
		$_SESSION[EW_SESSION_INLINE_MODE] = "add"; // Enable inline add
	}

	// Perform update to Inline Add/Copy record
	function InlineInsert() {
		global $Language, $objForm, $gsFormError;
		$this->LoadOldRecord(); // Load old recordset
		$objForm->Index = 0;
		$this->LoadFormValues(); // Get form values

		// Validate form
		if (!$this->ValidateForm()) {
			$this->setFailureMessage($gsFormError); // Set validation error message
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
			return;
		}
		$this->SendEmail = TRUE; // Send email on add success
		if ($this->AddRow($this->OldRecordset)) { // Add record
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up add success message
			$this->ClearInlineMode(); // Clear inline add mode
		} else { // Add failed
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->book_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->book_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = $UserProfile->GetSearchFilters(CurrentUserName(), "fbooklistsrch");
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->book_id->AdvancedSearch->ToJSON(), ","); // Field book_id
		$sFilterList = ew_Concat($sFilterList, $this->IMSA_id->AdvancedSearch->ToJSON(), ","); // Field IMSA_id
		$sFilterList = ew_Concat($sFilterList, $this->title->AdvancedSearch->ToJSON(), ","); // Field title
		$sFilterList = ew_Concat($sFilterList, $this->ISBN->AdvancedSearch->ToJSON(), ","); // Field ISBN
		$sFilterList = ew_Concat($sFilterList, $this->cost->AdvancedSearch->ToJSON(), ","); // Field cost
		$sFilterList = ew_Concat($sFilterList, $this->fee->AdvancedSearch->ToJSON(), ","); // Field fee
		$sFilterList = ew_Concat($sFilterList, $this->dept_id->AdvancedSearch->ToJSON(), ","); // Field dept_id
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}
		$sFilterList = preg_replace('/,$/', "", $sFilterList);

		// Return filter list in json
		if ($sFilterList <> "")
			$sFilterList = "\"data\":{" . $sFilterList . "}";
		if ($sSavedFilterList <> "") {
			if ($sFilterList <> "")
				$sFilterList .= ",";
			$sFilterList .= "\"filters\":" . $sSavedFilterList;
		}
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Process filter list
	function ProcessFilterList() {
		global $UserProfile;
		if (@$_POST["ajax"] == "savefilters") { // Save filter request (Ajax)
			$filters = ew_StripSlashes(@$_POST["filters"]);
			$UserProfile->SetSearchFilters(CurrentUserName(), "fbooklistsrch", $filters);

			// Clean output buffer
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			echo ew_ArrayToJson(array(array("success" => TRUE))); // Success
			$this->Page_Terminate();
			exit();
		} elseif (@$_POST["cmd"] == "resetfilter") {
			$this->RestoreFilterList();
		}
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ew_StripSlashes(@$_POST["filter"]), TRUE);
		$this->Command = "search";

		// Field book_id
		$this->book_id->AdvancedSearch->SearchValue = @$filter["x_book_id"];
		$this->book_id->AdvancedSearch->SearchOperator = @$filter["z_book_id"];
		$this->book_id->AdvancedSearch->SearchCondition = @$filter["v_book_id"];
		$this->book_id->AdvancedSearch->SearchValue2 = @$filter["y_book_id"];
		$this->book_id->AdvancedSearch->SearchOperator2 = @$filter["w_book_id"];
		$this->book_id->AdvancedSearch->Save();

		// Field IMSA_id
		$this->IMSA_id->AdvancedSearch->SearchValue = @$filter["x_IMSA_id"];
		$this->IMSA_id->AdvancedSearch->SearchOperator = @$filter["z_IMSA_id"];
		$this->IMSA_id->AdvancedSearch->SearchCondition = @$filter["v_IMSA_id"];
		$this->IMSA_id->AdvancedSearch->SearchValue2 = @$filter["y_IMSA_id"];
		$this->IMSA_id->AdvancedSearch->SearchOperator2 = @$filter["w_IMSA_id"];
		$this->IMSA_id->AdvancedSearch->Save();

		// Field title
		$this->title->AdvancedSearch->SearchValue = @$filter["x_title"];
		$this->title->AdvancedSearch->SearchOperator = @$filter["z_title"];
		$this->title->AdvancedSearch->SearchCondition = @$filter["v_title"];
		$this->title->AdvancedSearch->SearchValue2 = @$filter["y_title"];
		$this->title->AdvancedSearch->SearchOperator2 = @$filter["w_title"];
		$this->title->AdvancedSearch->Save();

		// Field ISBN
		$this->ISBN->AdvancedSearch->SearchValue = @$filter["x_ISBN"];
		$this->ISBN->AdvancedSearch->SearchOperator = @$filter["z_ISBN"];
		$this->ISBN->AdvancedSearch->SearchCondition = @$filter["v_ISBN"];
		$this->ISBN->AdvancedSearch->SearchValue2 = @$filter["y_ISBN"];
		$this->ISBN->AdvancedSearch->SearchOperator2 = @$filter["w_ISBN"];
		$this->ISBN->AdvancedSearch->Save();

		// Field cost
		$this->cost->AdvancedSearch->SearchValue = @$filter["x_cost"];
		$this->cost->AdvancedSearch->SearchOperator = @$filter["z_cost"];
		$this->cost->AdvancedSearch->SearchCondition = @$filter["v_cost"];
		$this->cost->AdvancedSearch->SearchValue2 = @$filter["y_cost"];
		$this->cost->AdvancedSearch->SearchOperator2 = @$filter["w_cost"];
		$this->cost->AdvancedSearch->Save();

		// Field fee
		$this->fee->AdvancedSearch->SearchValue = @$filter["x_fee"];
		$this->fee->AdvancedSearch->SearchOperator = @$filter["z_fee"];
		$this->fee->AdvancedSearch->SearchCondition = @$filter["v_fee"];
		$this->fee->AdvancedSearch->SearchValue2 = @$filter["y_fee"];
		$this->fee->AdvancedSearch->SearchOperator2 = @$filter["w_fee"];
		$this->fee->AdvancedSearch->Save();

		// Field dept_id
		$this->dept_id->AdvancedSearch->SearchValue = @$filter["x_dept_id"];
		$this->dept_id->AdvancedSearch->SearchOperator = @$filter["z_dept_id"];
		$this->dept_id->AdvancedSearch->SearchCondition = @$filter["v_dept_id"];
		$this->dept_id->AdvancedSearch->SearchValue2 = @$filter["y_dept_id"];
		$this->dept_id->AdvancedSearch->SearchOperator2 = @$filter["w_dept_id"];
		$this->dept_id->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->IMSA_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->title, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->ISBN, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->cost, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->fee, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSQL(&$Where, &$Fld, $arKeywords, $type) {
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if (EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace(EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));

				// Search keyword in any fields
				if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
					foreach ($ar as $sKeyword) {
						if ($sKeyword <> "") {
							if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
							$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
						}
					}
				} else {
					$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->book_id); // book_id
			$this->UpdateSort($this->IMSA_id); // IMSA_id
			$this->UpdateSort($this->title); // title
			$this->UpdateSort($this->ISBN); // ISBN
			$this->UpdateSort($this->cost); // cost
			$this->UpdateSort($this->fee); // fee
			$this->UpdateSort($this->dept_id); // dept_id
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->book_id->setSort("");
				$this->IMSA_id->setSort("");
				$this->title->setSort("");
				$this->ISBN->setSort("");
				$this->cost->setSort("");
				$this->fee->setSort("");
				$this->dept_id->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = FALSE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssStyle = "white-space: nowrap;";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Set up row action and key
		if (is_numeric($this->RowIndex) && $this->CurrentMode <> "view") {
			$objForm->Index = $this->RowIndex;
			$ActionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
			$OldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormOldKeyName);
			$KeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormKeyName);
			$BlankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
			if ($this->RowAction <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $ActionName . "\" id=\"" . $ActionName . "\" value=\"" . $this->RowAction . "\">";
			if ($this->RowAction == "delete") {
				$rowkey = $objForm->GetValue($this->FormKeyName);
				$this->SetupKeyValues($rowkey);
			}
			if ($this->RowAction == "insert" && $this->CurrentAction == "F" && $this->EmptyRow())
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $BlankRowName . "\" id=\"" . $BlankRowName . "\" value=\"1\">";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if (($this->CurrentAction == "add" || $this->CurrentAction == "copy") && $this->RowType == EW_ROWTYPE_ADD) { // Inline Add/Copy
			$this->ListOptions->CustomItem = "copy"; // Show copy column only
			$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
			$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
				"<a class=\"ewGridLink ewInlineInsert\" title=\"" . ew_HtmlTitle($Language->Phrase("InsertLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InsertLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("InsertLink") . "</a>&nbsp;" .
				"<a class=\"ewGridLink ewInlineCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("CancelLink") . "</a>" .
				"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"insert\"></div>";
			return;
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($this->CurrentAction == "edit" && $this->RowType == EW_ROWTYPE_EDIT) { // Inline-Edit
			$this->ListOptions->CustomItem = "edit"; // Show edit column only
			$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
				$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
					"<a class=\"ewGridLink ewInlineUpdate\" title=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . ew_GetHashUrl($this->PageName(), $this->PageObjName . "_row_" . $this->RowCnt) . "');\">" . $Language->Phrase("UpdateLink") . "</a>&nbsp;" .
					"<a class=\"ewGridLink ewInlineCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("CancelLink") . "</a>" .
					"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"update\"></div>";
			$oListOpt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . ew_HtmlEncode($this->book_id->CurrentValue) . "\">";
			return;
		}

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		$viewcaption = ew_HtmlTitle($Language->Phrase("ViewLink"));
		if (TRUE) {
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . $viewcaption . "\" data-caption=\"" . $viewcaption . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		$editcaption = ew_HtmlTitle($Language->Phrase("EditLink"));
		if (TRUE) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" href=\"" . ew_HtmlEncode(ew_GetHashUrl($this->InlineEditUrl, $this->PageObjName . "_row_" . $this->RowCnt)) . "\">" . $Language->Phrase("InlineEditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		$copycaption = ew_HtmlTitle($Language->Phrase("CopyLink"));
		if (TRUE) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineCopy\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineCopyLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->InlineCopyUrl) . "\">" . $Language->Phrase("InlineCopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if (TRUE)
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt && $this->Export == "" && $this->CurrentAction == "") {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->book_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$addcaption = ew_HtmlTitle($Language->Phrase("AddLink"));
		$item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "");

		// Inline Add
		$item = &$option->Add("inlineadd");
		$item->Body = "<a class=\"ewAddEdit ewInlineAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineAddLink")) . "\" href=\"" . ew_HtmlEncode($this->InlineAddUrl) . "\">" .$Language->Phrase("InlineAddLink") . "</a>";
		$item->Visible = ($this->InlineAddUrl <> "");
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fbooklistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fbooklistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fbooklist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			$this->CurrentAction = ""; // Clear action
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fbooklistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load default values
	function LoadDefaultValues() {
		$this->book_id->CurrentValue = NULL;
		$this->book_id->OldValue = $this->book_id->CurrentValue;
		$this->IMSA_id->CurrentValue = NULL;
		$this->IMSA_id->OldValue = $this->IMSA_id->CurrentValue;
		$this->title->CurrentValue = NULL;
		$this->title->OldValue = $this->title->CurrentValue;
		$this->ISBN->CurrentValue = NULL;
		$this->ISBN->OldValue = $this->ISBN->CurrentValue;
		$this->cost->CurrentValue = NULL;
		$this->cost->OldValue = $this->cost->CurrentValue;
		$this->fee->CurrentValue = NULL;
		$this->fee->OldValue = $this->fee->CurrentValue;
		$this->dept_id->CurrentValue = NULL;
		$this->dept_id->OldValue = $this->dept_id->CurrentValue;
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->book_id->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
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
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->book_id->CurrentValue = $this->book_id->FormValue;
		$this->IMSA_id->CurrentValue = $this->IMSA_id->FormValue;
		$this->title->CurrentValue = $this->title->FormValue;
		$this->ISBN->CurrentValue = $this->ISBN->FormValue;
		$this->cost->CurrentValue = $this->cost->FormValue;
		$this->fee->CurrentValue = $this->fee->FormValue;
		$this->dept_id->CurrentValue = $this->dept_id->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("book_id")) <> "")
			$this->book_id->CurrentValue = $this->getKey("book_id"); // book_id
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// book_id
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

			// Add refer script
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

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// IMSA_id
		$this->IMSA_id->SetDbValueDef($rsnew, $this->IMSA_id->CurrentValue, "", FALSE);

		// title
		$this->title->SetDbValueDef($rsnew, $this->title->CurrentValue, "", FALSE);

		// ISBN
		$this->ISBN->SetDbValueDef($rsnew, $this->ISBN->CurrentValue, "", FALSE);

		// cost
		$this->cost->SetDbValueDef($rsnew, $this->cost->CurrentValue, NULL, FALSE);

		// fee
		$this->fee->SetDbValueDef($rsnew, $this->fee->CurrentValue, NULL, FALSE);

		// dept_id
		$this->dept_id->SetDbValueDef($rsnew, $this->dept_id->CurrentValue, 0, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->book_id->setDbValue($conn->Insert_ID());
				$rsnew['book_id'] = $this->book_id->DbValue;
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
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

		//$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($book_list)) $book_list = new cbook_list();

// Page init
$book_list->Page_Init();

// Page main
$book_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$book_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fbooklist = new ew_Form("fbooklist", "list");
fbooklist.FormKeyCountName = '<?php echo $book_list->FormKeyCountName ?>';

// Validate form
fbooklist.Validate = function() {
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
	return true;
}

// Form_CustomValidate event
fbooklist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbooklist.ValidateRequired = true;
<?php } else { ?>
fbooklist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbooklist.Lists["x_dept_id"] = {"LinkField":"x_dept_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_name","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"department"};

// Form object for search
var CurrentSearchForm = fbooklistsrch = new ew_Form("fbooklistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php if ($book_list->TotalRecs > 0 && $book_list->ExportOptions->Visible()) { ?>
<?php $book_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($book_list->SearchOptions->Visible()) { ?>
<?php $book_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($book_list->FilterOptions->Visible()) { ?>
<?php $book_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php
	$bSelectLimit = $book_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($book_list->TotalRecs <= 0)
			$book_list->TotalRecs = $book->SelectRecordCount();
	} else {
		if (!$book_list->Recordset && ($book_list->Recordset = $book_list->LoadRecordset()))
			$book_list->TotalRecs = $book_list->Recordset->RecordCount();
	}
	$book_list->StartRec = 1;
	if ($book_list->DisplayRecs <= 0 || ($book->Export <> "" && $book->ExportAll)) // Display all records
		$book_list->DisplayRecs = $book_list->TotalRecs;
	if (!($book->Export <> "" && $book->ExportAll))
		$book_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$book_list->Recordset = $book_list->LoadRecordset($book_list->StartRec-1, $book_list->DisplayRecs);

	// Set no record found message
	if ($book->CurrentAction == "" && $book_list->TotalRecs == 0) {
		if ($book_list->SearchWhere == "0=101")
			$book_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$book_list->setWarningMessage($Language->Phrase("NoRecord"));
	}
$book_list->RenderOtherOptions();
?>
<?php if ($book->Export == "" && $book->CurrentAction == "") { ?>
<form name="fbooklistsrch" id="fbooklistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($book_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="fbooklistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="book">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($book_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($book_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $book_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($book_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($book_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($book_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($book_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php $book_list->ShowPageHeader(); ?>
<?php
$book_list->ShowMessage();
?>
<?php if ($book_list->TotalRecs > 0 || $book->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid book">
<form name="fbooklist" id="fbooklist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($book_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $book_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="book">
<div id="gmp_book" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($book_list->TotalRecs > 0 || $book->CurrentAction == "add" || $book->CurrentAction == "copy") { ?>
<table id="tbl_booklist" class="table ewTable">
<?php echo $book->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$book_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$book_list->RenderListOptions();

// Render list options (header, left)
$book_list->ListOptions->Render("header", "left");
?>
<?php if ($book->book_id->Visible) { // book_id ?>
	<?php if ($book->SortUrl($book->book_id) == "") { ?>
		<th data-name="book_id"><div id="elh_book_book_id" class="book_book_id"><div class="ewTableHeaderCaption"><?php echo $book->book_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="book_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->book_id) ?>',1);"><div id="elh_book_book_id" class="book_book_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->book_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($book->book_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->book_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->IMSA_id->Visible) { // IMSA_id ?>
	<?php if ($book->SortUrl($book->IMSA_id) == "") { ?>
		<th data-name="IMSA_id"><div id="elh_book_IMSA_id" class="book_IMSA_id"><div class="ewTableHeaderCaption"><?php echo $book->IMSA_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="IMSA_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->IMSA_id) ?>',1);"><div id="elh_book_IMSA_id" class="book_IMSA_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->IMSA_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($book->IMSA_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->IMSA_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->title->Visible) { // title ?>
	<?php if ($book->SortUrl($book->title) == "") { ?>
		<th data-name="title"><div id="elh_book_title" class="book_title"><div class="ewTableHeaderCaption"><?php echo $book->title->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="title"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->title) ?>',1);"><div id="elh_book_title" class="book_title">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->title->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($book->title->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->title->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->ISBN->Visible) { // ISBN ?>
	<?php if ($book->SortUrl($book->ISBN) == "") { ?>
		<th data-name="ISBN"><div id="elh_book_ISBN" class="book_ISBN"><div class="ewTableHeaderCaption"><?php echo $book->ISBN->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ISBN"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->ISBN) ?>',1);"><div id="elh_book_ISBN" class="book_ISBN">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->ISBN->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($book->ISBN->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->ISBN->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->cost->Visible) { // cost ?>
	<?php if ($book->SortUrl($book->cost) == "") { ?>
		<th data-name="cost"><div id="elh_book_cost" class="book_cost"><div class="ewTableHeaderCaption"><?php echo $book->cost->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="cost"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->cost) ?>',1);"><div id="elh_book_cost" class="book_cost">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->cost->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($book->cost->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->cost->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->fee->Visible) { // fee ?>
	<?php if ($book->SortUrl($book->fee) == "") { ?>
		<th data-name="fee"><div id="elh_book_fee" class="book_fee"><div class="ewTableHeaderCaption"><?php echo $book->fee->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="fee"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->fee) ?>',1);"><div id="elh_book_fee" class="book_fee">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->fee->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($book->fee->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->fee->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($book->dept_id->Visible) { // dept_id ?>
	<?php if ($book->SortUrl($book->dept_id) == "") { ?>
		<th data-name="dept_id"><div id="elh_book_dept_id" class="book_dept_id"><div class="ewTableHeaderCaption"><?php echo $book->dept_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="dept_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $book->SortUrl($book->dept_id) ?>',1);"><div id="elh_book_dept_id" class="book_dept_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $book->dept_id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($book->dept_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($book->dept_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$book_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
	if ($book->CurrentAction == "add" || $book->CurrentAction == "copy") {
		$book_list->RowIndex = 0;
		$book_list->KeyCount = $book_list->RowIndex;
		if ($book->CurrentAction == "copy" && !$book_list->LoadRow())
				$book->CurrentAction = "add";
		if ($book->CurrentAction == "add")
			$book_list->LoadDefaultValues();
		if ($book->EventCancelled) // Insert failed
			$book_list->RestoreFormValues(); // Restore form values

		// Set row properties
		$book->ResetAttrs();
		$book->RowAttrs = array_merge($book->RowAttrs, array('data-rowindex'=>0, 'id'=>'r0_book', 'data-rowtype'=>EW_ROWTYPE_ADD));
		$book->RowType = EW_ROWTYPE_ADD;

		// Render row
		$book_list->RenderRow();

		// Render list options
		$book_list->RenderListOptions();
		$book_list->StartRowCnt = 0;
?>
	<tr<?php echo $book->RowAttributes() ?>>
<?php

// Render list options (body, left)
$book_list->ListOptions->Render("body", "left", $book_list->RowCnt);
?>
	<?php if ($book->book_id->Visible) { // book_id ?>
		<td data-name="book_id">
<input type="hidden" data-table="book" data-field="x_book_id" name="o<?php echo $book_list->RowIndex ?>_book_id" id="o<?php echo $book_list->RowIndex ?>_book_id" value="<?php echo ew_HtmlEncode($book->book_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->IMSA_id->Visible) { // IMSA_id ?>
		<td data-name="IMSA_id">
<span id="el<?php echo $book_list->RowCnt ?>_book_IMSA_id" class="form-group book_IMSA_id">
<input type="text" data-table="book" data-field="x_IMSA_id" name="x<?php echo $book_list->RowIndex ?>_IMSA_id" id="x<?php echo $book_list->RowIndex ?>_IMSA_id" size="30" maxlength="15" placeholder="<?php echo ew_HtmlEncode($book->IMSA_id->getPlaceHolder()) ?>" value="<?php echo $book->IMSA_id->EditValue ?>"<?php echo $book->IMSA_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="book" data-field="x_IMSA_id" name="o<?php echo $book_list->RowIndex ?>_IMSA_id" id="o<?php echo $book_list->RowIndex ?>_IMSA_id" value="<?php echo ew_HtmlEncode($book->IMSA_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->title->Visible) { // title ?>
		<td data-name="title">
<span id="el<?php echo $book_list->RowCnt ?>_book_title" class="form-group book_title">
<input type="text" data-table="book" data-field="x_title" name="x<?php echo $book_list->RowIndex ?>_title" id="x<?php echo $book_list->RowIndex ?>_title" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($book->title->getPlaceHolder()) ?>" value="<?php echo $book->title->EditValue ?>"<?php echo $book->title->EditAttributes() ?>>
</span>
<input type="hidden" data-table="book" data-field="x_title" name="o<?php echo $book_list->RowIndex ?>_title" id="o<?php echo $book_list->RowIndex ?>_title" value="<?php echo ew_HtmlEncode($book->title->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->ISBN->Visible) { // ISBN ?>
		<td data-name="ISBN">
<span id="el<?php echo $book_list->RowCnt ?>_book_ISBN" class="form-group book_ISBN">
<input type="text" data-table="book" data-field="x_ISBN" name="x<?php echo $book_list->RowIndex ?>_ISBN" id="x<?php echo $book_list->RowIndex ?>_ISBN" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($book->ISBN->getPlaceHolder()) ?>" value="<?php echo $book->ISBN->EditValue ?>"<?php echo $book->ISBN->EditAttributes() ?>>
</span>
<input type="hidden" data-table="book" data-field="x_ISBN" name="o<?php echo $book_list->RowIndex ?>_ISBN" id="o<?php echo $book_list->RowIndex ?>_ISBN" value="<?php echo ew_HtmlEncode($book->ISBN->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->cost->Visible) { // cost ?>
		<td data-name="cost">
<span id="el<?php echo $book_list->RowCnt ?>_book_cost" class="form-group book_cost">
<input type="text" data-table="book" data-field="x_cost" name="x<?php echo $book_list->RowIndex ?>_cost" id="x<?php echo $book_list->RowIndex ?>_cost" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->cost->getPlaceHolder()) ?>" value="<?php echo $book->cost->EditValue ?>"<?php echo $book->cost->EditAttributes() ?>>
</span>
<input type="hidden" data-table="book" data-field="x_cost" name="o<?php echo $book_list->RowIndex ?>_cost" id="o<?php echo $book_list->RowIndex ?>_cost" value="<?php echo ew_HtmlEncode($book->cost->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->fee->Visible) { // fee ?>
		<td data-name="fee">
<span id="el<?php echo $book_list->RowCnt ?>_book_fee" class="form-group book_fee">
<input type="text" data-table="book" data-field="x_fee" name="x<?php echo $book_list->RowIndex ?>_fee" id="x<?php echo $book_list->RowIndex ?>_fee" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->fee->getPlaceHolder()) ?>" value="<?php echo $book->fee->EditValue ?>"<?php echo $book->fee->EditAttributes() ?>>
</span>
<input type="hidden" data-table="book" data-field="x_fee" name="o<?php echo $book_list->RowIndex ?>_fee" id="o<?php echo $book_list->RowIndex ?>_fee" value="<?php echo ew_HtmlEncode($book->fee->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($book->dept_id->Visible) { // dept_id ?>
		<td data-name="dept_id">
<span id="el<?php echo $book_list->RowCnt ?>_book_dept_id" class="form-group book_dept_id">
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $book->dept_id->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x<?php echo $book_list->RowIndex ?>_dept_id" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $book->dept_id->RadioButtonListHtml(TRUE, "x{$book_list->RowIndex}_dept_id") ?>
		</div>
	</div>
	<div id="tp_x<?php echo $book_list->RowIndex ?>_dept_id" class="ewTemplate"><input type="radio" data-table="book" data-field="x_dept_id" data-value-separator="<?php echo $book->dept_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $book_list->RowIndex ?>_dept_id" id="x<?php echo $book_list->RowIndex ?>_dept_id" value="{value}"<?php echo $book->dept_id->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x<?php echo $book_list->RowIndex ?>_dept_id" id="s_x<?php echo $book_list->RowIndex ?>_dept_id" value="<?php echo $book->dept_id->LookupFilterQuery() ?>">
</span>
<input type="hidden" data-table="book" data-field="x_dept_id" name="o<?php echo $book_list->RowIndex ?>_dept_id" id="o<?php echo $book_list->RowIndex ?>_dept_id" value="<?php echo ew_HtmlEncode($book->dept_id->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$book_list->ListOptions->Render("body", "right", $book_list->RowCnt);
?>
<script type="text/javascript">
fbooklist.UpdateOpts(<?php echo $book_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
<?php
if ($book->ExportAll && $book->Export <> "") {
	$book_list->StopRec = $book_list->TotalRecs;
} else {

	// Set the last record to display
	if ($book_list->TotalRecs > $book_list->StartRec + $book_list->DisplayRecs - 1)
		$book_list->StopRec = $book_list->StartRec + $book_list->DisplayRecs - 1;
	else
		$book_list->StopRec = $book_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($book_list->FormKeyCountName) && ($book->CurrentAction == "gridadd" || $book->CurrentAction == "gridedit" || $book->CurrentAction == "F")) {
		$book_list->KeyCount = $objForm->GetValue($book_list->FormKeyCountName);
		$book_list->StopRec = $book_list->StartRec + $book_list->KeyCount - 1;
	}
}
$book_list->RecCnt = $book_list->StartRec - 1;
if ($book_list->Recordset && !$book_list->Recordset->EOF) {
	$book_list->Recordset->MoveFirst();
	$bSelectLimit = $book_list->UseSelectLimit;
	if (!$bSelectLimit && $book_list->StartRec > 1)
		$book_list->Recordset->Move($book_list->StartRec - 1);
} elseif (!$book->AllowAddDeleteRow && $book_list->StopRec == 0) {
	$book_list->StopRec = $book->GridAddRowCount;
}

// Initialize aggregate
$book->RowType = EW_ROWTYPE_AGGREGATEINIT;
$book->ResetAttrs();
$book_list->RenderRow();
$book_list->EditRowCnt = 0;
if ($book->CurrentAction == "edit")
	$book_list->RowIndex = 1;
while ($book_list->RecCnt < $book_list->StopRec) {
	$book_list->RecCnt++;
	if (intval($book_list->RecCnt) >= intval($book_list->StartRec)) {
		$book_list->RowCnt++;

		// Set up key count
		$book_list->KeyCount = $book_list->RowIndex;

		// Init row class and style
		$book->ResetAttrs();
		$book->CssClass = "";
		if ($book->CurrentAction == "gridadd") {
			$book_list->LoadDefaultValues(); // Load default values
		} else {
			$book_list->LoadRowValues($book_list->Recordset); // Load row values
		}
		$book->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($book->CurrentAction == "edit") {
			if ($book_list->CheckInlineEditKey() && $book_list->EditRowCnt == 0) { // Inline edit
				$book->RowType = EW_ROWTYPE_EDIT; // Render edit
			}
		}
		if ($book->CurrentAction == "edit" && $book->RowType == EW_ROWTYPE_EDIT && $book->EventCancelled) { // Update failed
			$objForm->Index = 1;
			$book_list->RestoreFormValues(); // Restore form values
		}
		if ($book->RowType == EW_ROWTYPE_EDIT) // Edit row
			$book_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$book->RowAttrs = array_merge($book->RowAttrs, array('data-rowindex'=>$book_list->RowCnt, 'id'=>'r' . $book_list->RowCnt . '_book', 'data-rowtype'=>$book->RowType));

		// Render row
		$book_list->RenderRow();

		// Render list options
		$book_list->RenderListOptions();
?>
	<tr<?php echo $book->RowAttributes() ?>>
<?php

// Render list options (body, left)
$book_list->ListOptions->Render("body", "left", $book_list->RowCnt);
?>
	<?php if ($book->book_id->Visible) { // book_id ?>
		<td data-name="book_id"<?php echo $book->book_id->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_book_id" class="form-group book_book_id">
<span<?php echo $book->book_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $book->book_id->EditValue ?></p></span>
</span>
<input type="hidden" data-table="book" data-field="x_book_id" name="x<?php echo $book_list->RowIndex ?>_book_id" id="x<?php echo $book_list->RowIndex ?>_book_id" value="<?php echo ew_HtmlEncode($book->book_id->CurrentValue) ?>">
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_book_id" class="book_book_id">
<span<?php echo $book->book_id->ViewAttributes() ?>>
<?php echo $book->book_id->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $book_list->PageObjName . "_row_" . $book_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($book->IMSA_id->Visible) { // IMSA_id ?>
		<td data-name="IMSA_id"<?php echo $book->IMSA_id->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_IMSA_id" class="form-group book_IMSA_id">
<input type="text" data-table="book" data-field="x_IMSA_id" name="x<?php echo $book_list->RowIndex ?>_IMSA_id" id="x<?php echo $book_list->RowIndex ?>_IMSA_id" size="30" maxlength="15" placeholder="<?php echo ew_HtmlEncode($book->IMSA_id->getPlaceHolder()) ?>" value="<?php echo $book->IMSA_id->EditValue ?>"<?php echo $book->IMSA_id->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_IMSA_id" class="book_IMSA_id">
<span<?php echo $book->IMSA_id->ViewAttributes() ?>>
<?php echo $book->IMSA_id->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($book->title->Visible) { // title ?>
		<td data-name="title"<?php echo $book->title->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_title" class="form-group book_title">
<input type="text" data-table="book" data-field="x_title" name="x<?php echo $book_list->RowIndex ?>_title" id="x<?php echo $book_list->RowIndex ?>_title" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($book->title->getPlaceHolder()) ?>" value="<?php echo $book->title->EditValue ?>"<?php echo $book->title->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_title" class="book_title">
<span<?php echo $book->title->ViewAttributes() ?>>
<?php echo $book->title->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($book->ISBN->Visible) { // ISBN ?>
		<td data-name="ISBN"<?php echo $book->ISBN->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_ISBN" class="form-group book_ISBN">
<input type="text" data-table="book" data-field="x_ISBN" name="x<?php echo $book_list->RowIndex ?>_ISBN" id="x<?php echo $book_list->RowIndex ?>_ISBN" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($book->ISBN->getPlaceHolder()) ?>" value="<?php echo $book->ISBN->EditValue ?>"<?php echo $book->ISBN->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_ISBN" class="book_ISBN">
<span<?php echo $book->ISBN->ViewAttributes() ?>>
<?php echo $book->ISBN->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($book->cost->Visible) { // cost ?>
		<td data-name="cost"<?php echo $book->cost->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_cost" class="form-group book_cost">
<input type="text" data-table="book" data-field="x_cost" name="x<?php echo $book_list->RowIndex ?>_cost" id="x<?php echo $book_list->RowIndex ?>_cost" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->cost->getPlaceHolder()) ?>" value="<?php echo $book->cost->EditValue ?>"<?php echo $book->cost->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_cost" class="book_cost">
<span<?php echo $book->cost->ViewAttributes() ?>>
<?php echo $book->cost->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($book->fee->Visible) { // fee ?>
		<td data-name="fee"<?php echo $book->fee->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_fee" class="form-group book_fee">
<input type="text" data-table="book" data-field="x_fee" name="x<?php echo $book_list->RowIndex ?>_fee" id="x<?php echo $book_list->RowIndex ?>_fee" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($book->fee->getPlaceHolder()) ?>" value="<?php echo $book->fee->EditValue ?>"<?php echo $book->fee->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_fee" class="book_fee">
<span<?php echo $book->fee->ViewAttributes() ?>>
<?php echo $book->fee->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($book->dept_id->Visible) { // dept_id ?>
		<td data-name="dept_id"<?php echo $book->dept_id->CellAttributes() ?>>
<?php if ($book->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_dept_id" class="form-group book_dept_id">
<div class="ewDropdownList has-feedback">
	<span onclick="" class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $book->dept_id->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x<?php echo $book_list->RowIndex ?>_dept_id" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php echo $book->dept_id->RadioButtonListHtml(TRUE, "x{$book_list->RowIndex}_dept_id") ?>
		</div>
	</div>
	<div id="tp_x<?php echo $book_list->RowIndex ?>_dept_id" class="ewTemplate"><input type="radio" data-table="book" data-field="x_dept_id" data-value-separator="<?php echo $book->dept_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $book_list->RowIndex ?>_dept_id" id="x<?php echo $book_list->RowIndex ?>_dept_id" value="{value}"<?php echo $book->dept_id->EditAttributes() ?>></div>
</div>
<input type="hidden" name="s_x<?php echo $book_list->RowIndex ?>_dept_id" id="s_x<?php echo $book_list->RowIndex ?>_dept_id" value="<?php echo $book->dept_id->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php if ($book->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $book_list->RowCnt ?>_book_dept_id" class="book_dept_id">
<span<?php echo $book->dept_id->ViewAttributes() ?>>
<?php echo $book->dept_id->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$book_list->ListOptions->Render("body", "right", $book_list->RowCnt);
?>
	</tr>
<?php if ($book->RowType == EW_ROWTYPE_ADD || $book->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fbooklist.UpdateOpts(<?php echo $book_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	if ($book->CurrentAction <> "gridadd")
		$book_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($book->CurrentAction == "add" || $book->CurrentAction == "copy") { ?>
<input type="hidden" name="<?php echo $book_list->FormKeyCountName ?>" id="<?php echo $book_list->FormKeyCountName ?>" value="<?php echo $book_list->KeyCount ?>">
<?php } ?>
<?php if ($book->CurrentAction == "edit") { ?>
<input type="hidden" name="<?php echo $book_list->FormKeyCountName ?>" id="<?php echo $book_list->FormKeyCountName ?>" value="<?php echo $book_list->KeyCount ?>">
<?php } ?>
<?php if ($book->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($book_list->Recordset)
	$book_list->Recordset->Close();
?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($book->CurrentAction <> "gridadd" && $book->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($book_list->Pager)) $book_list->Pager = new cPrevNextPager($book_list->StartRec, $book_list->DisplayRecs, $book_list->TotalRecs) ?>
<?php if ($book_list->Pager->RecordCount > 0 && $book_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($book_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $book_list->PageUrl() ?>start=<?php echo $book_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($book_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $book_list->PageUrl() ?>start=<?php echo $book_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $book_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($book_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $book_list->PageUrl() ?>start=<?php echo $book_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($book_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $book_list->PageUrl() ?>start=<?php echo $book_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $book_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $book_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $book_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $book_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($book_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
</div>
<?php } ?>
<?php if ($book_list->TotalRecs == 0 && $book->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($book_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<script type="text/javascript">
fbooklistsrch.FilterList = <?php echo $book_list->GetFilterList() ?>;
fbooklistsrch.Init();
fbooklist.Init();
</script>
<?php
$book_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$book_list->Page_Terminate();
?>
