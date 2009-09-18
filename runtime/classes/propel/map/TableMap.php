<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

/**
 * TableMap is used to model a table in a database.
 *
 * GENERAL NOTE
 * ------------
 * The propel.map classes are abstract building-block classes for modeling
 * the database at runtime.  These classes are similar (a lite version) to the
 * propel.engine.database.model classes, which are build-time modeling classes.
 * These classes in themselves do not do any database metadata lookups, but instead
 * are used by the MapBuilder classes that were generated for your datamodel. The
 * MapBuilder that was created for your datamodel build a representation of your
 * database by creating instances of the DatabaseMap, TableMap, ColumnMap, etc.
 * classes. See propel/templates/om/php5/MapBuilder.tpl and the classes generated
 * by that template for your datamodel to further understand how these are put
 * together.
 *
 * @author     Hans Lellelid <hans@xmpl.org> (Propel)
 * @author     John D. McNally <jmcnally@collab.net> (Torque)
 * @author     Daniel Rall <dlr@finemaltcoding.com> (Torque)
 * @version    $Revision$
 * @package    propel.map
 */
class TableMap {

	/** The columns in the table. */
	private $columns;

	/** The database this table belongs to. */
	private $dbMap;

	/** The name of the table. */
	private $tableName;

	/** The PHP name of the table. */
	private $phpName;

	/** The Classname for this table */
	private $classname;

	/** Whether to use an id generator for pkey. */
	private $useIdGenerator;

	/**
	 * Object to store information that is needed if the
	 * for generating primary keys.
	 */
	private $pkInfo;

	/**
	 * Construct a new TableMap.
	 *
	 * @param      string $tableName The name of the table.
	 * @param      DatabaseMap $containingDB A DatabaseMap that this table belongs to.
	 */
	public function __construct($tableName, DatabaseMap $containingDB)
	{
		$this->tableName = $tableName;
		$this->dbMap = $containingDB;
		$this->columns = array();
	}

	/**
	 * Normalizes the column name, removing table prefix and uppercasing.
	 *
	 * article.first_name becomes FIRST_NAME
	 *
	 * @param      string $name
	 * @return     string Normalized column name.
	 */
	protected function normalizeColName($name)
	{
		if (false !== ($pos = strpos($name, '.'))) {
			$name = substr($name, $pos + 1);
		}
		$name = strtoupper($name);
		return $name;
	}

	/**
	 * Does this table contain the specified column?
	 *
	 * @param      mixed   $name name of the column or ColumnMap instance
	 * @param      boolean $normalize Normalize the column name (if column name not like FIRST_NAME)
	 * @return     boolean True if the table contains the column.
	 */
	public function containsColumn($name, $normalize = true)
	{
		if ($name instanceof ColumnMap) {
			$name = $name->getColumnName();
		} else if($normalize) {
		  $name = $this->normalizeColName($name);
		}
		return isset($this->columns[$name]);
	}

	/**
	 * Get the DatabaseMap containing this TableMap.
	 *
	 * @return     DatabaseMap A DatabaseMap.
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * Get the name of the Table.
	 *
	 * @return     string A String with the name of the table.
	 */
	public function getName()
	{
		return $this->tableName;
	}

	/**
	 * Get the PHP name of the Table.
	 *
	 * @return     string A String with the name of the table.
	 */
	public function getPhpName()
	{
		return $this->phpName;
	}

	/**
	 * Set the PHP name of the Table.
	 *
	 * @param      string $phpName The PHP Name for this table
	 */
	public function setPhpName($phpName)
	{
		$this->phpName = $phpName;
	}

	/**
	 * Get the Classname of the Propel-Classes belonging to this table.
	 * @return     string
	 */
	public function getClassname()
	{
		return $this->classname;
	}

	/**
	 * Set the Classname of the Table. Could be useful for calling
	 * Peer and Object methods dynamically.
	 * @param      string $classname The Classname
	 */
	public function setClassname($classname)
	{
		$this->classname = $classname;
	}

	/**
	 * Whether to use Id generator for primary key.
	 * @return     boolean
	 */
	public function isUseIdGenerator() {
		return $this->useIdGenerator;
	}

	/**
	 * Get the information used to generate a primary key
	 *
	 * @return     An Object.
	 */
	public function getPrimaryKeyMethodInfo()
	{
		return $this->pkInfo;
	}

