<?php
include_once("./Services/UIComponent/Explorer2/classes/class.ilTreeExplorerGUI.php");

/**
 * Class swdrTreeGUI
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrTreeGUI extends ilTreeExplorerGUI{

    /**
     * @var swdrTree
     */
    protected $tree;
	/**
	 * @var ilLog
	 */
	protected $log;

    public function __construct($a_expl_id, $a_parent_obj, $a_parent_cmd, swdrTree $tree){
        global $tpl, $ilLog;
        parent::__construct($a_expl_id, $a_parent_obj, $a_parent_cmd, $tree);
	    $this->setSkipRootNode(false);
	    $this->setPreloadChilds(false);
	    $this->setAjax(true);

        // necessary from 5.4 to fix bug where only root node shows
        $this->setNodeOpen('/');

	    $this->log = $ilLog;
        $css =
            '.jstree a.clickable_node {
               color:black !important;
             }

             .jstree a:hover {
               color:#b2052e !important;
             }';
        $tpl->addInlineCss($css);
    }

	/**
	 * Get node icon
	 *
	 * @param array $a_node node data
	 * @return string icon path
	 */
	function getNodeIcon($a_node)
	{
        if($a_node->getType() == swdrItem::TYPE_FILE){
            $img = 'icon_dcl_file.svg';
        }else{
            $img = 'icon_dcl_fold.svg';
        }
		return  ilUtil::getImagePath($img);
	}

	/**
	 * Get node icon alt attribute
	 *
	 * @param mixed $a_node node object/array
	 * @return string image alt attribute
	 */
	function getNodeIconAlt($a_node)
	{
		return '';
	}

    /**
     * @param mixed $node
     * @return string
     */
    function getNodeContent($node)
    {
        $node->getName() ? $name = $node->getName() : $name = 'SWITCHdrive';
	    return $name;
    }

    function getNodeHref($node){
        global $ilCtrl;
        $ilCtrl->setParameter($this->parent_obj, 'root_path', $node->getFullPath());
        return $ilCtrl->getLinkTarget($this->parent_obj, 'editSettings');
    }

    function isNodeClickable($node){
        return ($node->getType() == swdrItem::TYPE_FOLDER);
    }

    /**
     * Get root node.
     *
     * Please note that the class does not make any requirements how
     * nodes are represented (array or object)
     *
     * @return mixed root node object/array
     */
    function getRootNode()
    {
        return $this->tree->getRootNode();
    }

    /**
     * Get id of a node
     *
     * @param mixed $a_node node array or object
     * @return string id of node
     */
    function getNodeId($a_node)
    {
        return $a_node->getFullPath();
    }


}