<?php

namespace App\Http\Controllers;

use App\DataSource\XMLData;
use Illuminate\Http\Request;

class SportsFeedController extends Controller
{

    /**
     * Returns the json version of all source data
     * @return string
     */
    public function getAllData(): string
    {
      return $this->convertXmlToArray(
          XMLData::xmlDataSource()
      ) ? json_encode(
          $this->convertXmlToArray(
              XMLData::xmlDataSource()
          )
      ): "no data found";
     }

    /**
     * Returns the json version of the meeting source data
     * @return string
     */
    public function getMeetingData(): string
    {
        $data = array();
        $xml = $this->convertXmlToArray(XMLData::xmlDataSource());
        foreach($xml['Meeting'] as $value) {
            $data[]  =  $value ;
        }
        return $data ? json_encode($data) : "no data found";
    }


    /**
     * Returns the json version of the race source data
     * @return string
     */
    public function getRaceData(): string
    {
        $data = array();
        $xml = $this->convertXmlToArray(XMLData::xmlDataSource());
        foreach($xml['Meeting']['Race'] as $value) {
            $data[]  =  $value ;
        }
        return $data ? json_encode($data) : "no data found";
    }

    /**
     * Returns the json version of the all horses source data
     * @return string
     */
    public function getAllHorsesData(): string
    {
        $data = array();
        $xml = $this->convertXmlToArray(XMLData::xmlDataSource());
        foreach($xml['Meeting']['Race']['Horse'] as $value) {
            $data[]  =  $value ;
        }
        return $data ? json_encode($data) : "no data found";
    }

    /**
     * Returns the json version of the specific horse source data
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getHorseData(Request $request): string
    {
        $data = array();
        $xml = $this->convertXmlToArray(XMLData::xmlDataSource());
        foreach($xml['Meeting']['Race']['Horse'] as $value) {

            if ($value['@attributes']['name'] == $request->input('name')){
                $data[]  =  $value ;
             }
        }
        return $data ? json_encode($data) : "no data found";
    }

    /**
     * Converts xml source data into array
     * @param $xml_string
     * @return array
     */
    public function convertXmlToArray($xml_string): array
    {
        $xml = simplexml_load_string($xml_string, "SimpleXMLElement", LIBXML_NOCDATA);
        return json_decode(json_encode($xml),TRUE);
    }
}


