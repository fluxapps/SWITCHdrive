<#1>
<?php
include_once("./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/class.ilSWITCHdrivePlugin.php");
$plugin_object = ilSWITCHdrivePlugin::getInstance();

$fields = array(
    'id' => array(
        'type' => 'integer',
        'length' => 8,
        'notnull' => true
    ),
    'base_uri' => array(
        'type' => 'text',
        'length' => 256
    ),
    'username' => array(
        'type' => 'text',
        'length' => 256
    ),
    'password' => array(
        'type' => 'text',
        'length' => 256
    ),
    'proxy' => array(
        'type' => 'text',
        'length' => 256
    ),
);
global $ilDB;
$ilDB->createTable($plugin_object->getPluginTableName(), $fields);
$ilDB->addPrimaryKey($plugin_object->getPluginTableName(), array( "id" ));
?>
<#2>
<?php
include_once("./Modules/Cloud/classes/class.ilCloudPluginConfig.php");
include_once("./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/class.ilSWITCHdrivePlugin.php");
$plugin_object = ilSWITCHdrivePlugin::getInstance();
$config_object = new ilCloudPluginConfig($plugin_object->getPluginConfigTableName());
$config_object->initDB();
?>
