<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes/Client/Item/class.swdrItemFactory.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/classes//class.swdrConfig.php');
use Sabre\DAV\Client;
/**
 * Class swdrClient
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class swdrClient {

    /**
     * @var Sabre\DAV\Client
     */
    protected $sabre_client;
    /**
     * @var swdrApp
     */
    protected $swdr_app;
    /**
     * @var ilSWITCHdrivePlugin
     */
    protected $pl;

    const DEBUG = true;

    /**
     * @param swdrApp $swdrApp
     */
    public function __construct(swdrApp $swdrApp) {
        $this->setSwdrApp($swdrApp);
        $this->pl = ilSWITCHdrivePlugin::getInstance();
        include './Customizing/global/plugins/Modules/Cloud/CloudHook/SWITCHdrive/lib/SabreDAV/vendor/autoload.php';
    }

    protected function getSabreClient(){
        if(!$this->sabre_client){
            $settings = $this->getObjectSettings();
            $this->sabre_client = new Client($settings);
        }
        return $this->sabre_client;
    }

    public function hasConnection(){
        $response = $this->getSabreClient()->request('GET');
        return ($response['statusCode'] < 400);
    }

    /**
     * @param $id
     *
     * @return swdrFile[]|swdrFolder[]
     */
    public function listFolder($id) {
        $id = rawurlencode($id);
        $settings = $this->getObjectSettings();
        if($client = $this->getSabreClient()){
            $response = $client->propFind($settings['base_uri'] . $id, array(), 1);
            $items = swdrItemFactory::getInstancesFromResponse($response);
            return $items;
        }
        return array();
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function folderExists($path) {
        return $this->itemExists($path);
    }


    /**
     * @param $path
     *
     * @return bool
     */
    public function fileExists($path) {
        return $this->itemExists($path);
    }


    /**
     * @param $path
     *
     * @return swdrFile
     * @throws ilCloudException
     */
    public function deliverFile($path) {
        $path = rawurlencode($path);
        $response = $this->getSabreClient()->request('GET', $path);
        if(self::DEBUG){
            global $log;
            $log->write("[swdrClient]->deliverFile({$path}) | response status Code: {$response['statusCode']}");
        }
        $path = rawurldecode($path);
        $file_name = pathinfo($path, PATHINFO_FILENAME);

        header("Content-type: application/octet-stream");
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file_name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $response['headers']['content-length'][0]);
        echo $response['body'];exit;

    }


    /**
     * @param $path
     *
     * @return bool
     */
    public function createFolder($path) {
        $path = rawurlencode($path);

        $response = $this->getSabreClient()->request('MKCOL', $path);
        if(self::DEBUG){
            global $log;
            $log->write("[swdrClient]->createFolder({$path}) | response status Code: {$response['statusCode']}");
        }
        return true;
    }


    /**
     * @param $location
     * @param $local_file_path
     *
     * @return bool
     * @throws ilCloudException
     */
    public function uploadFile($location, $local_file_path) {
        $location = rawurlencode($location);
        if($this->fileExists($location)){
            $basename = pathinfo($location, PATHINFO_FILENAME);
            $extension = pathinfo($location, PATHINFO_EXTENSION);
            $i = 1;
            while($this->fileExists($basename."({$i}).".$extension)){
                $i++;
            }
            $location = $basename."({$i}).".$extension;
        }
        $response = $this->getSabreClient()->request('PUT', $location, file_get_contents($local_file_path));
        if(self::DEBUG){
            global $log;
            $log->write("[swdrClient]->uploadFile({$location}, {$local_file_path}) | response status Code: {$response['statusCode']}");
        }
        return true;
    }


    /**
     * @param $path
     *
     * @return bool
     */
    public function delete($path) {
        $response = $this->getSabreClient()->request('DELETE', rawurlencode($path));
        if(self::DEBUG){
            global $log;
            $log->write("[swdrClient]->delete({$path}) | response status Code: {$response['statusCode']}");
        }
        return true;
    }


    /**
     * @param $path
     *
     * @return bool
     */
    protected function itemExists($path) {
        $request = $this->getSabreClient()->request('GET', rawurlencode($path));
        if($request['statusCode'] < 400){
            return true;
        }
        return false;
    }


    /**
     * @return swdrApp
     */
    public function getSwdrApp() {
        return $this->swdr_app;
    }


    /**
     * @param exodApp $exod_app
     */
    public function setSwdrApp($swdr_app) {
        $this->exod_app = $swdr_app;
    }

    /**
     * @return array
     */
    protected function getObjectSettings()
    {
        $obj_id = ilObject2::_lookupObjectId($_GET['ref_id']);
        $SWITCHdriveObj = new ilSWITCHdrive('SWITCHdrive', $obj_id);
        $conf = new swdrConfig();
        $settings = array(
            'baseUri' => $conf->getBaseURL(),
            'userName' => $SWITCHdriveObj->getUsername(),
            'password' => $SWITCHdriveObj->getPassword(),
        );
        if ($proxy = $SWITCHdriveObj->getProxy()) {
            $settings['proxy'] = $proxy;
            return $settings;
        }
        return $settings;
    }

} 