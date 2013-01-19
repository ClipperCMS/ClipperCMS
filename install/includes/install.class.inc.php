<?php
class Install {

	public $queryTime = 0, $executedQueries = 0, $dumpSQL = false, $queryCode = '';
	public $installFailed, $mysqlErrors, $prefix;
	public $sitename, $adminname, $adminemail, $adminpass, $managerlanguage;
	public $mode, $fileManagerPath, $imgPath, $imgUrl;
	public $errContinue = false;
	public $autoTemplateLogic;

    /**
     * Returns the current micro time
     *
     * @return float
     */
    function getMicroTime() {
        list ($usec, $sec)= explode(' ', microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * Exits with error message
     * 
     * @param string $msg Default: unspecified error
     * @param string $query Default: Empty string
     * @param boolean $is_error Default: true
     * @param string $nr Default: Empty string
     * @param string $file Default: Empty string
     * @param string $source Default: Empty string
     * @param string $text Default: Empty string
     * @param string $line Default: Empty string
     * @return void
     */
    function messageQuit($msg= 'unspecified error', $query= '', $is_error= true, $nr= '', $file= '', $source= '', $text= '', $line= '') {
        if (! $this->errContinue) {
    	    exit("\n\n$msg\n\n$query");
        }
	}

	/**
	* Parser function to set up tables and data from *.sql setup files
    * transferred from sqlParser class
	*/
	function process($filename) {

	// check to make sure file exists
	if (! file_exists($filename)) {
		$this->mysqlErrors[] = array("error" => "File '$filename' not found");
		$this->installFailed = true ;
		return false;
	}

	$fh = fopen($filename, 'r');
	$idata = '';

	while (!feof($fh)) {
		$idata .= fread($fh, 1024);
	}

	fclose($fh);
	$idata = str_replace("\r", '', $idata);

	// check if in upgrade mode
	if ($this->mode=="upd") {
		// remove non-upgradeable parts
		$s = strpos($idata,"non-upgrade-able[[");
		$e = strpos($idata,"]]non-upgrade-able")+17;
		if($s && $e) $idata = str_replace(substr($idata,$s,$e-$s)," Removed non upgradeable items",$idata);
	}

	// replace {} tags
	$idata = str_replace('{PREFIX}', $this->prefix, $idata);
	$idata = str_replace('{ADMIN}', $this->adminname, $idata);
	$idata = str_replace('{ADMINEMAIL}', $this->adminemail, $idata);
	$idata = str_replace('{ADMINPASS}', $this->adminpass, $idata);
	$idata = str_replace('{IMAGEPATH}', $this->imagePath, $idata);
	$idata = str_replace('{IMAGEURL}', $this->imageUrl, $idata);
	$idata = str_replace('{FILEMANAGERPATH}', $this->fileManagerPath, $idata);
	$idata = str_replace('{MANAGERLANGUAGE}', $this->managerlanguage, $idata);
	$idata = str_replace('{AUTOTEMPLATELOGIC}', $this->autoTemplateLogic, $idata);

	$sql_array = explode("\n\n", $idata);

	$num = 0;

// Don't exit on failed query
    $this->errContinue = true;

	foreach($sql_array as $sql_entry) {
		$sql_do = trim($sql_entry, "\r\n; ");

		if (preg_match('/^\#/', $sql_do)) continue;

		$num = $num + 1;

		if ($sql_do) {
			$this->db->query($sql_do);
		}

		if ($this->db->getLastError()) {
			// Ignore duplicate and drop errors - Raymond
			if ($this->ignoreDuplicateErrors){
				$errno = $this->db->getLastError('', 'num');
				if ($errno == 1060 || $errno == 1061 || $errno == 1091) continue;
			}

			$this->mysqlErrors[] = array("error" => $this->db->getLastError(), "sql" => $sql_do);
			$this->installFailed = true;
		}
	}

    $this->errContinue = false;
    }

	/**
	* Select or create category for installation TPL file
    * transferred from instprocessor.php
	*/
function getCreateDbCategory($category) {
    $table_prefix = $this->prefix;
    $category_id = 0;

    if(!empty($category)) {
        $category = $this->db->escape($category);
        $rs = $this->db->select('id', "`{$table_prefix}categories`", "category='$category' ");
        
        $row = $this->db->getValue($rs);

        if (! empty($row)) {
            $category_id = $row[0];
        } else {
            $category_id = $this->db->insert(array("`category`"=>"$category"), "`{$table_prefix}categories`");
        }
    }
    return $category_id;
}

}
