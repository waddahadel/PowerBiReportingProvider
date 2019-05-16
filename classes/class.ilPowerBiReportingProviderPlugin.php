<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once("./Services/Cron/classes/class.ilCronHookPlugin.php");

use QU\PowerBiReportingProvider\Lock\PidBasedLocker;
use QU\PowerBiReportingProvider\Logging\Log;
use QU\PowerBiReportingProvider\Logging\Settings as LogSettings;
use QU\PowerBiReportingProvider\Logging\Writer\Ilias;
use QU\PowerBiReportingProvider\Logging\Writer\Gilo;
use QU\PowerBiReportingProvider\Logging\Writer\StdOut;
use QU\PowerBiReportingProvider\Task\ReportingProvider;

class ilPowerBiReportingProviderPlugin extends \ilCronHookPlugin
{
	const PLUGIN_ID = "powbi_rep_prov";
	const PLUGIN_NAME = "PowerBiReportingProvider";
	const PLUGIN_SETTINGS = "qu_crnhk_powbi_rep_prov";
	const PLUGIN_NS = 'QU\PowerBiReportingProvider';

	/** @var ilPowerBiReportingProviderPlugin */
	protected static $instance;

	/** @var \ilSetting */
	protected $settings;

	/** @var array */
	protected $jobs;

	/**
	 * @return void
	 */
	protected function init()
	{
		self::registerAutoloader();

		global $DIC;

		if(!isset($DIC['plugin.powbi.export.logger.writer.ilias'])) {
			$GLOBALS['DIC']['plugin.powbi.export.logger.writer.ilias'] = function (Pimple\Container $c) {
				$logLevel = \ilLoggingDBSettings::getInstance()->getLevel();

				return new Ilias($c['ilLog'], $logLevel);
			};
		}

		if(!isset($DIC['plugin.powbi.export.cronjob.logger'])) {
			$GLOBALS['DIC']['plugin.powbi.export.cronjob.logger'] = function (Pimple\Container $c) {
//			global $DIC;
				$logger = new Log();

				$logger->addWriter(new StdOut());
				$logger->addWriter($c['plugin.powbi.export.logger.writer.ilias']);

				$tempDirectory = \ilUtil::ilTempnam();
				\ilUtil::makeDir($tempDirectory);
//			$DIC->filesystem()->temp()->createDir($tempDirectory);
				$now = new \DateTimeImmutable();
				$settings = new LogSettings($tempDirectory, 'powbi_rep_prov_' . $now->format('Y_m_d_H_i_s') . '.log');
				$logger->addWriter(new Gilo($settings));

				return $logger;
			};
		}

		if(!isset($DIC['plugin.powbi.export.web.logger'])) {
			$GLOBALS['DIC']['plugin.powbi.export.web.logger'] = function (Pimple\Container $c) {
				$logger = new Log();

				$logger->addWriter($c['plugin.powbi.export.logger.writer.ilias']);

				return $logger;
			};
		}

		if(!isset($DIC['plugin.powbi.cronjob.locker'])) {
			$GLOBALS['DIC']['plugin.powbi.cronjob.locker'] = function (Pimple\Container $c) {
				return new PidBasedLocker(
					new \ilSetting($this->getPluginName()),
					$c['plugin.powbi.export.cronjob.logger']
				);
			};
		}

		$this->jobs = $this->getCronJobInstances();
	}

	/**
	 * @return ilPowerBiReportingProviderPlugin
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return void
	 */
	public static function registerAutoloader()
	{
		global $DIC;

		if(!isset($DIC['autoload.lc.lcautoloader'])) {
			require_once(realpath(dirname(__FILE__)) . '/Autoload/LCAutoloader.php');
			$Autoloader = new LCAutoloader();
			$Autoloader->register();
			$Autoloader->addNamespace('ILIAS\Plugin', '/Customizing/global/plugins');
			$DIC['autoload.lc.lcautoloader'] = $Autoloader;
		}
		$DIC['autoload.lc.lcautoloader']->addNamespace(self::PLUGIN_NS, realpath(dirname(__FILE__)));
	}

	/**
	 * ilPowerBiReportingProviderPlugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
		$this->settings = new ilSetting(self::PLUGIN_SETTINGS);
	}

	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}

	/**
	 * @return \ilSetting
	 */
	public function getSettings(): ilSetting
	{
		return $this->settings;
	}

	/**
	 * @return array
	 */
	function getCronJobInstances()
	{
		// get array with all jobs
		$this->jobs = [];
		$job = new ReportingProvider(
			$this,
			$GLOBALS['DIC']['plugin.powbi.cronjob.locker'],
			$GLOBALS['DIC']['plugin.powbi.export.cronjob.logger'],
			new ilSetting()
		);
		$this->jobs[$job->getId()] = $job;
		return $this->jobs;
	}

	/**
	 * @param $a_job_id
	 * @return mixed
	 * @throws Exception
	 */
	function getCronJobInstance($a_job_id)
	{
		// get specific job by id
		if(array_key_exists($a_job_id, $this->jobs)) {
			return $this->jobs[$a_job_id];
		}
		\ilUtil::sendFailure('ERROR: Unknown job called: ' . $a_job_id, true);
		return [];
	}

	/**
	 * @return void
	 */
	protected function afterActivation() {
		global $DIC;
		self::registerAutoloader();
		// check if api is initialized
		if(!isset($DIC['qu.lerq.api'])) {
			\ilPluginAdmin::getPluginObject(
				"Services",
				"Cron",
				"crnhk",
				"LpEventReportQueue"
			);
			if(!isset($DIC['qu.lerq.api'])) {
				$DIC->logger()->root()->error('Could not init LpEventReportQueue API');
				return;
			}
		}
		// register provider
		/** @var \QU\LERQ\API\API */
		$DIC['qu.lerq.api']->registerProvider(
			self::PLUGIN_NAME,
			self::PLUGIN_NS,
			realpath(dirname(__FILE__)),
			false
		);
	}

	/**
	 * @return void
	 */
	protected function afterDeactivation() {

		parent::afterDeactivation();

		global $DIC;

		if(!isset($DIC['autoload.lc.lcautoloader'])){
			\ilPluginAdmin::getPluginObject(
				'Services',
				'Cron',
				'crnhk',
				'LpEventReportQueue'
			);
		}

		// unregister provider
		/** @var \QU\LERQ\API\API */
		if(isset($DIC['qu.lerq.api'])){
			$DIC['qu.lerq.api']->unregisterProvider(
				self::PLUGIN_NAME,
				self::PLUGIN_NS
			);
		}
	}

	protected function beforeUninstall() {

		return parent::deactivate();
	}

}