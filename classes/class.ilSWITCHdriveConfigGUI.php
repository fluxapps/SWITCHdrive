<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/App/class.swdrApp.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/class.swdrConfig.php');
require_once('./Modules/Cloud/classes/class.ilCloudPluginConfigGUI.php');

/**
 * Class ilSWITCHdriveConfigGUI
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ilSWITCHdriveConfigGUI extends ilCloudPluginConfigGUI {

	const IL_CHECKBOX_INPUT_GUI = 'ilCheckboxInputGUI';
	const IL_TEXT_INPUT_GUI = 'ilTextInputGUI';
	const IL_NUMBER_INPUT_GUI = 'ilNumberInputGUI';
	const IL_SELECT_INPUT_GUI = 'ilSelectInputGUI';


	/**
	 * @return array
	 */
	public function getFields() {
		return array(
            swdrConfig::F_BASEURL => array( 'type' => self::IL_TEXT_INPUT_GUI, 'required' => true,'subelements' => NULL ),
//            swdrConfig::F_SSL_VERSION => array(
//				'type' => self::IL_SELECT_INPUT_GUI,
//				'options' => array(
//					CURL_SSLVERSION_DEFAULT => 'Standard',
//					CURL_SSLVERSION_TLSv1 => 'TLSv1',
//					CURL_SSLVERSION_SSLv2 => 'SSLv2',
//					CURL_SSLVERSION_SSLv3 => 'SSLv3'
//				),
//				'info' => 'config_info_ssl_version',
//				'subelements' => NULL
//			),
		);
	}


	public function initConfigurationForm() {
		global $lng, $ilCtrl;

		include_once("./Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();

		foreach ($this->fields as $key => $item) {
			$field = new $item["type"]($this->plugin_object->txt($key), $key);
			if ($item["type"] == self::IL_SELECT_INPUT_GUI) {
				$field->setOptions($item['options']);
			}
            if(isset($item["info"])){
			    $field->setInfo($this->plugin_object->txt($item["info"]));
            }
			if (is_array($item["subelements"])) {
				foreach ($item["subelements"] as $subkey => $subitem) {
					$subfield = new $subitem["type"]($this->plugin_object->txt($key . "_" . $subkey), $key . "_" . $subkey);
					$subfield->setInfo($this->plugin_object->txt($subitem["info"]));
					$field->addSubItem($subfield);
				}
			}
            $field->setRequired($item['required']);
			$this->form->addItem($field);
		}

		$this->form->addCommandButton("save", $lng->txt("save"));

		$this->form->setTitle($this->plugin_object->txt("configuration"));
		$this->form->setFormAction($ilCtrl->getFormAction($this));

		return $this->form;
	}
}
