<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");

use \QU\PowerBiReportingProvider\DataObjects\TrackingOptions;

/**
 * Class ilPowerBiReportingProviderConfigGUI
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
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

	/**
	 * @param $cmd
	 * @return void
	 */
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

	/**
	 * @return ilPropertyFormGUI
	 */
	public function getConfigurationForm()
	{
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->plugin->txt('configuration_export'));

		$target_plugin_id = 'lpeventreportqueue';
		$target_plugin_name = 'lpeventreportqueue';
		if (\ilPluginAdmin::isPluginActive($target_plugin_id) != false) {
			$link = $this->ctrl->getLinkTargetByClass([
				\ilObjComponentSettingsGUI::class
			], 'showPlugin', false, false, false);

			if (preg_match('/plugin_id=([^&]+)/i', $link) > 0) {
				$link = preg_replace_callback('/plugin_id=([^&]+)/i', function (array $matches) use ($target_plugin_id) {
					return 'plugin_id=' . $target_plugin_id;
				}, $link);

			} else {
				$link .= '&plugin_id=' . $target_plugin_id;
			}

			if (preg_match('/pname=([^&]+)/i', $link) > 0) {
				$link = preg_replace_callback('/pname=([^&]+)/i', function (array $matches) use ($target_plugin_name) {
					return 'pname=' . $target_plugin_name;
				}, $link);

			} else {
				$link .= 'pname=' . $target_plugin_name;
			}

		} else {
			$link="#";
		}

		$form->setDescription(
			sprintf($this->plugin->txt('config_export_desc'), $link)
		);

		$ti = new \ilTextInputGUI($this->plugin->txt('export_path'), 'export_path');
		$ti->setInfo($this->plugin->txt('export_path_info'));
		$ti->setValue($this->settings->get('export_path', '/tmp'));
		$form->addItem($ti);

		$ti = new \ilTextInputGUI($this->plugin->txt('export_filename'), 'export_filename');
		$ti->setInfo($this->plugin->txt('export_filename_info'));
		$ti->setValue($this->settings->get('export_filename', '[Y-m-d]_powbi_export'));
		$form->addItem($ti);

		$ni = new \ilNumberInputGUI($this->plugin->txt('export_limit'), 'export_limit');
		$ni->setInfo($this->plugin->txt('export_limit_info'));
		$ni->setValue($this->settings->get('export_limit', 0));
		$ni->setMinValue(0);
		$ni->setMaxValue(999);
		$form->addItem($ni);

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
				$sub_ti->setInfo($this->plugin->txt($keyword . '_name_info'));
				$sub_ti->setValue($option->getFieldName());

				$cb->addSubItem($sub_ti);
				$form->addItem($cb);

				unset($sub_ti);
				unset($cb);
			}
		}

		$ignoreNotAttempted = new ilCheckboxInputGUI($this->plugin->txt('ignoreNotAttempted'), 'ignoreNotAttempted');
		$ignoreNotAttempted->setChecked((bool) $this->settings->get('ignoreNotAttempted_' . $this->plugin->getId(), ''));
        $ignoreNotAttempted->setValue('1');
		$form->addItem($ignoreNotAttempted);

		$form->addCommandButton("save", $this->plugin->txt("save"));
		$form->setFormAction($this->ctrl->getFormAction($this));

		return $form;
	}

	/**
	 * @return void
	 */
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

			if ($form->getInput('export_path')) {
				$this->settings->set('export_path', $form->getInput('export_path'));
			}

			if ($form->getInput('export_filename')) {
				$this->settings->set('export_filename', $form->getInput('export_filename'));
			}

			if ($form->getInput('export_limit') || $form->getInput('export_limit') === '0') {
				$this->settings->set('export_limit', $form->getInput('export_limit'));
			}

			$this->settings->set('ignoreNotAttempted_' . $this->plugin->getId(), (string) ((int) $form->getInput('ignoreNotAttempted')));

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
