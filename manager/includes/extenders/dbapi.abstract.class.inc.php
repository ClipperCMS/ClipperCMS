<?php
abstract class DBAPI_abstract {

	// -----
	// SETUP 
	// -----

     /**
      * The constructor. Can optionally have the database details passed to it, or alternatively it can use the globals (using the globals is deprecated functionality).
      *
      * <<<< TODO throw E_USER_DEPRECATED after ensuring that Install and DocumentParser do not use the globals.
      *
      * @param $parent Parent object e.g. $modx or $install
      * @param $host db hostname
      * @param $dbase db schema name
      * @param $uid db username
      * @param $pwd db password
      * @param $pre table prefix
      * @param $charset client character set
      * @param $connection SQL to set connection character set 
      */
     final public function __construct($parent = null, $host = '', $dbase = '', $uid = '',$pwd = '', $pre = null, $charset = '', $connection_method = 'SET CHARACTER SET') {

          // DEPRECATED functionality - pass $modx, $install or such as $core in all new and updated Extras. All core code should do this as of 1.2
          global $modx;
          if (is_null($parent)) $parent = $modx;

          $this->parent = $parent;
          $this->config['host'] = $host ? $host : $GLOBALS['database_server'];
          $this->config['dbase'] = $dbase ? $dbase : $GLOBALS['dbase'];
          $this->config['user'] = $uid ? $uid : $GLOBALS['database_user'];
          $this->config['pass'] = $pwd ? $pwd : $GLOBALS['database_password'];
          $this->config['charset'] = $charset ? $charset : $GLOBALS['database_connection_charset'];
          $this->config['connection_method'] =  $this->_dbconnectionmethod = (isset($GLOBALS['database_connection_method']) ? $GLOBALS['database_connection_method'] : $connection_method);
          $this->config['table_prefix'] = ($pre !== NULL) ? $pre : $GLOBALS['table_prefix']; // Not currently used by the DBAPI. Used just to store the value.
          $this->initDataTypes();
     }

     /**
      * Called in the constructor to set up arrays containing the types
      * of database fields that can be used with specific PHP types.
      * 
      * RDBMS specific.
      */
     abstract protected function initDataTypes();

	// -------
	// CONNECT
	// -------

     /**
      * Connect to the database.
      *
      * Can optionally have the database details passed to it but this is deprecated functionality. Pass the details in the constructor instead.
      *
      * @param $host db hostname
      * @param $dbase db schema name
      * @param $uid db username
      * @param $pwd db password
      * @param $persist If true, make a persistent connection.
      * @return void
      */
     final public function connect($host = '', $dbase = '', $uid = '', $pwd = '', $persist = false) {
          
        $uid = $uid ? $uid : $this->config['user'];
        $pwd = $pwd ? $pwd : $this->config['pass'];
        $host = $host ? $host : $this->config['host'];
        $dbase = str_replace('`', '', $dbase ? $dbase : $this->config['dbase']);
        $charset = $this->config['charset'];
        $connection_method = $this->config['connection_method'];

        $tstart = $this->parent->getMicroTime();
        if (!($persist ? $this->make_persistent_connection($host, $uid, $pwd) : $this->make_connection($host, $uid, $pwd))) {
            $this->parent->messageQuit('Failed to create the database connection!');
            exit;
        } else {
            if (!@$this->select_db($dbase)) {
                $this->parent->messageQuit("Failed to select the database '$dbase'!");
                exit;
            }
            $this->host = $host;
            $this->dbase = $dbase;
            @$this->query("{$connection_method} {$charset}"); // We should be able to remove this and it's associated functionality
            $this->set_charset($charset);
            
            $tend = $this->parent->getMicroTime();
            $totaltime = $tend - $tstart;
            if ($this->parent->dumpSQL) {
                $this->parent->queryCode .= "<fieldset style='text-align:left'><legend>Database connection</legend>" . sprintf("Database connection was created in %2.4f s", $totaltime) . "</fieldset><br />";
            }
            $this->isConnected = true;
            $this->parent->queryTime += $totaltime;
        }
    }

