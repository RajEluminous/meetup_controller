<?php

namespace Drupal\meetup_controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController {

  public function page($city) {
	//echo $city;
	//echo '<pre>';
	//print_r($request->get('city'));
  
    $items = array();

###########################################################
###############    ONE TIME CONFIGURATION    ##############
###########################################################
$radius = "10";
$cityval = strtolower($city);
switch ($cityval) {
    case "california":
		$longitude = "-119.41793239999998";
		$latitude = "36.778261";
        break;
	case "sydney":
		$longitude = "151.209900";
		$latitude = "-33.865143";
        break;	
    case "chicago":
        $longitude = "-87.62979819999998";
		$latitude = "41.8781136";
        break;
    case "boise":
        $longitude = "-116.20231369999999";
		$latitude = "43.6150186";
        break;
    default:
        $longitude = "-119.41793239999998";
		$latitude = "36.778261";
}
//echo '<br>longitude:'.$longitude;	 
//echo '<br>latitude: '.$latitude;

//$longitude = "-74.0059728";
//$latitude = "40.7127753";

##########################################################
#########          DO NOT CHANGE             #############
##########################################################

$meetupURL = "https://api.meetup.com/find/upcoming_events?photo-host=public&lat=".trim($latitude)."&lon=".trim($longitude)."&radius=".(int)trim($radius)."&topic_category=2&order=time&page=200&offset=0&key=7b3d38771a7557c241f1922117c974";

$cURL = curl_init();
curl_setopt($cURL, CURLOPT_URL, $meetupURL);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
$json_string = curl_exec($cURL);
$json = json_decode($json_string, true);

$cityVal = '';

// if events available, set timezone.
if(isset($json['events']) && count($json['events'])>0) {
	$vals = current($json['events']);
	$cityVal = 'Events: '.$json['city']['city']; 
	//date_default_timezone_set($vals['group']['timezone']);
	//$t=time();
	//echo($t . "<br>");
	//echo(date("Y-m-d H:i:s",$t));
}	
	//print_r($json);
 	
	if(isset($json['events']) && count($json['events'])>0) {
		foreach($json['events'] as $evt) {
			
			$rs_event_name =  (isset($evt['name']) && !empty($evt['name']))? $evt['name']:'';
			$rs_event_link =  (isset($evt['link']) && !empty($evt['link']))? $evt['link']:'';
			$rs_event_local_date =  (isset($evt['local_date']) && !empty($evt['local_date']))? $evt['local_date']:'';
			$rs_event_local_time =  (isset($evt['local_time']) && !empty($evt['local_time']))? $evt['local_time']:'';
			$rs_event_yes_rsvp_count =  (isset($evt['yes_rsvp_count']) && !empty($evt['yes_rsvp_count']))? $evt['yes_rsvp_count']:'';
			$rs_event_description = (isset($evt['description']) && !empty(strip_tags($evt['description'])))? strip_tags($evt['description']):'';
			
			$rs_desc_viewmore = '';
			if(!empty($rs_event_description)) {
				//$rs_desc_viewmore = wordwrap(substr($rs_event_description,0,300),50,"<br>\n",TRUE).'...<a href="'.$rs_event_link.'" target="_blank">View more</a>';
				$rs_desc_viewmore = wordwrap(substr($rs_event_description,0,200),50,"<br>\n",TRUE);
				if(strlen($rs_desc_viewmore)>200){
					$rs_desc_viewmore = $rs_desc_viewmore.'...';
				}
			}
			
			array_push($items,array("name" => $rs_event_name,
									"link" => $rs_event_link,
									"local_date" => $rs_event_local_date,
									"local_time" => $rs_event_local_time,
									"yes_rsvp_count" => $rs_event_yes_rsvp_count,
									"description" => strip_tags($rs_desc_viewmore)
				
			));
		}
    }
    return array(
      '#theme' => 'article_list',
      '#items' => $items,
      '#title' => $cityVal  
    );
  }
}
