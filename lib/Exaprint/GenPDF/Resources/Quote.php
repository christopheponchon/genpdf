<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 13/06/14
 * Time: 10:24
 */

namespace Exaprint\GenPDF\Resources;


use Curl;
use Locale\Helper;

class Quote extends Resource implements IResource {

    protected $_data;
    protected $_idQuote;

    /**
     * @param $quoteId
     * @return bool
     */
    public function fetchFromID($quoteId)
    {
        $dao = new DAO\Quote();
        list($id, $quantity) = explode('-',$quoteId);
        $this->_data = $dao->fetchFromId($id, $quantity);
        $this->_idQuote = $id;

        return !is_null($this->_data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if(!isset($_SERVER['masterprint_url'])){
            $_SERVER['masterprint_url'] = 'http://masterprint.exaprint.fr';
        }
        $url = "$_SERVER[masterprint_url]/quote/" . $this->_idQuote;

        $curl   = new Curl();
        //Execute la requete GET de récupération de devis
        $result = $curl->get($url);

        $data=json_decode($result,true);

        //$k = array_keys($data['result']['detail_demande']['element']);
        reset($data['result']['detail_demande']['element']);
        $k = each($data['result']['detail_demande']['element']);
        if(!is_numeric($k[0])){
            $data['result']['detail_demande']['element'] = array($data['result']['detail_demande']['element']);
        }

        $data['result']['data'] = $this->_data;


        return $data['result'];
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->_imageFolder . Helper::$current . "/header.html";
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return $this->_imageFolder . Helper::$current . "/footer.html";
    }

    /**
     * @return mixed
     */
    public function getTemplateFilename()
    {
        return "resources/quote.twig";
    }

    /**
     * @return string
     */
    public function getXml()
    {
        // TODO: Implement getXml() method.
    }
}