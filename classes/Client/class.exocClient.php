<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OwnCloud/classes/Client/Item/class.exocItemFactory.php');
use Sabre\DAV\Client;
/**
 * Class exocClient
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class exocClient {

    /**
     * @var Sabre\DAV\Client
     */
    protected $sabre_client;
    /**
     * @var exocApp
     */
    protected $exoc_app;
    /**
     * @var ilOwnCloudPlugin
     */
    protected $pl;

    const DEBUG = true;

    /**
     * @param exocApp $exocApp
     */
    public function __construct(exocApp $exocApp) {
        $this->setExocApp($exocApp);
        $this->pl = ilOwnCloudPlugin::getInstance();
        include './Customizing/global/plugins/Modules/Cloud/CloudHook/OwnCloud/lib/SabreDAV/vendor/autoload.php';
    }

    protected function getSabreClient(){
        if(!$this->sabre_client){
            $settings = $this->getObjectSettings();
            $this->sabre_client = new Client($settings);
        }
        return $this->sabre_client;
    }

    /**
     * @param $id
     *
     * @return exodFile[]|exodFolder[]
     */
    public function listFolder($id) {
        $id = rawurlencode($id);
        $settings = $this->getObjectSettings();
        if($client = $this->getSabreClient()){
            $response = $client->propFind($settings['base_uri'] . $id, array(), 1);
            $items = exocItemFactory::getInstancesFromResponse($response);
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
     * @return exocFile
     * @throws ilCloudException
     */
    public function deliverFile($path) {
        $path = rawurlencode($path);
        $response = $this->getSabreClient()->request('GET', $path);
        if(self::DEBUG){
            global $log;
            $log->write("[exocClient]->deliverFile({$path}) | response status Code: {$response['statusCode']}");
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
            $log->write("[exocClient]->createFolder({$path}) | response status Code: {$response['statusCode']}");
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
            $log->write("[exocClient]->uploadFile({$location}, {$local_file_path}) | response status Code: {$response['statusCode']}");
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
            $log->write("[exocClient]->delete({$path}) | response status Code: {$response['statusCode']}");
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
     * @return exocApp
     */
    public function getExocApp() {
        return $this->exoc_app;
    }


    /**
     * @param exodApp $exod_app
     */
    public function setExocApp($exoc_app) {
        $this->exod_app = $exoc_app;
    }

    /**
     * @return array
     */
    protected function getObjectSettings()
    {
        $obj_id = ilObject2::_lookupObjectId($_GET['ref_id']);
        $ownCloudObj = new ilOwnCloud('OwnCloud', $obj_id);
        $settings = array(
            'baseUri' => $ownCloudObj->getBaseUri(),
            'userName' => $ownCloudObj->getUsername(),
            'password' => $ownCloudObj->getPassword(),
        );
        if ($proxy = $ownCloudObj->getProxy()) {
            $settings['proxy'] = $proxy;
            return $settings;
        }
        return $settings;
    }

} 