    /**
      * Test database connection or selection
	  *
	  * Intended for installer use only.
	  * Does not set character set of connection.
	  *
	  * Will return false on failure and will not log errors or display any errors via DocumentParser::MessageQuit().
      *
      * @param $host db hostname
      * @param $dbase Optional db schema name
      * @param $uid db username
      * @param $pwd db password
      * @param $query Optional query to run
      */
	function test_connect($host = '', $dbase = '', $uid = '', $pwd = '', $query = '') {

        $uid = $uid ? $uid : $this->config['user'];
        $pwd = $pwd ? $pwd : $this->config['pass'];
        $host = $host ? $host : $this->config['host'];

		$output = @$this->make_connection($host, $uid, $pwd); 
		
		if ($this->conn && !empty($dbase)) {
			$dbase = str_replace('`', '', $dbase ? $dbase : $this->config['dbase']);
			if ($output = @$this->select_db($dbase)) {
				$this->dbase = $dbase;
			}
		}
		
		if ($this->conn && !empty($query)) {
			$output = $this->query($query, true);
		}
		
		return $output;
	}
	
	/**
     * Make a persistent connection to the database.
     *
     * @return void
     */
    final public function p_connect() {
    	$this->connect('', '', '', '', true);
   	}
    
    /**
     * Check for connection
     */
    final protected function connection_check() {
    	  if (empty ($this->conn)) { 
            $this->connect();
        }
    }
    
    /**
     * Connect to the RDBMS.
     * 
     * RDBMS specific.
     */
    abstract protected function make_connection($host, $uid, $pwd);

    /**
     * Connect to the RDBMS persistently.
     * 
     * RDBMS specific.
     */
    abstract protected function make_persistent_connection($host, $uid, $pwd);

    /**
     * Set connection character set
     *
     * RDBMS specific
     */
    abstract protected function set_charset($charset);

    /**
     * Select a database.
     * 
     * RDBMS specific.
     */
    abstract protected function select_db($dbname);

	// ----------
	// DISCONNECT
	// ----------

    /**
     * Disconnect from db.
     * 
     * RDBMS specific.
     */
    abstract public function disconnect();

	// ----------------
	// CLIPPERCMS DBAPI
	// ----------------

    /**
     * Escape a string
     *
     * @param string $s
     * @return string
     */    
    final public function escape($s) {
        $this->connection_check();
        return $this->_escape($s);
    }

    /**
     * Query the database.
     *
     * Developers should use select, update, insert (etc), delete where possible
     *
     * @param string $sql
     * @param bool $suppress_errors If true, return false on error, otherwise quit via MessageQuit().
     * @return resource
     */
    final public function query($sql, $suppress_errors = false) {
        $this->connection_check();
        $tstart = $this->parent->getMicroTime();
        if (!$result = @$this->_query($sql, $this->conn)) {
        	if ($suppress_errors) {
        		return false;
        	} else {
	            $this->parent->messageQuit("Execution of a query to the database failed - " . $this->getLastError(), $sql);
	        }
        } else {
            $tend = $this->parent->getMicroTime();
            $totaltime = $tend - $tstart;
            $this->parent->queryTime = $this->parent->queryTime + $totaltime;
            if ($this->parent->dumpSQL) {
                $this->parent->queryCode .= "<fieldset style='text-align:left'><legend>Query " . ($this->executedQueries + 1) . " - " . sprintf("%2.4f s", $totaltime) . "</legend>" . $sql . "</fieldset><br />";
            }
            $this->parent->executedQueries = $this->parent->executedQueries + 1;
            return $result;
        }
    }

    /**
     * DELETE
     *
     * @param string $from
     * @param string $where
     * @param string|array $fields
     * @return resource
     */
    final public function delete($from, $where = '', $fields = '') {
        if (!$from) {
            return false;
        } else {
            if (is_array($fields)) $fields = implode(',', $fields);
            $where = ($where != '') ? "WHERE $where" : '';
            return $this->query("DELETE $fields FROM $from $where");
        }
    }

