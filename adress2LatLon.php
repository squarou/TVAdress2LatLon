<?php
/**
 *  Name 	adress2LatLon
 *   $id 	The ID of the resource being published.
 *   $resource 	A reference to the modResource object being published.
 *   $templateId The id of the template the TVs are assigned to.
 *   $email the email of the site administrator
 *   
 *   TODO :
 *    - a TV can be assigned to multiple templates => use array and check if $resource->get('template') is in $templatesArray
 * 
 * 
 * 
 **/

$templateId = 1;
$email = 'mail@domain.tld';

$resource = $modx->getObject('modResource', $id);
switch ($modx->event->name) {
 
    // Documents
    case 'OnDocFormSave':
        if ($resource->get('template') == $templateId) {  
            if ($resource->getTVValue('geolocalisation') != '') {
                                                           
                $query = $resource->getTVValue('query');//on récupère la TV
                $query = rawurlencode($query);
                
                $baseUrl    = 'http://nominatim.openstreetmap.org/search?limit=1';
                $format     = 'jsonv2'; // see https://wiki.openstreetmap.org/wiki/Nominatim
                $fullUrl    = "{$baseUrl}&format={$format}&q={$query}&email={$email}";
                
                // lets get the data from Nominatims
                $data       = file_get_contents($fullUrl);
                
                            
                if ($data == "[]") {
                    $modx->toPlaceholder('notfound', '<span class="smaller grey">No location found.</span>');
                }
                               
                $jsonArray  = json_decode( $data );
                
                foreach ($jsonArray as $item) {
                    //print_r($json[0]);
                    $name = $item->display_name;
                    $lat = $item->lat;
                    $lon = $item->lon;
                    //print $lon;
                }
                //set lat lon tv
                if(!$resource->setTVValue('latitude', $lat)) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'There was a problem setting the TV value.'.$lat);
                } else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Latitude ='.$lat);
                }
                if(!$resource->setTVValue('longitude', $lon)) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'There was a problem setting the TV value.'.$lon);
                } else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'Longitude ='.$lon);
                }
            }
            else {
                if(!$resource->setTVValue('latitude', '')) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'There was a problem setting the TV value.'.$lat);
                } else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'No need latitude');
                }
                if(!$resource->setTVValue('longitude', '')) {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'There was a problem setting the TV value.'.$lon);
                } else {
                    $modx->log(modX::LOG_LEVEL_ERROR, 'No need longitude');
                }
            }
        }
    break;
}
return;
