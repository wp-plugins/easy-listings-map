<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class responsible for functions related to locations
 *
 * @since 	   1.0.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Location {

	/**
	 * Is latitude and longitude inside of bound or not
	 *
	 * @since 1.0.0
	 * @param  float  $latitude
	 * @param  float  $longitude
	 * @param  float  $bound_south_west_lat
	 * @param  float  $bound_south_west_lng
	 * @param  float  $bound_north_east_lat
	 * @param  float  $bound_north_east_lng
	 * @return boolean
	 */
	public function is_in_bound( $latitude, $longitude, $bound_south_west_lat, $bound_south_west_lng,
		$bound_north_east_lat, $bound_north_east_lng ) {
		// Checking against latitudes first.
		if ( $latitude >= $bound_south_west_lat && $latitude <= $bound_north_east_lat ) {
			// Checking against longitudes late.
			$longitude = $longitude == -180 ? 180 : $longitude;

			return $bound_south_west_lng > $bound_north_east_lng ?
				( $longitude >= $bound_south_west_lng || $longitude <= $this->$bound_north_east_lng ) && ( $bound_south_west_lng - $bound_north_east_lng != 360 ) :
				$longitude >= $bound_south_west_lng && $longitude <= $bound_north_east_lng;
		}

		return false;
	}

}
