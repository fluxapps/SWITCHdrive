<?php

/**
 * Class swdrTree
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrTree {

    /**
     * @var swdrClient
     */
    public $client;

    /**
     * @var string
     */
    protected $root_id;
    /**
     * @var string
     */
    protected $root_path;
    /**
     * @var swdrFolder[]
     */
    public $nodes;



    function __construct(swdrClient $client, $root_id = 'root', $root_path = ''){
        $this->client = $client;
        $this->root_id = $root_id;
        $this->root_path = $root_path;
        $root = new swdrFolder();
        $root->setId($root_id);
        $root->setName('SWITCHdrive');
        $root->setPath($root_path);
        $root->setChilds(array());
        $this->nodes = array();
        $this->nodes['root'] = $root;
        $this->buildTree($root->getId());
    }


    protected function buildTree($id){
        if($id != 'root'){
            $childs = $this->client->listFolder($this->nodes[$id]->getFullPath());
        }else{
            $childs = $this->client->listFolder($this->nodes[$id]->getPath());
        }
        foreach($childs as $child){
            $this->nodes[$id]->addChild($child->getId());
            $this->nodes[$child->getId()] = $child;
            if($child->getType() == swdrItem::TYPE_FOLDER){
                $this->buildTree($child->getId());
            }
        }
    }

    function getNode($node_id){
        return $this->nodes[$node_id];
    }

    function getNodes(){
        return $this->nodes;
    }

    function getRootNode(){
        return $this->nodes['root'];
    }


} 