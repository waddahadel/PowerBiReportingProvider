<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\FileWriter;

/**
 * Class CsvWriter
 * @package QU\PowerBiReportingProvider\FileWriter
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class CsvWriter
{
	/** @var string  */
	private $file_path;
	/** @var resource  */
	private $buffer;
	/** @var array  */
	private $fields;
	/** @var array  */
	private $data;

	/**
	 * CsvWriter constructor.
	 * @param string $file_path
	 */
	public function __construct(string $file_path)
	{
		$this->file_path = $file_path;
		$this->buffer = null;
		$this->fields = [];
		$this->data = [];
	}

	/**
	 * @param array $fields
	 * @return $this
	 * @throws \Exception
	 */
	public function setFields(array $fields)
	{
		if (!empty($this->fields)) {
			throw new \Exception('CSV fields are already set.');
		}
		$this->fields = $fields;
		return $this;
	}

	/**
	 * @param array $data
	 * @return $this
	 * @throws \Exception
	 */
	public function setData(array $data)
	{
		$this->data = [];
		foreach ($data as $row) {
			if (!is_array($row)) {
				throw new \Exception('Given data is invalid. Expected array, got ' . gettype($row) . '.');
			}
			if ( ($rcount = count($row)) != ($fcount = count($this->fields)) ) {
				throw new \Exception('Given data is invalid. Expected ' . $fcount . ' fields but got ' . $rcount . '.');
			}
			$this->data[] = $row;
		}
		return $this;
	}

	/**
	 * @param array $row
	 * @return $this
	 * @throws \Exception
	 */
	public function addRow(array $row)
	{
		if ( ($rcount = count($row)) != ($fcount = count($this->fields)) ) {
			throw new \Exception('Given data is invalid. Expected ' . $fcount . ' fields but got ' . $rcount . '.');
		}
		$this->data[] = $row;
		return $this;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function writeCsv()
	{
		if ($this->file_path === '') {
			throw new \Exception('No file path given.');
		}
		if (empty($this->fields)) {
			throw new \Exception('No fields defined.');
		}
		if (empty($this->data)) {
			throw new \Exception('Cannot write empty data.');
		}

		$this->openFile();
		$this->writeRow($this->fields);
		foreach ($this->data as $row) {
			if(false === $this->writeRow($row)) {
				$this->closeFile();
				throw new \Exception('Could not write all data. Stopped process.');
			}
		}

		return $this->closeFile();
	}

	/**
	 * @param array $row
	 * @return bool|int
	 */
	private function writeRow(array $row)
	{
		return fputcsv($this->buffer, $row, chr(124));
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	private function openFile()
	{
		$fnpos = strrpos($this->file_path, chr(47));
		$dir_path = substr($this->file_path, 0, $fnpos);
		$filename = substr($this->file_path, ($fnpos + 1));

		if (file_exists($this->file_path)) {
			throw new \Exception('File (' . $filename . ') already exists at ' . $dir_path . '.');
		}
		if (!is_dir($dir_path)) {
			if (false === mkdir($dir_path, 0755, true)) {
				throw new \Exception('Directory (' . $dir_path . ') does not exist and cannot be created.');
			}
		}

		$this->buffer = @fopen($this->file_path, 'x');
		if (!is_resource($this->buffer) || $this->buffer === false) {
			throw new \Exception('Cannot create file for writing.');
		}
	}

	/**
	 * @return bool
	 */
	private function closeFile()
	{
		return @fclose($this->buffer);
	}
}