<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\Logging\Writer;

use QU\PowerBiReportingProvider\Logging\Settings as LogSettings;
use QU\PowerBiReportingProvider\Logging\Logger;
use QU\PowerBiReportingProvider\Logging\TraceProcessor;

/**
 * Class Gilo
 * @package QU\PowerBiReportingProvider\Logging\Writer
 */
class Gilo extends Base
{
	
	/** @var null|\ilGenericInterfaceLogOverviewPlugin */
	protected $logOverviewPlugin = null;

	/** @var bool */
	protected $shutDownHandled = false;

	/** @var array */
	protected $loggedPriorities = [];

	/** @var int */
	protected $succeededDataSets = 0;

	/** @var int */
	protected $startTs = 0;

	/** @var string */
	protected $filename = '';

	/** @var \ilLogger */
	protected $aggregatedLogger;

	/** @param LogSettings $settings */
	public function __construct(LogSettings $settings)
	{
		foreach ($GLOBALS['ilPluginAdmin']->getActivePluginsForSlot(
			IL_COMP_SERVICE, 'UIComponent', 'uihk'
		) as $plugin_name) {
			$plugin = \ilPluginAdmin::getPluginObject(
				IL_COMP_SERVICE, 'UIComponent', 'uihk',
				$plugin_name
			);

			if (class_exists('\ilGenericInterfaceLogOverviewPlugin') && $plugin instanceof \ilGenericInterfaceLogOverviewPlugin) {

				$factory = \ilLoggerFactory::newInstance($settings);
				$this->aggregatedLogger = $factory->getComponentLogger('PowerBiReportingProvider');
				$this->aggregatedLogger->getLogger()->popProcessor();
				$this->aggregatedLogger->getLogger()->pushProcessor(new TraceProcessor(\ilLogLevel::DEBUG));

				$this->filename = $settings->getLogDir() . DIRECTORY_SEPARATOR . $settings->getLogFile();

				$this->logOverviewPlugin = $plugin;
				break;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function doWrite(array $message)
	{
		if ($this->logOverviewPlugin === null) {
			return;
		}

		if ($this->startTs == 0) {
			$this->startTs = time();
		}

		if (isset($message['extra']) && isset($message['extra']['exported_data_sets'])) {
			$this->succeededDataSets += $message['extra']['exported_data_sets'];
		}

		if (isset($message['priority'])) {
			if (!isset($this->loggedPriorities[$message['priority']])) {
				$this->loggedPriorities[$message['priority']] = 1;
			} else {
				++$this->loggedPriorities[$message['priority']];
			}
		}

		$line = $message['message'];

		switch ($message['priority']) {
			case Logger::EMERG:
				$method = 'emergency';
				break;

			case Logger::ALERT:
				$method = 'alert';
				break;

			case Logger::CRIT:
				$method = 'critical';
				break;

			case Logger::ERR:
				$method = 'error';
				break;

			case Logger::WARN:
				$method = 'warning';
				break;

			case Logger::INFO:
				$method = 'info';
				break;

			case Logger::NOTICE:
				$method = 'notice';
				break;

			case Logger::DEBUG:
			default:
				$method = 'debug';
				break;
		}

		$this->aggregatedLogger->{$method}($line);
	}

	/** @inheritdoc */
	public function shutdown()
	{
		if (!$this->shutDownHandled && $this->logOverviewPlugin !== null) {
			$this->logOverviewPlugin->getReportingData(
				$this->filename,
				(int)$this->loggedPriorities[Logger::ERR] + (int)$this->loggedPriorities[Logger::CRIT] +
				(int)$this->loggedPriorities[Logger::ALERT] + (int)$this->loggedPriorities[Logger::EMERG],
				(int)$this->loggedPriorities[Logger::WARN],
				$this->succeededDataSets,
				$this->startTs > 0 ? time() - $this->startTs : 0,
				$this->getHighestLoggedSeverity()
			);
		}

		unset($this->aggregated_logger);

		$this->shutDownHandled = true;
	}

	/**
	 * @return int
	 */
	protected function getHighestLoggedSeverity()
	{
		foreach([
					Logger::EMERG,
					Logger::ALERT,
					Logger::CRIT,
					Logger::ERR,
					Logger::WARN,
					Logger::NOTICE,
					Logger::INFO,
					Logger::DEBUG
				] as $severity) {
			if (isset($this->loggedPriorities[$severity]) && $this->loggedPriorities[$severity] > 0) {
				return $severity;
			}
		}

		return PHP_INT_MAX;
	}
}