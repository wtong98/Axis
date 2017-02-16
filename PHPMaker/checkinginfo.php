<?php

// Global variable for table object
$checking = NULL;

//
// Table class for checking
//
class cchecking extends cTable {
	var $book_id;
	var $book_title;
	var $student_id;
	var $date_checkedout;
	var $date_checkedin;
	var $year_checkedout;
	var $semester_checkedout;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'checking';
		$this->TableName = 'checking';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`checking`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = TRUE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = TRUE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// book_id
		$this->book_id = new cField('checking', 'checking', 'x_book_id', 'book_id', '`book_id`', '`book_id`', 3, -1, FALSE, '`book_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->book_id->Sortable = TRUE; // Allow sort
		$this->book_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['book_id'] = &$this->book_id;

		// book_title
		$this->book_title = new cField('checking', 'checking', 'x_book_title', 'book_title', '`book_title`', '`book_title`', 200, -1, FALSE, '`book_title`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->book_title->Sortable = TRUE; // Allow sort
		$this->fields['book_title'] = &$this->book_title;

		// student_id
		$this->student_id = new cField('checking', 'checking', 'x_student_id', 'student_id', '`student_id`', '`student_id`', 200, -1, FALSE, '`student_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->student_id->Sortable = TRUE; // Allow sort
		$this->fields['student_id'] = &$this->student_id;

		// date_checkedout
		$this->date_checkedout = new cField('checking', 'checking', 'x_date_checkedout', 'date_checkedout', '`date_checkedout`', ew_CastDateFieldForLike('`date_checkedout`', 0, "DB"), 135, 0, FALSE, '`date_checkedout`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->date_checkedout->Sortable = TRUE; // Allow sort
		$this->date_checkedout->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['date_checkedout'] = &$this->date_checkedout;

		// date_checkedin
		$this->date_checkedin = new cField('checking', 'checking', 'x_date_checkedin', 'date_checkedin', '`date_checkedin`', ew_CastDateFieldForLike('`date_checkedin`', 0, "DB"), 135, 0, FALSE, '`date_checkedin`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->date_checkedin->Sortable = TRUE; // Allow sort
		$this->date_checkedin->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['date_checkedin'] = &$this->date_checkedin;

		// year_checkedout
		$this->year_checkedout = new cField('checking', 'checking', 'x_year_checkedout', 'year_checkedout', '`year_checkedout`', '`year_checkedout`', 3, -1, FALSE, '`year_checkedout`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->year_checkedout->Sortable = TRUE; // Allow sort
		$this->year_checkedout->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['year_checkedout'] = &$this->year_checkedout;

		// semester_checkedout
		$this->semester_checkedout = new cField('checking', 'checking', 'x_semester_checkedout', 'semester_checkedout', '`semester_checkedout`', '`semester_checkedout`', 200, -1, FALSE, '`semester_checkedout`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->semester_checkedout->Sortable = TRUE; // Allow sort
		$this->fields['semester_checkedout'] = &$this->semester_checkedout;
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`checking`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		$cnt = -1;
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match("/^SELECT \* FROM/i", $sSql)) {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
			if (array_key_exists('book_id', $rs))
				ew_AddFilter($where, ew_QuotedName('book_id', $this->DBID) . '=' . ew_QuotedValue($rs['book_id'], $this->book_id->FldDataType, $this->DBID));
			if (array_key_exists('student_id', $rs))
				ew_AddFilter($where, ew_QuotedName('student_id', $this->DBID) . '=' . ew_QuotedValue($rs['student_id'], $this->student_id->FldDataType, $this->DBID));
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`book_id` = @book_id@ AND `student_id` = '@student_id@'";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->book_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@book_id@", ew_AdjustSql($this->book_id->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		$sKeyFilter = str_replace("@student_id@", ew_AdjustSql($this->student_id->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "checkinglist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "checkinglist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("checkingview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("checkingview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "checkingadd.php?" . $this->UrlParm($parm);
		else
			$url = "checkingadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("checkingedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("checkingadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("checkingdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "book_id:" . ew_VarToJson($this->book_id->CurrentValue, "number", "'");
		$json .= ",student_id:" . ew_VarToJson($this->student_id->CurrentValue, "string", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->book_id->CurrentValue)) {
			$sUrl .= "book_id=" . urlencode($this->book_id->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		if (!is_null($this->student_id->CurrentValue)) {
			$sUrl .= "&student_id=" . urlencode($this->student_id->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return $this->AddMasterUrl(ew_CurrentPage() . "?" . $sUrlParm);
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
			for ($i = 0; $i < $cnt; $i++)
				$arKeys[$i] = explode($EW_COMPOSITE_KEY_SEPARATOR, $arKeys[$i]);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
			for ($i = 0; $i < $cnt; $i++)
				$arKeys[$i] = explode($EW_COMPOSITE_KEY_SEPARATOR, $arKeys[$i]);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsHttpPost();
			if ($isPost && isset($_POST["book_id"]))
				$arKey[] = ew_StripSlashes($_POST["book_id"]);
			elseif (isset($_GET["book_id"]))
				$arKey[] = ew_StripSlashes($_GET["book_id"]);
			else
				$arKeys = NULL; // Do not setup
			if ($isPost && isset($_POST["student_id"]))
				$arKey[] = ew_StripSlashes($_POST["student_id"]);
			elseif (isset($_GET["student_id"]))
				$arKey[] = ew_StripSlashes($_GET["student_id"]);
			else
				$arKeys = NULL; // Do not setup
			if (is_array($arKeys)) $arKeys[] = $arKey;

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				if (!is_array($key) || count($key) <> 2)
					continue; // Just skip so other keys will still work
				if (!is_numeric($key[0])) // book_id
					continue;
				$ar[] = $key;
			}
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->book_id->CurrentValue = $key[0];
			$this->student_id->CurrentValue = $key[1];
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->book_id->setDbValue($rs->fields('book_id'));
		$this->book_title->setDbValue($rs->fields('book_title'));
		$this->student_id->setDbValue($rs->fields('student_id'));
		$this->date_checkedout->setDbValue($rs->fields('date_checkedout'));
		$this->date_checkedin->setDbValue($rs->fields('date_checkedin'));
		$this->year_checkedout->setDbValue($rs->fields('year_checkedout'));
		$this->semester_checkedout->setDbValue($rs->fields('semester_checkedout'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// book_id
		// book_title
		// student_id
		// date_checkedout
		// date_checkedin
		// year_checkedout
		// semester_checkedout
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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// book_id
		$this->book_id->EditAttrs["class"] = "form-control";
		$this->book_id->EditCustomAttributes = "";
		$this->book_id->EditValue = $this->book_id->CurrentValue;
		$this->book_id->ViewCustomAttributes = "";

		// book_title
		$this->book_title->EditAttrs["class"] = "form-control";
		$this->book_title->EditCustomAttributes = "";
		$this->book_title->EditValue = $this->book_title->CurrentValue;
		$this->book_title->PlaceHolder = ew_RemoveHtml($this->book_title->FldCaption());

		// student_id
		$this->student_id->EditAttrs["class"] = "form-control";
		$this->student_id->EditCustomAttributes = "";
		$this->student_id->EditValue = $this->student_id->CurrentValue;
		$this->student_id->ViewCustomAttributes = "";

		// date_checkedout
		$this->date_checkedout->EditAttrs["class"] = "form-control";
		$this->date_checkedout->EditCustomAttributes = "";
		$this->date_checkedout->EditValue = ew_FormatDateTime($this->date_checkedout->CurrentValue, 8);
		$this->date_checkedout->PlaceHolder = ew_RemoveHtml($this->date_checkedout->FldCaption());

		// date_checkedin
		$this->date_checkedin->EditAttrs["class"] = "form-control";
		$this->date_checkedin->EditCustomAttributes = "";
		$this->date_checkedin->EditValue = ew_FormatDateTime($this->date_checkedin->CurrentValue, 8);
		$this->date_checkedin->PlaceHolder = ew_RemoveHtml($this->date_checkedin->FldCaption());

		// year_checkedout
		$this->year_checkedout->EditAttrs["class"] = "form-control";
		$this->year_checkedout->EditCustomAttributes = "";
		$this->year_checkedout->EditValue = $this->year_checkedout->CurrentValue;
		$this->year_checkedout->PlaceHolder = ew_RemoveHtml($this->year_checkedout->FldCaption());

		// semester_checkedout
		$this->semester_checkedout->EditAttrs["class"] = "form-control";
		$this->semester_checkedout->EditCustomAttributes = "";
		$this->semester_checkedout->EditValue = $this->semester_checkedout->CurrentValue;
		$this->semester_checkedout->PlaceHolder = ew_RemoveHtml($this->semester_checkedout->FldCaption());

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->book_id->Exportable) $Doc->ExportCaption($this->book_id);
					if ($this->book_title->Exportable) $Doc->ExportCaption($this->book_title);
					if ($this->student_id->Exportable) $Doc->ExportCaption($this->student_id);
					if ($this->date_checkedout->Exportable) $Doc->ExportCaption($this->date_checkedout);
					if ($this->date_checkedin->Exportable) $Doc->ExportCaption($this->date_checkedin);
					if ($this->year_checkedout->Exportable) $Doc->ExportCaption($this->year_checkedout);
					if ($this->semester_checkedout->Exportable) $Doc->ExportCaption($this->semester_checkedout);
				} else {
					if ($this->book_id->Exportable) $Doc->ExportCaption($this->book_id);
					if ($this->book_title->Exportable) $Doc->ExportCaption($this->book_title);
					if ($this->student_id->Exportable) $Doc->ExportCaption($this->student_id);
					if ($this->date_checkedout->Exportable) $Doc->ExportCaption($this->date_checkedout);
					if ($this->date_checkedin->Exportable) $Doc->ExportCaption($this->date_checkedin);
					if ($this->year_checkedout->Exportable) $Doc->ExportCaption($this->year_checkedout);
					if ($this->semester_checkedout->Exportable) $Doc->ExportCaption($this->semester_checkedout);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->book_id->Exportable) $Doc->ExportField($this->book_id);
						if ($this->book_title->Exportable) $Doc->ExportField($this->book_title);
						if ($this->student_id->Exportable) $Doc->ExportField($this->student_id);
						if ($this->date_checkedout->Exportable) $Doc->ExportField($this->date_checkedout);
						if ($this->date_checkedin->Exportable) $Doc->ExportField($this->date_checkedin);
						if ($this->year_checkedout->Exportable) $Doc->ExportField($this->year_checkedout);
						if ($this->semester_checkedout->Exportable) $Doc->ExportField($this->semester_checkedout);
					} else {
						if ($this->book_id->Exportable) $Doc->ExportField($this->book_id);
						if ($this->book_title->Exportable) $Doc->ExportField($this->book_title);
						if ($this->student_id->Exportable) $Doc->ExportField($this->student_id);
						if ($this->date_checkedout->Exportable) $Doc->ExportField($this->date_checkedout);
						if ($this->date_checkedin->Exportable) $Doc->ExportField($this->date_checkedin);
						if ($this->year_checkedout->Exportable) $Doc->ExportField($this->year_checkedout);
						if ($this->semester_checkedout->Exportable) $Doc->ExportField($this->semester_checkedout);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
