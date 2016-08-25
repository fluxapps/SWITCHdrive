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

    function __construct(swdrClient $client){
        $this->client = $client;
    }


    public function getChilds($id, $order){
    	return $this->client->listFolder($id);
    }

    function getRootNode(){
	    $root = new swdrFolder();
	    $root->setName('');
	    $root->setPath('/');
        return $root;
    }


} 