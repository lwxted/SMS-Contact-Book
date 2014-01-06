<?php
	
/**
* DB Class
* 
* MySQL Database portal.
* 
* @since 0.1
*/

/**
 * MySQL Database Access Abstract Object
 */
class DB
{

	/**
	 * The username of login to a database
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbuser;

	/**
	 * The password of login to a database
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbpassword;

	/**
	 * The name of database to be accessed
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbname;

	/**
	 * The host address of the database
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbhost;

	/**
	 * The port to the database
	 * @var int
	 * @since 0.1
	 * @access private
	 */
	private $dbport;

	/**
	 * The charset of the database
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbcharset;

	/**
	 * The collate type of the database
	 * @var string
	 * @since 0.1
	 * @access private
	 */
	private $dbcollate;

	/**
	 * The connection handler of the database
	 * @var resource
	 * @since 0.1
	 * @access private
	 */
	private $dbcon;

	/**
	 * The mysqli result returned from a SELECT query
	 * @var resource
	 * @since 0.1
	 * @access private
	 */
	private $select_result;
	
	/**
	 * Whether the database is ready for query
	 * @var boolean
	 * @since 0.1
	 * @access private
	 * @deprecated
	 */
	private $ready;

	/**
	 * Construct a DB object for further access to the database.
	 * @param string $dbuser     The username of login to the database
	 * @param string $dbpassword The password of login to the database
	 * @param string $dbname     The name of database to be used
	 * @param string $dbhost     The host address through which the database can be accessed
	 * @param int 	 $dbport     The port through which the database can be accessed
	 */
	function __construct( $dbuser, $dbpassword, $dbname, $dbhost, $dbport ) {
		register_shutdown_function( array( $this, '__destruct' ) );

		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;
		if ( defined('DB_CHARSET') && DB_CHARSET ) {
			$this->dbcharset = DB_CHARSET;
		} else {
			$this->dbcharset = 'UTF8';
		}
		if ( defined('DB_COLLATE') && DB_COLLATE ) {
			$this->dbcollate = DB_COLLATE;
		} else {
			$this->dbcollate = 'utf8_general_ci';
		}
		
		$this->db_connect();
	}

