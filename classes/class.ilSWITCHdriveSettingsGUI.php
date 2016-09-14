<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/class.swdrTree.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/class.swdrTreeGUI.php');
/**
 * Class ilSWITCHdriveSettingsGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy ilSWITCHdriveSettingsGUI : ilObjCloudGUI
 * @ingroup           ModulesCloud
 */
class ilSWITCHdriveSettingsGUI extends ilCloudPluginSettingsGUI {

	/**
	 * @var ilPropertyFormGUI
	 */
	protected $form;


    public function initSettingsForm()
    {
        global $ilCtrl, $lng;

        $this->form = new ilPropertyFormGUI();

        // title
        $ti = new ilTextInputGUI($lng->txt("title"), "title");
        $ti->setRequired(true);
        $this->form->addItem($ti);

        // description
        $ta = new ilTextAreaInputGUI($lng->txt("description"), "desc");
        $this->form->addItem($ta);

        // online
        $cb = new ilCheckboxInputGUI($lng->txt("online"), "online");
        $this->form->addItem($cb);

        $folder = new ilTextInputGUI($lng->txt("cld_root_folder"), "root_folder");
        if(!$this->cloud_object->currentUserIsOwner())
        {
            $folder->setDisabled(true);
            $folder->setInfo($lng->txt("cld_only_owner_has_permission_to_change_root_path"));
        }

        $folder->setMaxLength(255);
        $folder->setSize(50);
        $this->form->addItem($folder);

        $this->createPluginSection();
        $this->initPluginSettings();

        $this->form->addCommandButton("updateSettings", $lng->txt("save"));

        $this->form->setTitle($lng->txt("cld_edit_Settings"));
        $this->form->setFormAction($ilCtrl->getFormActionByClass("ilCloudPluginSettingsGUI"));
    }

    public function setRootFolder($root_path){
        global $ilCtrl, $lng;
        $this->getPluginObject()->getCloudModulObject()->setRootFolder($root_path);
        $this->getPluginObject()->getCloudModulObject()->update();
        ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
        $ilCtrl->redirectByClass('ilCloudPluginSettingsGUI', 'editSettings');
    }


	protected function initPluginSettings() {
        // username
        $item = new ilTextInputGUI($this->getPluginObject()->getPluginHookObject()->txt('username'), 'username');
        $item->setRequired(true);
	    $this->form->addItem($item);


        //password
        $item = new ilPasswordInputGUI($this->getPluginObject()->getPluginHookObject()->txt('password'), 'password');
        $item->setInfo($this->getPluginObject()->getPluginHookObject()->txt('password_info'));
        $item->setRetype(false);
        $item->setRequired(true);
        $item->setSkipSyntaxCheck(true);
	    $this->form->addItem($item);
    }

    protected function getPluginSettingsValues(&$values){
        $values['username'] = $this->getPluginObject()->getUsername();
        $values['password'] = $this->getPluginObject()->getPassword();
    }

    public function updatePluginSettings()
    {
        global $ilCtrl;
        $this->setTabs("general");

        $client = $this->getPluginObject()->getSwdrApp()->getSwdrClient();
        $had_connection = $client->hasConnection();

        $this->getPluginObject()->setUsername($this->form->getInput("username"));
        $this->getPluginObject()->setPassword($this->form->getInput("password"));
        $this->getPluginObject()->doUpdate();

        $client->loadClient();
        $has_connection = $client->hasConnection();
        // show tree view if client found connection after the update
        if (!$had_connection && $has_connection) {
            $ilCtrl->setParameter($this, 'active_subtab', 'choose_root');
        } else {
            $ilCtrl->setParameter($this, 'active_subtab', 'general');
        }

        if(!$client->hasConnection()){
            ilUtil::sendFailure($this->getPluginObject()->getPluginHookObject()->txt('no_connection'), true);
        }
    }

    /**
     * Edit Settings. This commands uses the form class to display an input form.
     */
    function editSettings()
    {
        global $tpl;

        if($root_path = $_GET['root_path']) {
            $this->setRootFolder($root_path);
        }

        if($_GET['active_subtab'] == 'choose_root'){
            $this->setTabs("choose_root");
            $this->showTreeView();
        }

        $this->setTabs("general");

        try
        {
            $this->initSettingsForm();
            $this->getSettingsValues();
            $client = $this->getPluginObject()->getSwdrApp()->getSwdrClient();
            if(!$client->hasConnection()){
                ilUtil::sendFailure($this->getPluginObject()->getPluginHookObject()->txt('no_connection'), true);
            }
            $tpl->setContent($this->form->getHTML());
        } catch (Exception $e)
        {
            ilUtil::sendFailure($e->getMessage());
        }
    }

    /**
	 * @return ilSWITCHdrive
	 */
	public function getPluginObject() {
		return parent::getPluginObject(); // TODO: Change the autogenerated stub
	}

    /**
     * @param $active
     */
    protected function setTabs($active)
    {
        global $ilTabs, $ilCtrl, $lng;
        $ilTabs->activateTab("settings");

        $ilCtrl->setParameter($this, 'active_subtab', 'general');
        $ilTabs->addSubTab("general", $lng->txt("general_settings"), $ilCtrl->getLinkTarget($this, 'editSettings'));
        $ilCtrl->setParameter($this, 'active_subtab', 'choose_root');
        $ilTabs->addSubTab("choose_root", $this->getPluginObject()->getPluginHookObject()->txt("subtab_choose_root"), $ilCtrl->getLinkTarget($this, 'editSettings'));
        $ilTabs->activateSubTab($active);
    }

    public function showTreeView(){
        global $tpl, $ilCtrl;
        $client = $this->getPluginObject()->getSwdrApp()->getSwdrClient();
        if($client->hasConnection()){
            $tree = new swdrTree($client);
            $tree_gui = new swdrTreeGUI('tree_expl', $this, 'editSettings', $tree);
            if ($tree_gui->handleCommand())
            {
                return;
            }
            ilUtil::sendInfo($this->getPluginObject()->getPluginHookObject()->txt('choose_root'), true);
            $tpl->setContent($tree_gui->getHTML());
            $tpl->show();exit;
        }else{
            $ilCtrl->setParameter($this, 'active_subtab', 'general');
            $ilCtrl->redirect($this, 'editSettings');
        }
    }
}

?>
