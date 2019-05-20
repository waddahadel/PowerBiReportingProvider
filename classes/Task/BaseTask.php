<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\Task;

require_once 'Services/Cron/classes/class.ilCronJob.php';

/**
 * Class BaseTask
 * @package QU\PowerBiReportingProvider\Task
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
abstract class BaseTask extends \ilCronJob
{
	/**
	 * @inheritdoc
	 */
	public function hasAutoActivation()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function hasFlexibleSchedule()
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultScheduleType()
	{
		return self::SCHEDULE_TYPE_DAILY;
	}

	/**
	 * @inheritdoc
	 */
	public function getDefaultScheduleValue()
	{
		return 1;
	}

	/**
	 * @inheritdoc
	 */
	public function isManuallyExecutable()
	{
		if(defined('DEVMODE') && DEVMODE) {
			return true;
		}

		return false;
	}
}