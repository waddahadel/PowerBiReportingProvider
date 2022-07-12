<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\APIEndpoint;

use QU\LERQ\API\API;
use QU\LERQ\API\Filter\FilterObject;
use QU\LERQ\Model\QueueModel;

/**
 * Class Report
 * @author Ingmar Szmais <iszmais@databay.de>
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class Report
{

	/**
	 * @param array $params
	 * @return array
	 */
	public function getFilteredEvents(array $params)
	{
		global $DIC;
		if(!isset($DIC['autoload.lc.lcautoloader'])){
			\ilPluginAdmin::getPluginObject(
				'Services',
				'Cron',
				'crnhk',
				'LpEventReportQueue'
			);
		}

		/** @var \QU\LERQ\API\API $API */
		$API = $DIC['qu.lerq.api'];

		$filter = $this->createFilterObject($API, $params);

		/** @var \QU\LERQ\Model\QueueModel $value */
		foreach ($API->getCollection($filter) as $value) {
			$value->setCourseStart($this->convertToISO8601($value->getCourseStart()));
			$value->setCourseEnd($this->convertToISO8601($value->getCourseEnd()));
			$data[] = $value;
		}
		return [
			'data' => $data,
		];
	}

	/**
	 * @param API $API
	 * @param array $params
	 * @return FilterObject
	 */
	private function createFilterObject(API $API, array $params): FilterObject
	{
		/** @var FilterObject $filter */
		$filter = $API->createFilterObject();
		if(isset($params['event_before'])){
			$filter->setEventHappenedStart($this->convertFromISO8601($params['event_before']));
		}
		if(isset($params['event_after'])){
			$filter->setEventHappenedEnd($this->convertFromISO8601($params['event_after']));
		}
		if(isset($params['course_before'])){
			$filter->setCourseEnd($this->convertFromISO8601($params['course_before']));
		}
		if(isset($params['course_after'])){
			$filter->setCourseStart($this->convertFromISO8601($params['course_after']));
		}
		if(isset($params['excluded_progress'])){
			$filter->setExcludedProgress($params['excluded_progress']);
		}
		if(isset($params['progress'])){
			$filter->setProgress($params['progress']);
		}
		if(isset($params['trigger'])){
			$filter->setEvent($params['trigger']);
		}
		if(isset($params['assignment'])){
			$filter->setAssignment($params['assignment']);
		}
		if(isset($params['start'])){
			$filter->setPageStart($params['start']);
		}
		if(isset($params['limit'])){
			$filter->setPageLength($params['limit']);
		}
		if(isset($params['negative_pager'])){
			$filter->setNegativePager($params['negative_pager']);
		}
		return $filter;
	}

	/**
	 * @param string $iso
	 * @return int
	 * @throws Exception
	 */
	private function convertFromISO8601(string $iso){
		$dateTime = new \DateTime($iso);
		return $dateTime->getTimestamp();
	}

	/**
	 * @param $timestamp
	 * @return string|null
	 * @throws Exception
	 */
	private function convertToISO8601($timestamp){
		if($timestamp === null){
			return null;
		}
		$b = new \Datetime();
		$b->setTimestamp($timestamp);
		$b->setTimezone(new \DateTimeZone('UTC'));
		return $b->format('c');
	}

}
