<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

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
		$form->setTitle($this->plugin->txt('configuration'));

		// @Todo

		$form->addCommandButton("save", $this->plugin->txt("save"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		return $form;
	}

	public function saveCmd() // @Todo
	{
		$form = $this->getConfigurationForm();
//		$settings = new \QU\LERQ\Model\SettingsModel();

		if ($form->checkInput()) {
			// save...
			/** @var \QU\LERQ\Model\SettingsItemModel $setting */
//			foreach ($settings->getAll() as $keyword => $setting) {
//				if ($form->getInput($keyword)) {
//					$settings->__set($keyword, $form->getInput($keyword));
//				} else {
//					$settings->__set($keyword, false);
//				}
//			}
//			$settings->save();

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
		return [
		];
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
