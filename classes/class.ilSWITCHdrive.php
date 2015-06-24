<?php
require_once('./Modules/Cloud/classes/class.ilCloudPlugin.php');

/**
 * Class ilSWITCHdrive
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilSWITCHdrive : ilObjCloudGUI
 */
class ilSWITCHdrive extends ilCloudPlugin {

    /**
     * @var String
     */
    protected $base_uri;
    /**
     * @var String
     */
    protected $username;
    /**
     * @var String
     */
    protected $password;
    /**
     * @var String
     */
    protected $proxy;


    /**
     * @return bool
     */
    public function read() {
        global $ilDB;

        $set = $ilDB->query('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ' . $ilDB->quote($this->getObjId(), 'integer'));
        $rec = $ilDB->fetchObject($set);
        if ($rec == NULL) {
            return false;
        } else {
            foreach ($this->getArrayForDb() as $k => $v) {
                $this->{$k} = $rec->{$k};
            }
        }
        $this->setMaxFileSize(500);

        return true;
    }


    public function doUpdate() {
        global $ilDB;
        $ilDB->update($this->getTableName(), $this->getArrayForDb(), array( 'id' => array( 'integer', $this->getObjId() ) ));
    }


    public function doDelete() {
        global $ilDB;

        $ilDB->manipulate('DELETE FROM ' . $this->getTableName() . ' WHERE ' . ' id = ' . $ilDB->quote($this->getObjId(), 'integer'));
    }


    public function create() {
        global $ilDB;

        $ilDB->insert($this->getTableName(), $this->getArrayForDb());
    }

    /**
     * @return array
     */
    protected function getArrayForDb() {
        return array(
            'id' => array(
                'text',
                $this->getObjId()
            ),
            'base_uri' => array(
                'text',
                $this->getBaseUri()
            ),
            'username' => array(
                'text',
                $this->getUsername()
            ),
            'password' => array(
                'text',
                $this->getPassword()
            ),
            'proxy' => array(
                'text',
                $this->getProxy()
            ),
        );
    }

    /**
     * @return swdrApp
     * @throws ilCloudException
     */
    public function getSwdrApp() {
        $inst = ilSWITCHdrivePlugin::getInstance()->getSwdrApp();
        return $inst;
    }

    /**
     * @param String $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param String $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return String
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param String $proxy
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * @return String
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @param String $base_uri
     */
    public function setBaseUri($base_uri)
    {
        $this->base_uri = $base_uri;
    }

    /**
     * @return String
     */
    public function getBaseUri()
    {
        return $this->base_uri;
    }

} 