    /**
     * SELECT
     * 
     * @param string|array $fields
     * @param string $from
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return resource
     */
    final public function select($fields = "*", $from = '', $where = '', $orderby = '', $limit = '') {
        if (!$from) {
            return false;
        } else {
            if (is_array($fields)) $fields = implode(',', $fields);
            $where = ($where != '') ? "WHERE $where" : '';
            $orderby = ($orderby != '') ? "ORDER BY $orderby " : '';
            $limit = ($limit != '') ? "LIMIT $limit" : '';
            return $this->query("SELECT $fields FROM $from $where $orderby $limit");
        }
    }

    /**
     * UPDATE
     *
     * @param string|array $fields
     * @param string $table
     * @param string $where
     * @return resource
     */
    final public function update($fields, $table, $where = '') {
        if (!$table) {
            return false;
        } else {
            if (!is_array($fields)) {
                $flds = $fields;
            } else {
                $flds = '';
                foreach ($fields as $key => $value) {
                    if (!empty ($flds)) $flds .= ',';
                    $flds .= "$key = '$value'";
                }
            }
            $where = ($where != '') ? "WHERE $where" : '';
            return $this->query("UPDATE $table SET $flds $where");
        }
    }

    /**
     * INSERT
     *
     * @param string|array $fields
     * @param string $intotable
     * @param string|array $fromfields
     * @param string $fromtable
     * @param string $where
     * @param string $limit
     * @return mixed Either last id inserted (if supported) or the result from the query
     */
    final public function insert($fields, $intotable, $fromfields = "*", $fromtable = '', $where = '', $limit = '') {
    	return $this->__insert('INSERT', $fields, $intotable, $fromfields, $fromtable, $where, $limit);
    }

    /**
     * INSERT IGNORE
     *
     * @param string|array $fields
     * @param string $intotable
     * @param string|array $fromfields
     * @param string $fromtable
     * @param string $where
     * @param string $limit
     * @return mixed Either last id inserted (if supported) or the result from the query
     */
    public function insert_ignore($fields, $intotable, $fromfields = "*", $fromtable = '', $where = '', $limit = '') {
    	return $this->__insert('INSERT IGNORE', $fields, $intotable, $fromfields, $fromtable, $where, $limit);
    }    
    
    /**
     * REPLACE
     *
     * @param string|array $fields
     * @param string $intotable
     * @param string|array $fromfields
     * @param string $fromtable
     * @param string $where
     * @param string $limit
     * @return mixed Either last id inserted (if supported) or the result from the query
    */
    public function replace($fields, $intotable, $fromfields = "*", $fromtable = '', $where = '', $limit = '') {
    	return $this->__insert('REPLACE', $fields, $intotable, $fromfields, $fromtable, $where, $limit);
    }    

    /**
     * Internal private insert function for use by the above.
     */
    private function __insert($insert_method, $fields, $intotable, $fromfields = "*", $fromtable = '', $where = '', $limit = '') {
        if (!$intotable) {
            return false;
        } else {
            $sql = '';
            if (!is_array($fields))
                $flds = $fields;
            else {
                $keys = array_keys($fields);
                $values = array_values($fields);
                $flds = '('.implode(',', $keys).') '.(!$fromtable && $values ? 'VALUES(\''.implode('\',\'', $values).'\')' : '');
            }
            
            if ($fromtable) {
                if (is_array($fromfields)) $fromfields = implode(',', $fromfields);
                $where = ($where != '') ? "WHERE $where" : '';
                $limit = ($limit != '') ? "LIMIT $limit" : '';
                $sql = "SELECT $fromfields FROM $fromtable $where $limit";
            }

            $rt = $this->query("$insert_method $intotable $flds $sql");
            $lid = $this->_getInsertId();
            return $lid ? $lid : $rt;
        }
    }

