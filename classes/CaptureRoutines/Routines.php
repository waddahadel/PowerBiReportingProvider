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
	
	public function collectLpPeriod(EventModel $event): array
	{
		return [];
	}

	public function collectUDFData(EventModel $event): array
	{
		return [];
	}

	public function collectUserData(EventModel $event): array
	{
		return [];
	}
	
	public function collectMemberData(EventModel $event): array
	{
		return [];
	}
	
	public function collectObjectData(EventModel $event): array
	{
		return [];
	}
}