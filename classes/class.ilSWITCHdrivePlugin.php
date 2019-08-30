<?php
require_once('./Modules/Cloud/classes/class.ilCloudHookPlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/App/class.swdrApp.php');
require_once('class.ilDynamicLanguage.php');
/**
 * Class ilSWITCHdrivePlugin
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ilSWITCHdrivePlugin extends ilCloudHookPlugin {

    const PLUGIN_NAME = 'SWITCHdrive';

    /**
     * @var swdrApp
     */
    protected static $app_instance;

    /**
     * @return string
     */
    public function getPluginName() {
        return self::PLUGIN_NAME;
    }


    /**
     * @var ilSWITCHdrivePlugin
     */
    protected static $instance;


    /**
     * @return ilSWITCHdrivePlugin
     */
    public static function getInstance() {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @return string
     */
    public function getCsvPath() {
        return './Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/lang/lang.csv';
    }


    /**
     * @return string
     */
    public function getAjaxLink() {
        return NULL;
    }

    /**
     * @param exodBearerToken $exodBearerToken
     *
     * @return exodAppBusiness
     */
    public function getSwdrApp() {
        $app = swdrApp::getInstance();
        return $app;
    }


} 