	/**
	 * Returns array of ColumnMap objects that make up the primary key for this table.
	 * @return     array ColumnMap[]
	 */
	public function getPrimaryKeyColumns()
	{
		$pk = array();
		foreach ($this->columns as $col) {
			if ($col->isPrimaryKey()) {
				$pk[] = $col;
			}
		}
		return $pk;
	}

	/**
	 * Get a ColumnMap[] of the columns in this table.
	 *
	 * @return     array A ColumnMap[].
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Get a ColumnMap for the named table.
	 *
	 * @param      string    $name A String with the name of the table.
	 * @param      boolean   $normalize Normalize the column name (if column name not like FIRST_NAME)
	 * @return     ColumnMap A ColumnMap.
	 * @throws     PropelException if the column is undefined
	 */
	public function getColumn($name, $normalize = true)
	{
	  if ($normalize) {
	    $name = $this->normalizeColName($name);
	  }
		if (!$this->containsColumn($name, false)) {
			throw new PropelException("Cannot fetch ColumnMap for undefined column: " . $name);
		}
		return $this->columns[$name];
	}

	/**
	 * Add a primary key column to this Table.
	 *
	 * @param      string $columnName A String with the column name.
	 * @param      string $type A string specifying the Propel type.
	 * @param      boolean $isNotNull Whether column does not allow NULL values.
	 * @param      $size An int specifying the size.
	 * @return     ColumnMap Newly added PrimaryKey column.
	 */
	public function addPrimaryKey($columnName, $phpName, $type, $isNotNull = false, $size = null)
	{
		return $this->addColumn($columnName, $phpName, $type, $isNotNull, $size, true, null, null);
	}

	/**
	 * Add a foreign key column to the table.
	 *
	 * @param      string $columnName A String with the column name.
	 * @param      string $type A string specifying the Propel type.
	 * @param      string $fkTable A String with the foreign key table name.
	 * @param      string $fkColumn A String with the foreign key column name.
	 * @param      boolean $isNotNull Whether column does not allow NULL values.
	 * @param      int $size An int specifying the size.
	 * @param      string $defaultValue The default value for this column.
	 * @return     ColumnMap Newly added ForeignKey column.
	 */
	public function addForeignKey($columnName, $phpName, $type, $fkTable, $fkColumn, $isNotNull = false, $size = 0)
	{
		return $this->addColumn($columnName, $phpName, $type, $isNotNull, $size, false, $fkTable, $fkColumn);
	}

	/**
	 * Add a foreign primary key column to the table.
	 *
	 * @param      string $columnName A String with the column name.
	 * @param      string $type A string specifying the Propel type.
	 * @param      string $fkTable A String with the foreign key table name.
	 * @param      string $fkColumn A String with the foreign key column name.
	 * @param      boolean $isNotNull Whether column does not allow NULL values.
	 * @param      int $size An int specifying the size.
	 * @param      string $defaultValue The default value for this column.
	 * @return     ColumnMap Newly created foreign pkey column.
	 */
	public function addForeignPrimaryKey($columnName, $phpName, $type, $fkTable, $fkColumn, $isNotNull = false, $size = 0)
	{
		return $this->addColumn($columnName, $phpName, $type, $isNotNull, $size, true, $fkTable, $fkColumn);
	}

	/**
	 * Add a pre-created column to this table.  It will replace any
	 * existing column.
	 *
	 * @param      ColumnMap $cmap A ColumnMap.
	 * @return     ColumnMap The added column map.
	 */
	public function addConfiguredColumn($cmap)
	{
		$this->columns[ $cmap->getColumnName() ] = $cmap;
		return $cmap;
	}

