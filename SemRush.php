<?PHP

/**
 * SemRush API PHP Class
 * 
 * @see http://www.semrush.com/api.html
 * @see http://pmg.co
 * @author Chris Alvares <chris.alvares@pmg.co>
 * @copyright 2012 Performance Media Group
 * @license MIT License
*/



/*
Methods to Call
	public static function getValidDatabases()


	public function __construct($url, $useDomainLevelData=true, $semrushdb='us')
	public function getMainReport()
	public function getMainKeywordReport()
	public function getOrganicKeywordsReport()
	public function getAdwordsKeywordReport()	
	public function getOrganicURLReport()
	public function getAdwordsURLReport()
	public function getCompetitorsInOrganicSearchReport()
	public function getCompetitorsInAdwordsSearchReport()
	public function getPotentialAdTrafficBuyersReport()	
	public function getPotentialAdTrafficSellersReport()

Example Use, see test cases for more information

$sr = new SemRush("http://pmg.co");
print_r($sr->getMainReport());


Error Messages
ERROR 30 :: LIMIT EXCEEDED
ERROR 50 :: NOTHING FOUND
ERROR 70 :: API KEY HASH FAILURE
ERROR 40 :: MANDATORY PARAMETER "action" NOT SET OR EMPTY
ERROR 41 :: MANDATORY PARAMETER "type" NOT SET OR EMPTY
ERROR 42 :: MANDATORY PARAMETER "domain" NOT SET OR EMPTY
ERROR 43 :: MANDATORY PARAMETER "phrase" NOT SET OR EMPTY
ERROR 44 :: MANDATORY PARAMETER "url" NOT SET OR EMPTY
ERROR 45 :: MANDATORY PARAMETER "vs_domain" NOT SET OR EMPTY
ERROR 120 :: WRONG KEY - ID PAIR
ERROR 121 :: WRONG FORMAT OR EMPTY HASH
ERROR 122 :: WRONG FORMAT OR EMPTY KEY
ERROR 130 :: API DISABLED
ERROR 131 :: LIMIT EXCEEDED
ERROR 132 :: API UNITS BALANCE IS ZERO
ERROR 133 :: DB ACCESS DENIED
ERROR 134 :: TOTAL LIMIT EXCEEDED


*/

class SemRush
{

	protected $apikey='XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
	public $url, $useDomainLevelData, $db;
	public $queryLimit=null;
	
	/*
	We are going to provide backwards compatiblity with the old SEMRush API Class Created by "Indiana Jones"
	 * @see http://php.bubble.ro
	 * @author Indiana Jones <contact@bubble.ro>
	*/
	const TYPE_BY_ORGANIC = 1;
    const TYPE_BY_ADWORDS = 2;
    const TYPE_BY_ORGANIC_ORGANIC = 3;
    const TYPE_BY_ADWORDS_ADWORDS = 4;
    const TYPE_BY_ORGANIC_ADWORDS = 5;
    const TYPE_BY_ADWORDS_ORGANIC = 6;
    const TYPE_RELATED = 7;
    const TYPE_RANK_VALUES = 0;

		
	public function __construct($url='', $useDomainLevelData=true, $semrushdb='us')
	{
		$this->url = $url;
		$this->useDomainLevelData = $useDomainLevelData;
		$this->db = $semrushdb;
		
	}
	
	public static function getValidDatabases()
	{
		return array('us', 'uk', 'ca', 'ru', 'de', 'fr', 'es', 'it', 'br', 'au', 'us.bing');
	}
	
	public function search($url, $reportType = 0, $db = 'us', $limit = 500, $offset = 0)
	{
		$this->url = $url;
		if($db != null)
			$this->db = $db;
		
		$this->queryLimit = $limit;
		//offset is not supported yet.
		
		if($reportType == self::TYPE_BY_ORGANIC) return $this->getOrganicKeywordsReport();
		if($reportType == self::TYPE_BY_ADWORDS) return $this->getAdwordsKeywordReport();
		if($reportType == self::TYPE_BY_ORGANIC_ORGANIC) return $this->getCompetitorsInOrganicSearchReport();
		if($reportType == self::TYPE_BY_ADWORDS_ADWORDS) return $this->getCompetitorsInAdwordsSearchReport();
		if($reportType == self::TYPE_BY_ORGANIC_ADWORDS) return $this->getPotentialAdTrafficBuyersReport();
		if($reportType == self::TYPE_BY_ADWORDS_ORGANIC) return $this->getPotentialAdTrafficSellersReport();
		if($reportType == self::TYPE_RELATED) return $this->getMainKeywordReport($url);
		if($reportType == self::TYPE_RANK_VALUES) return $this->getMainReport();
		
		return null;
	}
	
	
	public function getMainReport()
	{
		return $this->callReport('domain_rank', $optionalParams=null);
	}
	
