<?php
require_once('./Modules/Cloud/classes/class.ilCloudHookPlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OwnCloud/classes/class.exocConfig.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OwnCloud/classes/App/class.exocApp.php');
require_once('class.ilDynamicLanguage.php');
/**
 * Class ilOwnCloudPlugin
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ilOwnCloudPlugin extends ilCloudHookPlugin implements ilDynamicLanguageInterfaceOC{

    const PLUGIN_NAME = 'OwnCloud';

    /**
     * @var exocApp
     */
    protected static $app_instance;

    /**
     * @return string
     */
    public function getPluginName() {
        return self::PLUGIN_NAME;
    }


    /**
     * @var ilOwnCloudPlugin
     */
    protected static $instance;


    /**
     * @return ilOwnCloudPlugin
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
        return './Customizing/global/plugins/Modules/Cloud/CloudHook/OwnCloud/lang/lang.csv';
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
    public function getExocApp() {
        $exocConfig = new exocConfig();
        $exocConfig->checkComplete();

        $app = exocApp::getInstance();


        return $app;
    }


} 