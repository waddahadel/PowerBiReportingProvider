<?php

namespace QU\PowerBiReportingProvider\DataObjects;

/**
 * Class DataObject
 * @package QU\PowerBiReportingProvider\DataObjects
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
abstract class DataObject implements DataObjectInterface
{
	/**
	 * Table name for DataObject
	 *
	 * You MUST set this parameter inside your
	 * DataObject to define the table name.
	 *
	 * @var string
	 */
	protected $use_table;

	/**
	 * ID field in database table
	 *
	 * This is used as index.
	 * You MUST set this parameter inside your
	 * DataObject to define the table index field.
	 *
	 * @var string
	 */
	protected $use_index;

	/** @var \ilDBInterface  */
	private $database;

	/**
	 * DataObject constructor.
	 * 
	 * If you override this function, you SHOULD use
	 * the parent::__construct at the beginning of
	 * your own constructor.
	 */
	public function __construct()
	{
		global $DIC;
		$this->database = $DIC->database();
	}

	/**
	 * Get next sequence id
	 *
	 * @return int
	 */
	final protected function getNextId(): int
	{
		return $this->database->nextId($this->use_table);
	}

	/**
	 * Load all entries from database
	 *
	 * This is not recommended. You should use _loadById() instead.
	 *
	 * @return array			Array with database values like
	 * 							[ field_name => field_value ]
	 */
	final protected function _load(): array
	{
		$select = 'SELECT * FROM `' . $this->use_table . '`;';

		$result = $this->database->query($select);

		$res = $this->database->fetchAll($result);
		return $res;
	}

	/**
	 * Load a specific entry be its ID
	 *
	 * This is the recommended function to load the data
	 * into your object. Just use this function inside 
	 * your objects __construct() and assign the returned
	 * data to your objects parameters.
	 *
	 * @param int $id			Entry ID from $use_index field
	 * @return array			Array with database values like
	 * 							[ field_name => field_value ]
	 */
	final protected function _loadById(int $id): array
	{
		$select = 'SELECT * FROM `' . $this->use_table . '` WHERE ' . $this->use_index . ' = ' .
			$this->database->quote($id, 'integer');

		$result = $this->database->query($select);

		$res = $this->database->fetchAll($result);
		return $res[0];
	}

	/**
	 * Create a new entry in database
	 *
	 * @param array $fields		Array of fields
	 * @param array $types		Array of field types
	 * @param array $values		Array of values to save
	 * @return bool
	 */
	final protected function _create(array $fields, array $types, array $values)
	{
		$query = 'INSERT INTO `' . $this->use_table . '` ';
		$query .= '(`' . implode('`, `', $fields) . '`) ';
		$query .= 'VALUES (' . implode(', ', array_fill(0, count($fields), '%s')) . ') ';

		$res = $this->database->manipulateF(
			$query,
			$types,
			$values
		);

		return ($res === false);
	}

	/**
	 * Update an entry in database
	 *
	 * @param array $fields		Array of fields
	 * @param array $types		Array of field types
	 * @param array $values		Array of values to save
	 * @param int $whereIndex	Entry ID from $use_index field
	 * @return bool
	 */
	final protected function _update(array $fields, array $types, array $values, int $whereIndex)
	{
		$query = 'UPDATE `' . $this->use_table . '` SET ';
		$query .= implode(' = %s,', $fields) . ' = %s ';
		$query .= 'WHERE ' . $this->use_index . ' = ' . $this->database->quote($whereIndex, 'integer') . ';';

		$res = $this->database->manipulateF(
			$query,
			$types,
			$values
		);

		return ($res === false);
	}

	/**
	 * Delete an entry from database
	 *
	 * @param int $whereIndex	Entry ID from $use_index field
	 * @return bool
	 */
	final protected function _delete(int $whereIndex)
	{

		$query = 'DELETE FROM `' . $this->use_table . '` WHERE ' . $this->use_index . ' = ' .
			$this->database->quote($whereIndex, 'text') . ';';

		$res = $this->database->manipulate($query);

		return ($res === false);
	}
}