	public function getMainKeywordReport($keyword)
	{
		return $this->callReport('phrase_this', array("phrase"=>urlencode($keyword)));
	}
	
	public function getOrganicKeywordsReport()
	{
		return $this->callReport('domain_organic');
	}
	
	public function getAdwordsKeywordReport()
	{
		return $this->callReport('domain_adwords');
	}
	
	public function getOrganicURLReport()
	{
		return $this->callReport('url_organic', array('url'=>$this->url));
	}
	
	public function getAdwordsURLReport()
	{
		return $this->callReport('url_adwords', array('url'=>$this->url));
	}
	
	public function getCompetitorsInOrganicSearchReport()
	{
		return $this->callReport('domain_organic_organic');
	}
	
	public function getCompetitorsInAdwordsSearchReport()
	{
		return $this->callReport('domain_adwords_adwords');
	}
	
	public function getPotentialAdTrafficBuyersReport()
	{
		return $this->callReport('domain_organic_adwords');
	}
	
	public function getPotentialAdTrafficSellersReport()
	{
		return $this->callReport('domain_adwords_organic');
	}
	
	public function callReport($reportType, $optionalParams=null)
	{
		$url = $this->buildQueryString($reportType, $optionalParams);
		return $this->buildReport($this->cURL($url));
	}
	
	
	
	protected function buildReport($reportData)
	{
		
		//first get rid of all of the 
		if($reportData === false) return array();
		
		//draw out the data into lines
		$lines = explode("\n", $reportData);
		if(sizeof($lines) == 0) return array();
		
		
		if(strpos($lines[0], 'ERROR:'))
		{
			throw new Exception('ERROR: ' . $reportData);
		}
		
		//now put these in teh values
		$grid = $this->splitCSVFields($lines);
		
		$firstRow = array_shift($grid);
		
		for($i=0;$i<sizeof($firstRow);$i++)
		{
			$firstRow[$i] = trim(strtolower(str_ireplace(' ', '_', $firstRow[$i])));
			$firstRow[$i] = str_ireplace('(%)', 'percent', $firstRow[$i]);
		}	
		
		
		$array = array();
		
		foreach($grid as $row)
		{
			$r = array();
			for($i=0;$i<sizeof($row);$i++)
			{
				if(isset($firstRow[$i]))
				{
					$r[$firstRow[$i]] = $row[$i];
				}
			}
			$array[] = $r;
		
		}
		return $array;		
	}
	
	private function splitCSVFields($lines)
	{
		$fields = array();
		foreach($lines as $line)
		{
			$newline = $this->getParsedCSVString($line);
			try
			{
				$values = explode(";", $newline);
				$fields[] = str_replace("!-!", ",",$values);
			}
			catch(Exception $e)
			{
			
			}
		}
		
		return $fields;
		
	}
		
	private function getParsedCSVString($fullString)
	{
		$array = explode('"', $fullString);
		for($i = 1; $i<sizeof($array);$i+=2)
		{
			$array[$i] = str_ireplace(";", "!-!", $array[$i]);
		}
		return implode($array);	
	}
	
	protected function buildQueryString($reportType, $optionalParams=array())
	{

		$url = 'http://' . $this->db . '.api.semrush.com/?action=report';
		$params = array(
			'type'=>$reportType,
			'key'=>$this->apikey,
			'export'=>'api',
			'export_escape'=>1,
		);	
		
		if($this->queryLimit != null)
			$params['display_limit'] = $this->queryLimit;		
		
		if($this->useDomainLevelData)
			$params['domain'] = $this->url;	
		else 
			$params['url'] = $this->url;
				
		if($optionalParams != null)
			$params = array_merge($params, $optionalParams);
		
		$query = '';
		foreach($params as $paramName=>$paramValue)
		{
			$query .= '&' . $paramName . '=' . $paramValue;
		}
		
		return $url . $query;		
		
	}


	protected function cURL($request)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array ( 'X-Real-IP',  $_SERVER['SERVER_ADDR']));
		$data = curl_exec($curl);
		
		if(curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 )
		{
			curl_close($curl);
			return $data;
		}
		
		throw new SemRushException(curl_getinfo($curl, CURLINFO_HTTP_CODE) .' - '. $data . "\nURL: " . $request);
		
		curl_close($curl);
		return $data;
	   
	}


}

class SemRushException extends Exception
{

}




?>