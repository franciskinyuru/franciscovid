<?php
 function Covid19ImpactEstimator($data_input){
//$data_input='{"region": {"name": "Africa","avgAge": 19.7,"avgDailyIncomeInUSD": 4,"avgDailyIncomePopulation": 0.73},"periodType": "days","timeToElapse": 38,"reportedCases": 2747,"population": 92931687,"totalHospitalBeds": 678874}';
$datas=json_encode($data_input);
$datas=json_decode($datas);
if($datas->periodType=="days"){
  $timeToElapse=$datas->timeToElapse;
}else if($datas->periodType=="weeks"){
  $timeToElapse=($datas->timeToElapse)*7;
}else{
  $timeToElapse=($datas->timeToElapse)*30;
}
$data=array("data"=>$datas);
$rry=array("estimate"=>array_merge(impact($datas,$timeToElapse),severe($datas,$timeToElapse)));
$result=array_merge($data,impact($datas,$timeToElapse),severe($datas,$timeToElapse));
$final=json_encode($result); 
print_r($result);
/*if (isset($request->info)) {
  if ($request->info=="json") {
  	logs();
    return json_decode($final,true);  }
  if ($request->info=="logs") {
   if (file_exists("logs.json")) {
   $mylog="logs.json";
$logs=file_get_contents("logs.json");
$txt=json_decode("[".$logs."]",true);
for ($i=0; $i <count($txt) ; $i++) { 
print_r($txt[$i]['request_method']."\t\t".$txt[$i]['URL']."\t\t".$txt[$i]['response']."\t\t"."done in ".$txt[$i]['time']." "."seconds \n");
}} else{
      return "no logs"; } }
if ($request->info=="xml") {
$info=json_encode($data);
$dat=json_decode($info,true);
$array_xml = array ('xmldata' => 'xmldata',$dat,impact($datas),severe($datas),);
$xml_info = xml_info($array_xml, false );
logs();
return $xml_info->asXML();
}}else{
 logs();
 $ke=json_decode($final,true);*/
 print_r($result);
return $result;
}
function impact($datas,$timeToElapse){
	$currenlyInfected=(int)$datas->reportedCases*(10);
	$factor=(int)(((int)$timeToElapse)/3);
	$poww=pow(2, $factor);
	$infectionsByRequestedTime=($currenlyInfected * $poww);
	$severeCasesByRequestedTime=(int)($infectionsByRequestedTime*0.15);
	$val=(0.35*($datas->totalHospitalBeds))-$severeCasesByRequestedTime;
    if($val<0){$hospitalBedsByRequestedTime=(floor($val*-1)*-1);}
      else{$hospitalBedsByRequestedTime=floor($val);}
	$casesForICUByRequestedTime=(int)($infectionsByRequestedTime*0.05);
	$casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
	$avgDailyIncomePopulation=(int)$datas->region->avgDailyIncomeInUSD;
	$dollarsInFlight=$datas->region->avgDailyIncomeInUSD*$timeToElapse * $infectionsByRequestedTime*$datas->region->avgDailyIncomePopulation;
	$arrayimpact['impact']=array(
'currentlyInfected'=>$currenlyInfected,'infectionsByRequestedTime'=>$infectionsByRequestedTime,'severeCasesByRequestedTime'=>$severeCasesByRequestedTime,
'hospitalBedsByRequestedTime'=>$hospitalBedsByRequestedTime,'casesForICUByRequestedTime'=>$casesForICUByRequestedTime ,'casesForVentilatorsByRequestedTime'=>$casesForVentilatorsByRequestedTime,'dollarsInFlight'=>round($dollarsInFlight,1));
return $arrayimpact;
}
function severe($datas,$timeToElapse){
$currenlyInfected=(int)$datas->reportedCases*(50);	
$factor=(int)(((int)$timeToElapse)/3);
	$poww=pow(2, $factor);
	$infectionsByRequestedTime=($currenlyInfected * $poww);
	$severeCasesByRequestedTime=$infectionsByRequestedTime*0.15;
	$val=(0.35*($datas->totalHospitalBeds))-$severeCasesByRequestedTime;
    if($val<0){$hospitalBedsByRequestedTime=(floor($val*-1)*-1);}
      else{$hospitalBedsByRequestedTime=floor($val);}
	$casesForICUByRequestedTime=(int)($infectionsByRequestedTime*0.05);
	$casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
	$avgDailyIncomePopulation=(int)$datas->region->avgDailyIncomeInUSD;
	$dollarsInFlight=$datas->region->avgDailyIncomeInUSD*$timeToElapse * $infectionsByRequestedTime*$datas->region->avgDailyIncomePopulation;
$arrayimpact['severeImpact']=array('currentlyInfected'=>$currenlyInfected,'infectionsByRequestedTime'=>$infectionsByRequestedTime,
'severeCasesByRequestedTime'=>$severeCasesByRequestedTime,'hospitalBedsByRequestedTime'=>$hospitalBedsByRequestedTime,'casesForICUByRequestedTime'=>$casesForICUByRequestedTime,'casesForVentilatorsByRequestedTime'=>$casesForVentilatorsByRequestedTime,'dollarsInFlight'=>round($dollarsInFlight,1));
return $arrayimpact;
}
function logs(){
  $file=array("request_method"=>time(),"URL"=>"","response"=>http_response_code(),"time"=>"");
if (!file_exists("logs.json")) {
 $mylog="logs.json";
$logs=fopen($mylog, 'w');
fwrite($logs, json_encode($file));
fclose($logs);
}else{
  $mylog="logs.json";
$logs=fopen($mylog, 'a');
fwrite($logs,",".json_encode($file));
fclose($logs);}}
function xml_info( $datas, $xml=false) {
   if (!$xml) {
    $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
   }foreach($datas as $key => $value) {
    if(is_array($value)) {
      if(!is_numeric($key)){
        $node = $xml->addChild($key);
        foreach ($value as $key => $val) {
if (!is_array($val)) {
$node->addChild("$key","$val");
}else{
   $subnode= $node->addChild($key);
   foreach ($val as $key => $val1) {
   $subnode->addChild("$key","$val1");
   }} }  }else{
      xml_info($value, $xml);
      } }
    else {
      $xml->addChild("$key","$value");
    }}
  return $xml;
}
//print_r(Covid19ImpactEstimator());
?>