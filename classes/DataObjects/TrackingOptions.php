<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\DataObjects;

use QU\PowerBiReportingProvider\DataObjects\TrackingOption;

/**
 * Class TrackingOptions
 * @package QU\PowerBiReportingProvider\DataObjects
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class TrackingOptions
{
	/** @var TrackingOption */
	private $track_id;
	/** @var TrackingOption */
	private $track_timestamp;
	/** @var TrackingOption */
	private $track_trigger;
	/** @var TrackingOption */
	private $track_progress;
	/** @var TrackingOption */
	private $track_assignment;
	/** @var TrackingOption */
	private $track_obj_type;
	/** @var TrackingOption */
	private $track_obj_title;
	/** @var TrackingOption */
	private $track_refid;
	/** @var TrackingOption */
	private $track_link;
	/** @var TrackingOption */
	private $track_parent_title;
	/** @var TrackingOption */
	private $track_parent_refid;
	/** @var TrackingOption */
	private $track_user_mail;
	/** @var TrackingOption */
	private $track_user_id;
	/** @var TrackingOption */
	private $track_user_login;

	/**
	 * @return TrackingOption
	 */
	public function getTrackId(): TrackingOption
	{
		return $this->track_id;
	}

	/**
	 * @param TrackingOption $track_id
	 * @return TrackingOptions
	 */
	public function setTrackId(TrackingOption $track_id): TrackingOptions
	{
		$this->track_id = $track_id;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackTimestamp(): TrackingOption
	{
		return $this->track_timestamp;
	}

	/**
	 * @param TrackingOption $track_timestamp
	 * @return TrackingOptions
	 */
	public function setTrackTimestamp(TrackingOption $track_timestamp): TrackingOptions
	{
		$this->track_timestamp = $track_timestamp;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackTrigger(): TrackingOption
	{
		return $this->track_trigger;
	}

	/**
	 * @param TrackingOption $track_trigger
	 * @return TrackingOptions
	 */
	public function setTrackTrigger(TrackingOption $track_trigger): TrackingOptions
	{
		$this->track_trigger = $track_trigger;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackProgress(): TrackingOption
	{
		return $this->track_progress;
	}

	/**
	 * @param TrackingOption $track_progress
	 * @return TrackingOptions
	 */
	public function setTrackProgress(TrackingOption $track_progress): TrackingOptions
	{
		$this->track_progress = $track_progress;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackAssignment(): TrackingOption
	{
		return $this->track_assignment;
	}

	/**
	 * @param TrackingOption $track_assignment
	 * @return TrackingOptions
	 */
	public function setTrackAssignment(TrackingOption $track_assignment): TrackingOptions
	{
		$this->track_assignment = $track_assignment;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackObjType(): TrackingOption
	{
		return $this->track_obj_type;
	}

	/**
	 * @param TrackingOption $track_obj_type
	 * @return TrackingOptions
	 */
	public function setTrackObjType(TrackingOption $track_obj_type): TrackingOptions
	{
		$this->track_obj_type = $track_obj_type;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackObjTitle(): TrackingOption
	{
		return $this->track_obj_title;
	}

	/**
	 * @param TrackingOption $track_obj_title
	 * @return TrackingOptions
	 */
	public function setTrackObjTitle(TrackingOption $track_obj_title): TrackingOptions
	{
		$this->track_obj_title = $track_obj_title;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackRefid(): TrackingOption
	{
		return $this->track_refid;
	}

	/**
	 * @param TrackingOption $track_refid
	 * @return TrackingOptions
	 */
	public function setTrackRefid(TrackingOption $track_refid): TrackingOptions
	{
		$this->track_refid = $track_refid;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackLink(): TrackingOption
	{
		return $this->track_link;
	}

	/**
	 * @param TrackingOption $track_link
	 * @return TrackingOptions
	 */
	public function setTrackLink(TrackingOption $track_link): TrackingOptions
	{
		$this->track_link = $track_link;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackParentTitle(): TrackingOption
	{
		return $this->track_parent_title;
	}

	/**
	 * @param TrackingOption $track_parent_title
	 * @return TrackingOptions
	 */
	public function setTrackParentTitle(TrackingOption $track_parent_title): TrackingOptions
	{
		$this->track_parent_title = $track_parent_title;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackParentRefid(): TrackingOption
	{
		return $this->track_parent_refid;
	}

	/**
	 * @param TrackingOption $track_parent_refid
	 * @return TrackingOptions
	 */
	public function setTrackParentRefid(TrackingOption $track_parent_refid): TrackingOptions
	{
		$this->track_parent_refid = $track_parent_refid;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackUserMail(): TrackingOption
	{
		return $this->track_user_mail;
	}

	/**
	 * @param TrackingOption $track_user_mail
	 * @return TrackingOptions
	 */
	public function setTrackUserMail(TrackingOption $track_user_mail): TrackingOptions
	{
		$this->track_user_mail = $track_user_mail;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackUserId(): TrackingOption
	{
		return $this->track_user_id;
	}

	/**
	 * @param TrackingOption $track_userid
	 * @return TrackingOptions
	 */
	public function setTrackUserId(TrackingOption $track_userid): TrackingOptions
	{
		$this->track_user_id = $track_userid;
		return $this;
	}

	/**
	 * @return TrackingOption
	 */
	public function getTrackUserLogin(): TrackingOption
	{
		return $this->track_user_login;
	}

	/**
	 * @param TrackingOption $track_user_login
	 * @return TrackingOptions
	 */
	public function setTrackUserLogin(TrackingOption $track_user_login): TrackingOptions
	{
		$this->track_user_login = $track_user_login;
		return $this;
	}

	/**
	 * @param string $keyword
	 * @return \QU\PowerBiReportingProvider\DataObjects\TrackingOption|null
	 */
	public function getOptionByKeyword(string $keyword): TrackingOption
	{
		$func_name = $this->getGetterByKeyword($keyword);
		if (method_exists($this, $func_name)) {
			return $this->{$func_name}();
		}
		return null;
	}

	/**
	 * @return bool
	 */
	public function load(): bool
	{
		global $DIC;
		/** @var \QU\PowerBiReportingProvider\Logging\Log $logger */
		$logger = $DIC['plugin.powbi.export.cronjob.logger'];

		$load_status = true;

		$available = $this->getAvailableOptions();
		$options = $this->_load();
		if (!empty($options)) {
			foreach ($options as $option) {
				if (in_array($option['keyword'], $available)) {
					$tOption = new TrackingOption();

					$func_name = $this->getSetterByKeyword($option['keyword']);
					try {
						if (!$tOption->load($option['id'])) {
							$tOption->setId($option['id'])
								->setKeyword($option['keyword'])
								->setActive(($option['active'] == true))
								->setFieldName($option['field_name'])
								->setUpdatedAt($option['updated_at']);
						}
						$logger->debug('loaded option for ' . $option['keyword']);
						$this->{$func_name}($tOption);

					} catch (\Exception $e) {
						$load_status = false;
						$logger->warn('failure while loading option for ' . $option['keyword'] .
							': ' . $e->getMessage());
					}
				}
			}
		} else {
			$load_status = false;
		}
		return $load_status;
	}

	/**
	 * Get available option keywords
	 *
	 * @return array
	 */
	public function getAvailableOptions(): array
	{
		return [
			'id',
			'timestamp',
			'trigger',
			'progress',
			'assignment',
			'obj_type',
			'obj_title',
			'refid',
			'link',
			'parent_title',
			'parent_refid',
			'user_mail',
			'user_id',
			'user_login',
		];
	}

	/**
	 * @return array
	 */
	private function _load(): array
	{
		global $DIC;

		$select = 'SELECT * FROM `powbi_prov_options`;';

		$result = $DIC->database()->query($select);

		$res = $DIC->database()->fetchAll($result);
		return $res;
	}

	/**
	 * @param string $keyword
	 * @return string
	 */
	private function getSetterByKeyword(string $keyword): string
	{
		$rn = explode('_', $keyword);
		$func_name = 'setTrack';
		if (count($rn) > 1) {
			foreach ($rn as $rn_part) {
				$func_name .= ucfirst($rn_part);
			}
		} else {
			$func_name .= ucfirst($rn[0]);
		}
		return $func_name;
	}

	/**
	 * @param string $keyword
	 * @return string
	 */
	private function getGetterByKeyword(string $keyword): string
	{
		$rn = explode('_', $keyword);
		$func_name = 'getTrack';
		if (count($rn) > 1) {
			foreach ($rn as $rn_part) {
				$func_name .= ucfirst($rn_part);
			}
		} else {
			$func_name .= ucfirst($rn[0]);
		}
		return $func_name;
	}

}