<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

use \QU\PowerBiReportingProvider\DataObjects\TrackingOptions;

class ilPowerBiReportingProviderConfigGUI extends ilPluginConfigGUI
{
	/** @var ilPowerBiReportingProviderPlugin */
	private $plugin;

	/** @var \ilCtrl */
	protected $ctrl;

	/** @var \ilLanguage */
	protected $lng;

	/** @var \ilTemplate */
	protected $tpl;

	/** @var \ilTabsGUI */
	protected $tabs;

	/** @var \ilSetting */
	protected $settings;

	/** @var \ILIAS\DI\BackgroundTaskServices */
	protected $backgroundTasks = null;

	/** @var string */
	protected $active_tab;

	/**
	 * @return void
	 */
	public function construct()
	{
		global $DIC;

		$this->plugin = ilPowerBiReportingProviderPlugin::getInstance();
		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->tpl = $DIC["tpl"];
		$this->tabs = $DIC->tabs();
		$this->settings = $DIC->settings();
		if (null === $this->backgroundTasks) {
			$this->backgroundTasks = $DIC->backgroundTasks();
		}
	}

	function performCommand($cmd)
	{
		$this->construct();
		$next_class = $this->ctrl->getNextClass($this);
		$this->setTabs();

		switch ($next_class) {
			default:
				switch ($cmd) {
					case "configure":
//						$this->tabs->activateTab('configure');
						$this->configure();
						break;
					default:
						$cmd .= 'Cmd';
						$this->$cmd();
						break;
				}
				break;
		}
	}

	/**
	 * @return void
	 */
	public function configure()
	{
		$form = $this->getConfigurationForm();
		$this->tpl->setContent($form->getHTML());
	}

	public function getConfigurationForm()
	{
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->plugin->txt('configuration_export'));

		$trackingOptions = new TrackingOptions();
		$trackingOptions->load();
		foreach ($trackingOptions->getAvailableOptions() as $keyword) {
			$option = $trackingOptions->getOptionByKeyword($keyword);
			if (isset($option)) {
				$cb = new ilCheckboxInputGUI($this->plugin->txt($keyword), $keyword);
				$cb->setInfo($this->plugin->txt($keyword . '_info'));
				$cb->setChecked($option->isActive());
				if (in_array($keyword, ['id', 'timestamp'])) {
					$cb->setDisabled(true);
				}
				$sub_ti = new ilTextInputGUI($this->plugin->txt($keyword . '_name'), $keyword . '_name');
				$sub_ti->setInfo($keyword . '_name_info');
				$sub_ti->setValue($option->getFieldName());

				$cb->addSubItem($sub_ti);
				$form->addItem($cb);

				unset($sub_ti);
				unset($cb);
			}
		}

		$form->addCommandButton("save", $this->plugin->txt("save"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		return $form;
	}

	public function saveCmd()
	{
		$form = $this->getConfigurationForm();
		$trackingOptions = new TrackingOptions();
		$trackingOptions->load();

		if ($form->checkInput()) {
			// save...
			foreach ($trackingOptions->getAvailableOptions() as $keyword) {
				$opt = $trackingOptions->getOptionByKeyword($keyword);
				if ($form->getInput($keyword)) {
					$opt->setActive(true);
				} else {
					if (!in_array($keyword, ['id', 'timestamp'])) {
						$opt->setActive(false);
					}
				}
				if ($form->getInput($keyword . '_name')) {
					$opt->setFieldName(($form->getInput($keyword . '_name')));
				}
				$opt->save();
				unset($opt);
			}

			ilUtil::sendSuccess($this->plugin->txt("saving_invoked"), true);
			$this->ctrl->redirect($this, "configure");

		} else {
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHtml());
		}
	}

	/**
	 * @return array
	 */
	public function getTabs(): array
	{
		return [];
	}

	/**
	 * @return void
	 */
	protected function setTabs()
	{
		if (!empty($this->getTabs())) {
			foreach ($this->getTabs() as $tab) {
				$this->tabs->addTab($tab['id'], $tab['txt'], $this->ctrl->getLinkTarget($this, $tab['cmd']));
			}
		}
	}

}