    /**
     * Get the last insert ID
     *
     * @return void
     */
    final public function getInsertId() {
        return $this->_getInsertId();
    }
    
    /**
     * Get the number of affected rows.
     *
     * RDBMS specific.
     *
     * @return int
     */
    abstract public function getAffectedRows();

    /**
     * Get the last error.
     *
     * RDBMS specific.
     *
     * @param string $type
     * @return string
     */
    abstract public function getLastError($return_number = false);

    /**
     * Get the number of rows in a resultset. Return 0 if resultset invalid.
     *
     * @param resource $rs Resultset
     * @return int
     */
    final public function getRecordCount($rs) {
        return $rs ? $this->_recordcount($rs) : 0;
    }

    /**
     * Return an array of column values
     *
     * @param resource $rs Resultset
     * @param string $mode 'assoc', 'num' or 'both'.
     * @return array
     */
    final public function getRow($rs, $mode = 'assoc') {
        if ($rs) {
            if ($mode == 'assoc') {
                return $this->_getRowAssoc($rs);
            } elseif ($mode == 'num') {
                return $this->_getRowNumeric($rs);
            } elseif ($mode == 'both') {
                return $this->_getRowBoth($rs);
            } else {
                $this->parent->messageQuit("Unknown get type ($mode) specified for getRow - must be empty, 'assoc', 'num' or 'both'.");
            }
        }
    }
    
    /**
     * Returns an array of the values found on column $name
     *
     * @param string $name Column name
     * @param mixed $rsq Resultset or query string
     * @return array
     */
    final public function getColumn($name, $rsq) {
    
        if (is_string($rsq)) {
            $rsq = $this->query($rsq);
        }
        
        if ($rsq) {
            $col = array ();
            while ($row = $this->getRow($rsq)) {
                $col[] = $row[$name];
            }
            return $col;
        }
    }
  
    /**
     * Returns an array containing the column names in a resultset.
     *
     * @param mixed $rsq Resultset or query string
     * @return array
     */
    final public function getColumnNames($rsq) {
    
        if (is_string($rsq)) {
            $rsq = $this->query($rsq);
        }

        return $this->_getColumnNames($rsq);
    }

    /**
     * Returns the value from the first column in the set.
     *
     * @param mixed $rsq Resultset or query string
     * @return string
     */
    final public function getValue($rsq) {

        if (is_string($rsq)) {
            $rsq = $this->query($rsq);
        }

        if ($rsq) {
            $r = $this->getRow($rsq, 'num');
            return $r[0];
        }
    }

    /**
     * Returns an XML representation of the dataset $rsq
     *
     * @param mixed Resultset or query string
     * @return string
     */
    final public function getXML($rsq) {

        if (is_string($rsq)) {
            $rsq = $this->query($rsq);
        }
        
        $xmldata = "<xml>\r\n<recordset>\r\n";
        while ($row = $this->getRow($rsq, 'assoc')) {
            $xmldata .= "<item>\r\n";
            for ($j = 0; $line = each($row); $j++) {
                    $xmldata .= "<{$line['key']}>{$line['value']}</{$line['key']}>\r\n";
            }
            $xmldata .= "</item>\r\n";
        }
        $xmldata .= "</recordset>\r\n</xml>";
        return $xmldata;
    }

    /**
     * Returns an array of structure detail for each column of a
     *
     * @param string $table The full name of the database table
     * @return array
     */
    abstract public function getTableMetaData($table);


    /**
     * Returns a string containing the database server version
     *
     * @return string
     */
    abstract public function getVersion();
    
    /**
     * Free memory associated with a resultset
     *
     * @return void
     */
    abstract public function freeResult($rs);
    
