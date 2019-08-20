<?php
require_once("./Modules/Cloud/classes/class.ilCloudPluginService.php");
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once("./Modules/Cloud/classes/class.ilCloudUtil.php");

/**
 * Class ilSWITCHdriveService
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ilSWITCHdriveService extends ilCloudPluginService {

	public function __construct($service_name, $obj_id) {
		parent::__construct($service_name, $obj_id);
	}


	/**
	 * @return swdrApp
	 */
	public function getApp() {
		return $this->getPluginObject()->getSwdrApp();
	}


	/**
	 * @return swdrClient
	 */
	public function getClient() {
		return $this->getApp()->getSwdrClient();
	}


	public function afterAuthService() {
		global $ilCtrl;
		ilUtil::sendFailure($this->plugin_object->getPluginHookObject()->txt('msg_not_configured'), true);
		$ilCtrl->setCmd('edit');

		return true;
	}


    /**
     * @param ilCloudFileTree $file_tree
     * @param string          $parent_folder
     *
     * @throws ilCloudException
     */
	public function addToFileTree(ilCloudFileTree  $file_tree, $parent_folder = "/") {
		$swdrFiles = $this->getClient()->listFolder($parent_folder);

		foreach ($swdrFiles as $item) {
			$size = ($item instanceof swdrFile) ? $size = $item->getSize() : null;
			$is_Dir = $item instanceof swdrFolder;
			$file_tree->addNode($item->getFullPath(), $item->getId(), $is_Dir, strtotime($item->getDateTimeLastModified()), $size);
		}
		//		$file_tree->clearFileTreeSession();
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function getFile($path = null, ilCloudFileTree $file_tree = null) {
		$this->getClient()->deliverFile($path);
	}


	/**
	 * @param                 $file
	 * @param                 $name
	 * @param string $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return mixed
	 */
	public function putFile($file, $name, $path = '', ilCloudFileTree $file_tree = null) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		if ($path == '/') {
			$path = '';
		}

		$return = $this->getClient()->uploadFile($path . "/" . $name, $file);

		return $return;
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function createFolder($path = null, ilCloudFileTree $file_tree = null) {
		if ($file_tree instanceof ilCloudFileTree) {
			$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		}

		if ($path != '/') {
			$this->getClient()->createFolder($path);
		}

		return true;
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function deleteItem($path = null, ilCloudFileTree $file_tree = null) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);

		return $this->getClient()->delete($path);
	}


	/**
	 * @return ilSWITCHdrive
	 */
	public function getPluginObject() {
		return parent::getPluginObject();
	}


	/**
	 * @return ilSWITCHdrivePlugin
	 */
	public function getPluginHookObject() {
		return parent::getPluginHookObject();
	}
}