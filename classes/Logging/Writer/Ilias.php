<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\PowerBiReportingProvider\Logging\Writer;

use QU\PowerBiReportingProvider\Logging;

require_once 'Services/Calendar/classes/class.ilDateTime.php';

/**
 * Class ILIAS
 * @author Michael Jansen <mjansen@databay.de>
 */
class Ilias extends Base
{
	/** @var \ilLogger */
	protected $aggregatedLogger;

	/** @var string */
	private $logLevel;

	/** @var Logging\TraceProcessor */
	protected $processor;

	/**
	 * @var bool
	 */
	protected $shutdown_handled = false;

	/**
	 * ILIAS constructor.
	 * @param \ilLogger $log
	 * @param $logLevel
	 */
	public function __construct(\ilLogger $log, $logLevel)
	{
		$this->aggregatedLogger = $log;
		$this->logLevel = $logLevel;

		$this->processor = new Logging\TraceProcessor(\ilLogLevel::DEBUG);
	}

	/**
	 * @param array $message
	 * @return void
	 */
	protected function doWrite(array $message)
	{
		$line = $message['message'];

		switch ($message['priority']) {
			case Logging\Logger::EMERG:
				$method = 'emergency';
				break;

			case Logging\Logger::ALERT:
				$method = 'alert';
				break;

			case Logging\Logger::CRIT:
				$method = 'critical';
				break;

			case Logging\Logger::ERR:
				$method = 'error';
				break;

			case Logging\Logger::WARN:
				$method = 'warning';
				break;

			case Logging\Logger::INFO:
				$method = 'info';
				break;

			case Logging\Logger::NOTICE:
				$method = 'notice';
				break;

			case Logging\Logger::DEBUG:
			default:
				$method = 'debug';
				break;
		}

		$poppedProcessors = [];
		while ($this->aggregatedLogger->getLogger()->getProcessors() !== array()) {
			$processor = $this->aggregatedLogger->getLogger()->popProcessor();
			$poppedProcessors[] = $processor;
		}
		$this->aggregatedLogger->getLogger()->pushProcessor($this->processor);
		$this->aggregatedLogger->{$method}($line);
		$this->aggregatedLogger->getLogger()->popProcessor();
		foreach (array_reverse($poppedProcessors) as $processor) {
			$this->aggregatedLogger->getLogger()->pushProcessor($processor);
		}
	}

	/**
	 * @return void
	 */
	public function shutdown()
	{
		unset($this->aggregatedLogger);

		$this->shutdown_handled = true;
	}
}
