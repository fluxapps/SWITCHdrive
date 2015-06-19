<?php
require_once('class.exocFolder.php');
require_once('class.exocFile.php');
require_once('class.exocItemCache.php');
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');


/**
 * Class exocItemFactory
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class exocItemFactory {

	/**
	 * @param array $response
	 *
	 * @return exocFolder[]|exocFile[]
	 */
	public static function getInstancesFromResponse($response) {
		$return = array();
		if (count($response) == 0) {
			return $return;
		}
        $parent = array_shift($response);
        $parent_id = $parent['{http://owncloud.org/ns}id'];
        foreach ($response as $web_url => $props) {
			if (!$props["{DAV:}getcontenttype"]) {//is folder
                $exid_item = new exocFolder();
                $exid_item->loadFromProperties($web_url, $props, $parent_id);
                exocItemCache::store($exid_item);
                $return[] = $exid_item;
			} else { // is file
				$exid_item = new exocFile();
				$exid_item->loadFromProperties($web_url, $props, $parent_id);
                exocItemCache::store($exid_item);
				$return[] = $exid_item;
			}
		}

		return $return;
	}
}

?>
