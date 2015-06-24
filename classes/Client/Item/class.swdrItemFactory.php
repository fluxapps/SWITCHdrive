<?php
require_once('class.swdrFolder.php');
require_once('class.swdrFile.php');
require_once('class.swdrItemCache.php');
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');


/**
 * Class swdrItemFactory
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrItemFactory {

	/**
	 * @param array $response
	 *
	 * @return swdrFolder[]|swdrFile[]
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
                $exid_item = new swdrFolder();
                $exid_item->loadFromProperties($web_url, $props, $parent_id);
                swdrItemCache::store($exid_item);
                $return[] = $exid_item;
			} else { // is file
				$exid_item = new swdrFile();
				$exid_item->loadFromProperties($web_url, $props, $parent_id);
                swdrItemCache::store($exid_item);
				$return[] = $exid_item;
			}
		}

		return $return;
	}
}

?>
