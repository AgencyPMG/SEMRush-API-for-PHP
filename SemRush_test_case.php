<?PHP

include_once('SemRush.php');


$s = new SemRush('http://reddit.com');

echo "Main Report: ";
print_r($s->getMainReport());

//you can also use the SEMRush PHP API to call different types of reports not listed
print_r($s->callReport("domain_rank", array("export_columns"=>"Dn,Rk,Or,Ot,Oc,Ad,At,Ac")));


//echo "getMainKeywordReport: ";
//print_r($s->getMainKeywordReport("Seo"));


//echo "getOrganicKeywordsReport: ";
//print_r($s->getOrganicKeywordsReport());

//echo "getAdwordsKeywordReport: ";
//print_r($s->getAdwordsKeywordReport());

//echo "getOrganicURLReport";
//print_r($s->getOrganicURLReport());

//echo "getAdwordsURLReport";
//print_r($s->getAdwordsURLReport());


//echo 'getCompetitorsInOrganicSearchReport';
//print_r($s->getCompetitorsInOrganicSearchReport());

//echo 'getCompetitorsInAdwordsSearchReport';
//print_r($s->getCompetitorsInAdwordsSearchReport());

//echo 'getPotentialAdTrafficBuyersReport';
//print_r($s->getPotentialAdTrafficBuyersReport());

//echo 'getPotentialAdTrafficSellersReport';
//print_r($s->getPotentialAdTrafficSellersReport());


?>