	/**
	 * Add a column to the table.
	 *
	 * @param      string name A String with the column name.
	 * @param      string $type A string specifying the Propel type.
	 * @param      boolean $isNotNull Whether column does not allow NULL values.
	 * @param      int $size An int specifying the size.
	 * @param      boolean $pk True if column is a primary key.
	 * @param      string $fkTable A String with the foreign key table name.
	 * @param      $fkColumn A String with the foreign key column name.
	 * @param      string $defaultValue The default value for this column.
	 * @return     ColumnMap The newly created column.
	 */
	public function addColumn($name, $phpName, $type, $isNotNull = false, $size = null, $pk = false, $fkTable = null, $fkColumn = null)
	{

		$col = new ColumnMap($name, $this);

		if ($fkTable && $fkColumn) {
			if (strpos($fkColumn, '.') > 0 && strpos($fkColumn, $fkTable) !== false) {
				$fkColumn = substr($fkColumn, strlen($fkTable) + 1);
			}
			$col->setForeignKey($fkTable, $fkColumn);
		}

		$col->setType($type);
		$col->setPrimaryKey($pk);
		$col->setSize($size);
		$col->setPhpName($phpName);
		$col->setNotNull($isNotNull);

		$this->columns[$name] = $col;

		return $this->columns[$name];
	}

	/**
	* Add a validator to a table's column
	*
	* @param      string $columnName The name of the validator's column
	* @param      string $name The rule name of this validator
	* @param      string $classname The dot-path name of class to use (e.g. myapp.propel.MyValidator)
	* @param      string $value
	* @param      string $message The error message which is returned on invalid values
	* @return     void
	*/
	public function addValidator($columnName, $name, $classname, $value, $message)
	{
		if (false !== ($pos = strpos($columnName, '.'))) {
			$columnName = substr($columnName, $pos + 1);
		}

		$col = $this->getColumn($columnName);
		if ($col !== null) {
			$validator = new ValidatorMap($col);
			$validator->setName($name);
			$validator->setClass($classname);
			$validator->setValue($value);
			$validator->setMessage($message);
			$col->addValidator($validator);
		}
	}

	/**
	 * Set whether or not to use Id generator for primary key.
	 * @param      boolean $bit
	 */
	public function setUseIdGenerator($bit) {
		$this->useIdGenerator = $bit;
	}

	/**
	 * Sets the pk information needed to generate a key
	 *
	 * @param      $pkInfo information needed to generate a key
	 */
	public function setPrimaryKeyMethodInfo($pkInfo)
	{
		$this->pkInfo = $pkInfo;
	}

  // Deprecated methods and attributres, to be removed
	//---Utility methods for doing intelligent lookup of table names

	/** 
	 * The prefix on the table name. 
	 * @deprecated Not used anywhere in Propel
	 */
	private $prefix;

	/**
	 * Get table prefix name.
	 *
	 * @deprecated Not used anywhere in Propel
	 * @return     string A String with the prefix.
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * Set table prefix name.
	 *
	 * @deprecated Not used anywhere in Propel
	 * @param      string $prefix The prefix for the table name (ie: SCARAB for
	 * SCARAB_PROJECT).
	 * @return     void
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}
	
	/**
	 * Tell me if i have PREFIX in my string.
	 *
	 * @deprecated Not used anywhere in Propel
	 * @param      data A String.
	 * @return     boolean True if prefix is contained in data.
	 */
	protected function hasPrefix($data)
	{
		return (strpos($data, $this->prefix) === 0);
	}

	/**
	 * Removes the PREFIX if found
	 *
	 * @deprecated Not used anywhere in Propel
	 * @param      string $data A String.
	 * @return     string A String with data, but with prefix removed.
	 */
	protected function removePrefix($data)
	{
		return $this->hasPrefix($data) ? substr($data, strlen($this->prefix)) : $data;
	}

	/**
	 * Removes the PREFIX, removes the underscores and makes
	 * first letter caps.
	 *
	 * SCARAB_FOO_BAR becomes FooBar.
	 *
	 * @deprecated Not used anywhere in Propel. At buildtime, use Column::generatePhpName() for that purpose
	 * @param      data A String.
	 * @return     string A String with data processed.
	 */
	public final function removeUnderScores($data)
	{
		$out = '';
    $tmp = $this->removePrefix($data);
		$tok = strtok($tmp, '_');
		while ($tok) {
			$out .= ucfirst($tok);
			$tok = strtok('_');
		}
		return $out;
	}

	/**
	 * Makes the first letter caps and the rest lowercase.
	 *
	 * @deprecated Not used anywhere in Propel.
	 * @param      string $data A String.
	 * @return     string A String with data processed.
	 */
	private function firstLetterCaps($data)
	{
		return(ucfirst(strtolower($data)));
	}
}
