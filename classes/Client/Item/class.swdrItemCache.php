<?php

/**
 * Class swdrItemCache
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrItemCache {

	const SWDR_ITEM_CACHE = 'swdr_item_cache';
	/**
	 * @var array
	 */
	protected static $instances = array();


	/**
	 * @param swdrItem $swdrItem
	 */
	public static function store(swdrItem $swdrItem) {
		$_SESSION[self::SWDR_ITEM_CACHE][$swdrItem->getId()] = serialize($swdrItem);
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 */
	public static function exists($id) {
		return (unserialize($_SESSION[self::SWDR_ITEM_CACHE][$id]) instanceof swdrItem);
	}


	/**
	 * @param $id
	 *
	 * @return swdrItem
	 */
	public static function get($id) {
		if (self::exists($id)) {
			return unserialize($_SESSION[self::SWDR_ITEM_CACHE][$id]);
		}

		return NULL;
	}
}

?>
