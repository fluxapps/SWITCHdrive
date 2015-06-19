<?php

/**
 * Class exocItemCache
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class exocItemCache {

	const EXOC_ITEM_CACHE = 'exoc_item_cache';
	/**
	 * @var array
	 */
	protected static $instances = array();


	/**
	 * @param exocItem $exocItem
	 */
	public static function store(exocItem $exocItem) {
		$_SESSION[self::EXOC_ITEM_CACHE][$exocItem->getId()] = serialize($exocItem);
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 */
	public static function exists($id) {
		return (unserialize($_SESSION[self::EXOC_ITEM_CACHE][$id]) instanceof exocItem);
	}


	/**
	 * @param $id
	 *
	 * @return exocItem
	 */
	public static function get($id) {
		if (self::exists($id)) {
			return unserialize($_SESSION[self::EXOC_ITEM_CACHE][$id]);
		}

		return NULL;
	}
}

?>
