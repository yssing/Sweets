<?php
/*

* @author   Steve Winnington <stewinni@gmail.com>
* @version  1.000 03 Dec 2014
* @access   public

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

This class uses the Google to retrieve Geolocation information for a given address string

You can retrieve all the details as an Array.

Implementation Example:

$UtilsGeoAddress = new UtilsGeoAddress;
$result =$UtilsGeoAddress->lookup('my house, my town, my postcode');


if(is_array($result)){
	print_r($result);
}else{
	echo "No Geo location address found;
}

*/

interface iUtilsGeoAddress {
	public function lookup($address);
}

class UtilsGeoAddress implements iUtilsGeoAddress{
	
	private $response = null;
	private $address = null;
		
	public function __construct() {
		
		try {
			
		}
		catch (Exception $e){
			$this->exception[] = 'Exception caught on '.$e->getFile().":\n". $e->getMessage(). ' on line '.$e->getLine()."\n";
			return false;
		}
		
	}
	
	public function lookup($address){
	   
		try {
		   $this->address = $address;
		   $address = str_replace (" ", "+", urlencode($address));
		   $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false";
		 
		   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL, $details_url);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   $chexec = curl_exec($ch);
		   if (!$chexec) { throw new Exception( 'Your request has issued a malformed request.'); }
		   $response = json_decode($chexec, true);
		  
		   // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		   if ($response['status'] != 'OK') { throw new Exception( 'No response recieved ('.$this->address.'):  '.$response['status']);}
		 
			$set1 = false;
			$set2 = false;
			$set3 = false;
		   foreach($response as $key => $value){
			   foreach($value[0]['address_components'] as $key1 => $value1){
					if($value1['types'][0] == 'administrative_area_level_2' || $value1['types'][0] == 'administrative_area_level_1'){
						$currCounty = $value1['short_name'];
						$set1 = true;
					}
					
					if($value1['types'][0] == 'country'){
						$currCountry = $value1['short_name'];
						$set2 = true;
					}
					if($value1['types'][0] == 'postal_town'){
						$currTown = $value1['long_name'];
						$set3 = true;
					}
					if($value1['types'][0] == 'locality'){
						$currTown = $value1['long_name'];
						$set3 = true;
					}
					if($set1 && $set2 && $set3){
						break(2);
					}
				}	
		   }
		   $this->response = $response;
		   $geometry = $response['results'][0]['geometry'];
		 
			$longitude = $geometry['location']['lng'];
			$latitude = $geometry['location']['lat'];
		 
			$array = array(
				'lat' => $geometry['location']['lat'],
				'lng' => $geometry['location']['lng'],
				'country' => $currCountry,
				'county' => $currCounty,
				'town' => $currTown
				
			);
			return $array;
		 }
		catch (Exception $e){
			$this->exception[] = 'Exception caught on '.$e->getFile().":\n". $e->getMessage(). ' on line '.$e->getLine()."\n";
			print_r($this->exception);
			return false;
		}
	}
	
	public function getResponse(){
		
		try {
			if(!is_array($this->response)){  throw new Exception( 'No response yet recieved for: '.$this->address);	}
			return $this->response;
		}
		catch (Exception $e){
			$this->exception[] = 'Exception caught on '.$e->getFile().":\n". $e->getMessage(). ' on line '.$e->getLine()."\n";
			return false;
		}
	}
}