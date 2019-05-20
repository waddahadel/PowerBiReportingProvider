<?php

namespace QU\PowerBiReportingProvider\CaptureRoutines;

use QU\LERQ\API\DataCaptureRoutinesInterface;
use QU\LERQ\Model\EventModel;

/**
 * Class Routines
 * @author Ingmar Szmais <iszmais@databay.de>
 * @since 25.04.19
 */
class Routines implements DataCaptureRoutinesInterface
{
	/**
	 * @inheritDoc
	 */
	public function getOverrides(): array
	{
		return [
			'collectUserData' => false,
			'collectUDFData' => false,
			'collectMemberData' => false,
			'collectLpPeriod' => false,
			'collectObjectData' => false,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function collectLpPeriod(EventModel $event): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function collectUDFData(EventModel $event): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function collectUserData(EventModel $event): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function collectMemberData(EventModel $event): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function collectObjectData(EventModel $event): array
	{
		return [];
	}
}