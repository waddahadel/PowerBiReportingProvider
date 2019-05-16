<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\DataObjects;

/**
 * Class ProviderIndex
 * @package QU\PowerBiReportingProvider\DataObjects
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class ProviderIndex extends DataObject
{
	/** @inheritdoc  */
	protected $use_table = 'powbi_prov_index';
	/** @inheritdoc */
	protected $use_index = 'id';

	/** @var int */
	private $id;
	/** @var int */
	private $processed;
	/** @var string */
	private $trigger;
	/** @var int */
	private $timestamp;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getProcessed(): int
	{
		return $this->processed;
	}

	/**
	 * @param int $processed
	 * @return ProviderIndex
	 */
	public function setProcessed(int $processed): ProviderIndex
	{
		$this->processed = $processed;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTrigger(): string
	{
		return $this->trigger;
	}

	/**
	 * @param string $trigger
	 * @return ProviderIndex
	 */
	public function setTrigger(string $trigger): ProviderIndex
	{
		$this->trigger = $trigger;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getTimestamp(): int
	{
		return $this->timestamp;
	}

	/**
	 * @param int $timestamp
	 * @return ProviderIndex
	 */
	public function setTimestamp(int $timestamp): ProviderIndex
	{
		$this->timestamp = $timestamp;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function load(int $id = null): bool
	{
		if (isset($id)) {
			$data = $this->_loadById($id);
		} else {
			$data = $this->_load();
			$data = end($data);
		}
		if (!empty($data)) {
			$this->id = $data['id'];
			$this->setProcessed($data['processed']);
			$this->setTrigger($data['trigger']);
			$this->setTimestamp($data['timestamp']);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function save(): bool
	{
		$fields = [
			'processed',
			'trigger',
			'timestamp',
			$this->use_index
		];
		$types = [
			'integer',
			'string',
			'integer',
			'integer',
		];
		$values = [
			$this->getProcessed(),
			$this->getTrigger(),
			$this->getTimestamp(),
			$this->getNextId(),
		];

		return $this->_create($fields, $types, $values);
	}

	/**
	 * @inheritDoc
	 */
	public function remove(): bool
	{
		return false;
	}

}