	/**
	 * PHP5 style magic getter, used to lazy-load expensive data.
	 *
	 * @since 0.1
	 *
	 * @param string $name The private member to get, and optionally process
	 * @return mixed The private member
	 */
	function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 0.1
	 *
	 * @param string $name  The private member to set
	 * @param mixed  $value The value to set
	 */
	function __set( $name, $value ) {
		$this->$name = $value;
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 0.1
	 *
	 * @param string $name  The private member to check
	 *
	 * @return bool If the member is set or not
	 */
	function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 0.1
	 *
	 * @param string $name  The private member to unset
	 */
	function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Destruct method. Close any MySQL connections. Unset the object.
	 */
	function __destruct () {
		if (isset($this->con)) {
			if ( DEBUG_MODE ) {
				mysqli_close($this->con);
			} else {
				@mysqli_close($this->con);
			}
			unset($this->con);
		}
	}


	/**
	 * Establish connection to the database. Bail if connection error occured.
	 * @since 0.1
	 * @access private
	 * @todo Set up bailing mechanism.
	 */
	private function db_connect() {

		$new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : TRUE;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

		// Connect to database.
		if ( DEBUG_MODE ) {
			$this->dbcon = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname, $this->dbport);
		} else {
			$this->dbcon = @mysqli_connect($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname, $this->dbport);
		}

		if ( mysqli_connect_errno () ) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo ("Failed to connect to MySQL: " . mysqli_connect_error() . '<br />');
			}
		}

		// Set charset.
		mysqli_set_charset ( $this->dbcon, $this->dbcharset );

		// Set collate.
		// mysqli_query ( $this->dbcon, "COLLATE {$this->dbcollate}" );

		// Set the database as ready for queries.
		$this->ready = TRUE;
	}


	/**
	 * Escapes MySQL string for further action.
	 * @param  mixed   $string                   A string or int before escaping
	 * @param  string  $regex             		 Formatting according to regex
	 * @param  boolean $reconnect_attempt        Whether or not to attempt to reconnect,
	 *                                           before any action is performed. 
	 *                                           Defaults to FALSE.
	 * @return mixed                             String or int after escaping.
	 */
	private function escape ($string, $regex='@', $reconnect_attempt = FALSE) {
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		if (is_string($string)) {
			$string = mysqli_real_escape_string($this->dbcon, $string);
			// $string = addcslashes($string, '%_');
			$string = "'" . str_replace('@', $string, $regex) . "'";
		}
		return $string;
	}

	/**
	 * Return a formatted WHERE query segment based on the input identifier array
	 * @param  array   $identifier_array The array that specifies the WHERE statement query
	 * @param  boolean $o                Whether or not 'WHERE' is included in the output
	 * @return string                    Formatted WHERE query segment
	 * @access public
	 * @since 0.1
	 * 
	 * @var array
	 * $identifier_array contains a list of arrays, 
	 * each of which checks attribute 'name', 'value', 'atype', 'ctype', 'regex'.
	 * It follows the following format:
	 * $identifier_array = array(
	 * 	0 => array(
	 *  	'name' => '<field1>',
	 *   	'value' => '<value1>',
	 *    	'ctype' => 'LIKE',
	 *     	'atype' => 'AND',
	 *      'regex' => '%@%'
	 *  )
	 * );
	 * This will produce:
	 * (`<field1>` LIKE '%<value1>%') AND
	 * 
	 * One may format nested WHERE queries using multi-layer arrays.
	 * $identifier_array = array(
	 * 	0 => array(
	 *  	'value' => array(
	 *   		0 => array(
	 *  	  		'name' => '<field1>',
	 *   	   		'value' => '<value1>',
	 *    	    	'ctype' => '=',
	 *     	    	'atype' => 'OR',
	 *           	'regex' => '@'
	 *            ),
	 *          1 => array(
	 *  	  		'name' => '<field2>',
	 *   	   		'value' => '<value2>',
	 *    	    	'ctype' => 'LIKE',
	 *           	'regex' => '%@%'
	 *            )
	 * 		),
	 *  	'atype' => 'AND'
	 *  ),
	 *  1 => array(
	 *  	'value' => array(
	 *   		0 => array(
	 *  	  		'name' => '<field1>',
	 *   	   		'value' => '<value1>',
	 *    	    	'ctype' => '=',
	 *     	    	'atype' => 'AND',
	 *           	'regex' => '@'
	 *            ),
	 *          1 => array(
	 *  	  		'name' => '<field2>',
	 *   	   		'value' => '<value2>',
	 *    	    	'ctype' => 'LIKE',
	 *           	'regex' => '%@%'
	 *            )
	 * 		)
	 *  )
	 * );
	 * This will produce:
	 * ((`<field1>` = '<value1>') OR (`<field2>` LIKE '%<value2>%')) AND
	 * ((`<field1>` = '<value1>') AND (`<field2>` LIKE '%<value2>%'))
	 */
	function _where ($identifier_array, $o = TRUE) {
		$statement = '';
		if ( !isset($identifier_array) || !$identifier_array ) {
			return $statement;
		}
		$statement .= $o ? ' WHERE ' : ' ';
		foreach ($identifier_array as $query_array) {
			$sub_stmt = '(';
			if ( isset($query_array['name']) && ! is_array($query_array['value']) ) {
				$regex = isset($query_array['regex']) ? $query_array['regex'] : '@';
				$sub_stmt .= '`' . $query_array['name'] . '` ';
				$sub_stmt .= (isset($query_array['ctype']) ? $query_array['ctype'] : '=') . ' ';
				$sub_stmt .= $this->escape($query_array['value'], $regex, FALSE);
			} else {
				// Incoming value is an array.
				$sub_stmt .= $this->_where($query_array['value'], FALSE);
			}
			$sub_stmt .= ')';
			$sub_stmt .= (isset($query_array['atype']) ? (' ' . $query_array['atype']).' ' : '');
			$statement .= $sub_stmt;
		}
		return $statement; 
	}

	/**
	 * Return a formatted SELECT query segment.
	 * @param  string  $table_name          The name of the table against which
	 *                                      the SELECT statement is to be performed
	 * @param  array   $select_column_names An array of column names
	 * @param  boolean $distinct            Whether only distinct rows are selected
	 * @return string                       Formatted SELECT query segment
	 * @access public
	 * @since 0.1
	 */
	function _select ($table_name, $select_column_names, $distinct = FALSE) {
		$statement = $distinct ? 'SELECT DISTINCT ' : 'SELECT ';
		if ( isset($select_column_names) && $select_column_names ) {
			$count_select_column_names = count($select_column_names);
			for ($pointer = 0; $pointer < $count_select_column_names; $pointer++) { 
				if ($pointer == $count_select_column_names - 1 ) {
					$statement .= '`' . $select_column_names[$pointer] . '` ';
				} else {
					$statement .= '`' . $select_column_names[$pointer] . "`, ";
				}
			}
		} else {
			$statement .= "* ";
		}
		$statement .= "FROM `{$table_name}` " ;
		return $statement;
	}

	/**
	 * Return a formatted LIMIT & OFFSET query segment.
	 * @param  int    $limit  Limit
	 * @param  int    $offset Offset
	 * @return string         Formatted LIMIT / OFFSET query segment
	 * @access public
	 * @since 0.1
	 */
	function _limit_offset ($limit, $offset) {
		$statement = '';
		if (isset($limit) && $limit) {
			if (is_integer($limit)) {
				$statement .= " LIMIT {$limit}";
			} else {
				$statement .= " LIMIT " . intval($limit);
			}
		} else {
			$statement .= " LIMIT 0";
		}
		if (isset($offset) && $offset) {
			if (is_integer($offset)) {
				$statement .= " OFFSET {$offset} ";
			} else {
				$statement .= " OFFSET " . intval($offset) . " ";
			}
		}
		return $statement;
	}

	/**
	 * Return a formatted ORDER BY query segment.
	 * @param  array  $identifier_array The array that specifies the ORDER BY query
	 * @return string                   Formatted ORDER BY query segment
	 * @access public
	 * @since 0.1
	 * 
	 * @var array
	 * $identifier_array contains a list of arrays, 
	 * each of which checks attribute 'column_name', 'asc_desc'.
	 * 
	 * It follows the following format:
	 * $identifier_array = array(
	 * 	0 => array(
	 *  	'column_name' => '<column1>',
	 *   	'asc_desc' => 'ASC',
	 *  ),
	 *  1 => array(
	 *  	'column_name' => '<column2>',
	 *   	'asc_desc' => 'DESC'
	 * 	)
	 * );
	 * This will produce:
	 * ORDER BY `<column1>` ASC, `<column2>` DESC
	 * 
	 */
	function _order_by ($identifier_array, $o = TRUE) {
		$statement = '';
		if ( !isset($identifier_array) || !$identifier_array ) {
			return $statement;
		}
		$statement .= $o ? ' ORDER BY ' : ' ';
		$c = count($identifier_array);
		for ($count = 0; $count < $c; $count++) {
			$query_array = $identifier_array[$count]; 
			$statement .= '`'. $query_array['column_name'] . '` ';
			if ($count == $c - 1) {
				$statement .= $query_array['asc_desc'] . ' ';
			} else {
				$statement .= $query_array['asc_desc'] . ', ';
			}
		}
		return $statement;
	}

	/**
	 * Return a formatted DELETE query segment.
	 * @param  string $table_name The name of the table against which the
	 *                            DELETE query is to be performed.
	 * @return string             Formatted DELETE query statement.
	 * @access public
	 * @since 0.1
	 */
	function _delete ($table_name) {
		return "DELETE FROM `{$table_name}`";
	}

	/**
	 * Return a formatted INSERT query segment.
	 * @param  string $table_name The name of the table against which the 
	 *                            INSERT query is to be performed
	 * @param  array  $data_array Array that encloses the data
	 * @return string             Formatted INSERT query segment
	 * @access public
	 * @since 0.1
	 * 
	 * @var array
	 * $data_array contains a list of arrays, 
	 * each of which checks attribute 'name', 'value'.
	 * It follows the following format:
	 * $data_array = array (
	 * 	'<column1>' => '<value1>',
	 *  '<column2>' => '<value2>'
	 * )
	 * This will produce:
	 * INSERT INTO `<table_name>` (<column1>, <column2>)
	 * VALUES (<value1>, <value2>);
	 * 
	 * You may also insert multiple objects one time.
	 * This can be achieved by specifying an array as the value.
	 * $data_array = array (
	 * 	'<column1>' => array(
	 *  				'<value1>',
	 *      			'<value2>'
	 *         			),
	 *  '<column2>' => array(
	 *  				'<value3>',
	 *      			'<value4>'
	 *         			)
	 * )
	 * This will produce:
	 * INSERT INTO `<table_name>` (<column1>, <column2>)
	 * VALUES (<value1>, <value2>), (<value3>, <value4>)
	 */
	function _insert ($table_name, $data_array) {
		$statement = 'INSERT INTO ';
		$statement .= "`{$table_name}` (";
		$sub_stmt_1 = '';
		$sub_stmt_2 = '';
		$p_pointer = 0;
		$p_count = count($data_array);
		$first_column_name = '';
		foreach ($data_array as $column_name => $value) {
			if ($p_pointer == 0) {
				$first_column_name = $column_name;
			}
			if ($p_pointer == $p_count - 1) {
				$sub_stmt_1 .= '`' . $column_name . '`';
			} else {
				$sub_stmt_1 .= '`' . $column_name . '`, ';
			}
			$p_pointer ++;
		}
		$p_pointer = 0;
		$statement .= $sub_stmt_1;
		$statement .= ') VALUES ';
		if (is_array($data_array[$first_column_name])) {
			$s_count = count($data_array[$first_column_name]);
			for ($s_pointer = 0; $s_pointer < $s_count; $s_pointer++) {
				$sub_stmt_2 .= '(';
				$p_pointer = 0;
				foreach ($data_array as $column_name => $value) {
					if ($p_pointer == $p_count - 1) {
						$sub_stmt_2 .= $this->escape($data_array[$column_name][$s_pointer]);
					} else {
						$sub_stmt_2 .= $this->escape($data_array[$column_name][$s_pointer]) . ', ';
					}
					$p_pointer ++;			
				}
				$sub_stmt_2 .= ')';
				if ($s_pointer != $s_count - 1) {
					$sub_stmt_2 .= ', ';
				}
			}
		} else {
			$sub_stmt_2 .= '(';
			foreach ($data_array as $column_name => $value) {
				if ($p_pointer == $p_count - 1) {
					$sub_stmt_2 .= $this->escape($data_array[$column_name]);
				} else {
					$sub_stmt_2 .= $this->escape($data_array[$column_name]) . ', ';
				}
				$p_pointer ++;
			}
			$sub_stmt_2 .= ')';
		}
		$statement .= $sub_stmt_2;
		return $statement;
	}

	/**
	 * Return a formatted UPDATE query segment.
	 * @param  string $table_name The name of the table against which the
	 *                            UPDATE statement is to be performed
	 * @return string             Formatted UPDATE query segment
	 * @access public
	 * @since 0.1
	 */
	function _update ($table_name) {
		return "UPDATE `{$table_name}`";
	}

	/**
	 * Return a formatted SET query segment.
	 * @param  string $data_array An array that signifies how the data
	 *                            should be changed.
	 * @return string 			  Formatted SET query segment
	 * @var array
	 * $data_array contains a list of arrays, 
	 * each of which checks attribute 'name', 'value'.
	 * It follows the following format:
	 * $data_array = array (
	 * 	'<column1>' => '<value1>',
	 *  '<column2>' => '<value2>'
	 * )
	 * This will produce:
	 * SET `<column1>` = '<value1>', `<column2>` = '<value2>'
	 */
	function _set ($data_array, $o = TRUE) {
		$statement = $o ? ' SET ' : ' ';
		$count = count($data_array);
		$pointer = 0;
		foreach ($data_array as $column_name => $value) {
			$statement .= '`' . $column_name . '` = ';
			if ($pointer == $count - 1) {
				$statement .= $this->escape($data_array[$column_name]);
			} else {
				$statement .= $this->escape($data_array[$column_name]) . ', ';
			}
			$pointer ++;
		}
		return $statement;
	}

	/**
	 * Perform a SELECT query against the connected MySQL database.
	 * @param  boolean $distinct            Whether or not to enable DISTINCT
	 * @param  string  $table_name          The name of the table to perform the SELECT query
	 * @param  array   $select_column_names The names of the selected columns
	 * @param  array   $identifier_array    @uses _where()
	 * @param  int     $limit               LIMIT
	 * @param  int     $offset              OFFSET
	 * @param  boolean $reconnect_attempt   Whether or not to attempt to reconnect.
	 *                                      Defaults to TRUE.
	 * @return mysqli_result|FALSE			The fetched mysqli_result object or FALSE
	 * @todo Set up bailing mechanism
	 * @access public
	 * @since 0.1
	 */
	function select ($distinct, 
					 $table_name, 
					 $select_column_names,
					 $identifier_array = null,
					 $order_by_array = null,
					 $limit = null,
					 $offset = null,
					 $reconnect_attempt = TRUE
					 )
	{
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		$statement = $this->_select($table_name, $select_column_names, $distinct);
		$statement .= empty($identifier_array) ? '' : $this->_where($identifier_array, TRUE);
		$statement .= empty($order_by_array) ? '' : $this->_order_by($order_by_array, TRUE);
		$statement .= empty($limit) && empty($offset) ? '' : $this->_limit_offset($limit, $offset);
		// echo $statement . '<br />';
		$result = mysqli_query($this->dbcon, $statement);
		if (! $result ) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo "Error. " . mysqli_error($this->dbcon);
			}
			return FALSE;
		} else {
			return $result;
		}
	}

	/**
	 * Perform a INSERT query against the connected MySQL database.
	 * @param  string  $table_name        The name of the table against which the 
	 *                                    INSERT query is to be performed
	 * @param  array   $data_array        Array that encloses the data
	 * @param  boolean $reconnect_attempt Whether or not to attempt to reconnect
	 * @return boolean			  		  Whether the operation was performed successfully
	 * @access public
	 * @since 0.1
	 * @todo Set up bailing mechanism
	 */
	function insert ($table_name, 
					 $data_array, 
					 $reconnect_attempt = TRUE
					 ) 
	{
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		$statement = $this->_insert($table_name, $data_array);
		$result = mysqli_query($this->dbcon, $statement);
		if (! $result ) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo "Error. " . mysqli_error($this->dbcon);
			}
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Perform an UPDATE query against the connected MySQL Database.
	 * @param  string  $table_name        The name of the table to perform the UPDATE query
	 * @param  array   $data_array        The data to be updated against the database
	 * @param  array   $identifier_array  The array to identify against which row to perform the query
	 * @param  boolean $reconnect_attempt Whether or not to attempt to reconnect
	 * @return int|FALSE                  Number of rows affected|FALSE
	 * @since 0.1
	 * @access public
	 * @todo Set up bailing mechanism
	 */
	function update ($table_name, 
					 $data_array, 
					 $identifier_array = null, 
					 $reconnect_attempt = TRUE) 
	{
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		$statement = $this->_update($table_name);
		$statement .= $this->_set($data_array);
		$statement .= empty($identifier_array) ? '' : $this->_where($identifier_array);
		if (! mysqli_query($this->dbcon, $statement)) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo "Error. " . mysqli_error($this->dbcon);
			}
			return FALSE;
		} else {
			return mysqli_affected_rows($this->dbcon);
		}
	}

	/**
	 * Perform a DELETE query against the connected MySQL database.
	 * @param  string  $table_name        The name of the table to perform 
	 *                                    the DELETE query
	 * @param  array   $identifier_array  @uses _where()
	 * @param  boolean $reconnect_attempt Whether or not to attempt to reconnect
	 * @return boolean                    Whether or not the query is successfully performed
	 * @since 0.1
	 * @access public
	 * @todo Set up bailing mechanism
	 */
	function delete ($table_name, 
					 $identifier_array = null, 
					 $reconnect_attempt = TRUE)
	{
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		$statement = $this->_delete($table_name);
		$statement .= empty($identifier_array) ? '' : $this->_where($identifier_array);
		if (! mysqli_query($this->dbcon, $statement)) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo "Error. " . mysqli_error($this->dbcon);
			}
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Retrieve the next entry represented as an array.
	 * @param  resource $result     mysqli_result object returned from the select function
	 * @param  int      $resulttype Desired result type
	 * @return array                An array that represents the next entry.
	 * @since 0.1
	 * @access public
	 */
	function next_result ($result, $resulttype = MYSQLI_ASSOC) {
		return mysqli_fetch_array($result, $resulttype);
	}

	/**
	 * Perform a simple MySQL query using a specified query string.
	 * @param  string $query_string The query string to be performed
	 * @return mysqli_result|FALSE
	 * @access public
	 * @since 0.1
	 * @todo Set up bailing mechanism.
	 */
	function query ($query_string, $reconnect_attempt = TRUE) {
		if ($reconnect_attempt) {
			// If the connection is lost, try to reconnect.
			if ( !isset( $this->dbcon ) || ! $this->dbcon )  {
				$this->db_connect();
			}
		}
		$result = mysqli_query($this->dbcon, $query_string);
		if (! $result) {
			/* ===== Bail ===== */
			if (DEBUG_DISPLAY) {
				echo "Error. " . mysqli_error($this->dbcon);
			}
			return FALSE;
		}
		return $result;
	}
}
