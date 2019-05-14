<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\Lock;

/**
 * Interface Locker
 * @package QU\PowerBiReportingProvider\Lock
 */
interface Locker
{
	/**
	 * @return bool
	 */

	public function acquireLock();

	/**
	 * @return bool
	 */
	public function isLocked();

	/**
	 * @return void
	 */
	public function releaseLock();
}