    /**
     * Prepares a date in the proper format for specific database types given a UNIX timestamp
     *
     * @param int $timestamp: a UNIX timestamp
     * @param string $fieldType: the type of field to format the date for
     *            (in MySQL, you have DATE, TIME, YEAR, and DATETIME)
     * @return string
     */
    public function prepareDate($timestamp, $fieldType = 'DATETIME') {
        $date = '';
        if (!$timestamp === false && $timestamp > 0) {
            switch ($fieldType) {
                case 'DATE' :
                    $date = date('Y-m-d', $timestamp);
                    break;
                case 'TIME' :
                    $date = date('H:i:s', $timestamp);
                    break;
                case 'YEAR' :
                    $date = date('Y', $timestamp);
                    break;
                default :
                    $date = date('Y-m-d H:i:s', $timestamp);
                    break;
            }
        }
        return $date;
    }

    /**
     * @param string|resource $rsq Resultset or SQL query
     * @param array $params Data grid parameters
     *            columnHeaderClass
     *            tableClass
     *            itemClass
     *            altItemClass
     *            columnHeaderStyle
     *            tableStyle
     *            itemStyle
     *            altItemStyle
     *            columns
     *            fields
     *            colWidths
     *            colAligns
     *            colColors
     *            colTypes
     *            cellPadding
     *            cellSpacing
     *            header
     *            footer
     *            pageSize
     *            pagerLocation
     *            pagerClass
     *            pagerStyle
     *
     */
    final public function getHTMLGrid($rsq, $params) {

        if (is_string($rsq)) {
            $rsq = $this->query($rsq);
        }

        if ($rsq) {
            require_once(dirname(__FILE__).'/../controls/datagrid.class.php');

            $grd = new DataGrid('', $rsq);

            if (!empty($params)) {
                foreach ($params as $key=>$value) {
                    $grd->$key = $value;
                }
            }

            return $grd->render();
        }
    }

    /**
    * Turns a recordset into a multidimensional array
    * @param resource $rs Resultset
    * @return mixed An array of row arrays from recordset, or empty array if
    *               the recordset was empty, returns false if no recordset
    *               was passed
    */
    final public function makeArray ($rs) {
        if (!$rs) {
            return false;
        } else {
            $rsArray = array();
            while ($row = $this->getRow($rs)) {
                $rsArray[] = $row;
            }
            return $rsArray;
        }
    }
    
    /**
     * Get name of host.
     *
     * @return string
     */
    final public function getHostname() {
    		return $this->host;
    }

    /**
     * Get name of database.
     *
     * @return string
     */
    final public function getDBname() {
    		return $this->dbase;
    }

	/**
	 * Is a variable a resultset handle?
	 *
	 * @param mixed $var
	 * @return bool
	 */
	abstract function is_handle($var);

	/**
	 * Test for presence of Clipper db tables
	 *
	 * @param string $prefix
	 * @return bool;
	 */
	abstract function tables_present($prefix);

	/**
	 * Get table engine
	 *
	 * @return string
	 */
	abstract function table_engine($table);

	// -------------------------------------------
	// LOW LEVEL RBDMS-SPECIFIC INTERNAL FUNCTIONS
	// -------------------------------------------

    /**
     * Escape a string,
     * 
     * RDBMS specific.
     */
    abstract protected function _escape($s);

    /**
     * Make a query.
     * 
     * RDBMS specific.
     */
    abstract protected function _query($sql);

    /**
     * Get the last insert ID.
     * 
     * RDBMS specific.
     */
    abstract protected function _getInsertId();

    /**
     * Get the number of records in the resultset.
     *
     * RDBMS specific.
     */
    abstract protected function _recordcount($rs);

    /**
     * Get the column names in a resultset
     *
     * RDBMS specific.
     *
     * @param mixed $rs resultset
     * @return array
     */
    abstract protected function _getColumnNames($rs);
	
    /**
     * Get a row into an associative array.
     *
     * RDBMS specific.
     */
    abstract protected function _getRowAssoc($rs);

    /**
     * Get a row into a numeric array.
     *
     * RDBMS specific.
     */
    abstract protected function _getRowNumeric($rs);

    /**
     * Get a row into both an associative and numeric array.
     *
     * RDBMS specific.
     */
    abstract protected function _getRowBoth($rs);

}

