<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\Lock;

use QU\PowerBiReportingProvider\Logging\Logger;

/**
 * Class PidBasedLocker
 * @package QU\PowerBiReportingProvider\Lock
 */
class PidBasedLocker implements Locker
{
	/**
	 * @var \ilSetting
	 */
	protected $settings;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * @param \ilSetting $settings
	 * @param Logger     $logger
	 */
	public function __construct(\ilSetting $settings, Logger $logger)
	{
		$this->settings = $settings;
		$this->logger   = $logger;
	}

	/**
	 * @param string $pid
	 * @return bool
	 */
	protected function isRunning($pid)
	{
		try {
			$result = \shell_exec(\sprintf("ps %d", $pid));
			if (\count(\preg_split("/\n/", $result)) > 2) {
				return true;
			}
		} catch (\Exception $e) {
			$this->logger->err("Can\'t determine locking state: " . $e->getMessage());
		}

		return false;
	}

	/**
	 *
	 */
	protected function writeLockedState()
	{
		$this->settings->set('cron_lock_status', 1);
		$this->settings->set('cron_lock_ts', time());
		$this->settings->set('cron_lock_pid', getmypid());
	}

	/**
	 * @inheritdoc
	 */
	public function acquireLock()
	{
		if (!$this->settings->get('cron_lock_status', 0)) {
			$this->writeLockedState();
			return true;
		}

		$pid = $this->settings->get('cron_lock_pid', null);
		if ($pid && $this->isRunning($pid)) {
			$lastLockTimestamp = $this->settings->get('cron_lock_ts', time());
			if ($lastLockTimestamp > time() - (60 * 60 * 1)) {
				return false;
			}
		}

		$this->writeLockedState();
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function isLocked()
	{
		return (bool)$this->settings->get('cron_lock_status', 0);
	}

	/**
	 * @inheritdoc
	 */
	public function releaseLock()
	{
		$this->settings->set('cron_lock_status', 0);
		$this->settings->set('cron_lock_ts', null);
		$this->settings->set('cron_lock_pid', null);
	}
}
