=== Easy Listings Map ===
Contributors: c0dezer0
Donate link: http://codewp.github.io/easy-listings-map/
Tags: easy property listings, epl, easy property listings extension, easy property listings extensions, easy property listings map, easy property listings google maps, epl extension, epl extensions, epl map, epl google maps, property listings, property management, real estate, real estate connected
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use and advanced map extension for Easy Property Listings Wordpress plugin.

== Description ==

Easy Listings Map is an easy to use, advanced and free map extension for Easy Property Listings which allows site owners to add Google Maps to their site that shows listings in the map.

This extension also allows ability to show Google Maps for listing in single listing page.

Requires [Easy Property Listings](https://wordpress.org/plugins/easy-property-listings/)

Features of the plugin include:

* Google maps shortcode for showing listings in the map.
* Google maps in the single listing page.
* Clustering listings in the map.
* Showing custom markers in the map that user uploaded.
* Ability to show specific listings in the map that site owner choosed.
* Ability to customize map size.
* Ability to show details of listing in the map ( image of the listing, ... ).
* Ability to show details of the listings in the map that are in same position( tabbed Google Map info window ).
* Ability to auto zoom in the map for showing more listings in the map.
* Compatible with all of Easy Property Listings versions.
* A fast and efficient plugin written using WordPress standards.

More information at [Easy Listings Map](http://codewp.github.io/easy-listings-map/?utm_source=readme&utm_medium=description_tab&utm_content=home_link&utm_campaign=elm_home).

== Installation ==

1. Upload `easy-listings-map` to the `/wp-content/plugins/` directory.
2. Activate `easy-property-listings` in your wordpress site if it is not activated already.
3. Activate `easy-listings-map` through the 'Plugins' menu in WordPress.
4. For detailed setup instructions, vist the official [Documentation](http://codewp.github.io/easy-listings-map/doc/) page.

== Frequently Asked Questions ==

= How Easy Listings Map works? =

**Easy Listings Map** works by means of coordinates of **listings**, so if a **listing** has not filled coordinates field it will not shown in the map of **Easy Listings Map** in other words if you want to **listings** shown in the map you should add coordinates of them exactly.

= Why Easy Listings Map works by coordinates of listings? =

Because it is a best way to showing **listings** in the map from speed view. If it uses address of **listings** to showing them in the map it will reduce speed because address needs to geocoded and geocoding listings address will reduce speed of the site and map loading.

= How can I show listings in the Google Maps? =

**Easy Listings Map** has a shortcode for Google maps for showing listings in the map, so you can use this shortcode for showing listings in the map, also this shortcode has a user interface for adding it pages or posts. For detailed information refer to [Adding Listings Google Maps](http://codewp.github.io/easy-listings-map/doc/plugin-shortcodes/#how-to-create-a-map-for-showing-listings-).

= How can I customize dimension of Google Maps? =

* For customizing dimension of single listing page map refer to [single listing page map dimension](http://codewp.github.io/easy-listings-map/doc/plugin-settings/#general-tab-items-description-).
* For customizing dimension of Google Maps listings shortcode refer to [listings map dimension](http://codewp.github.io/easy-listings-map/doc/plugin-shortcodes/#shortcode-form-items-description-).

= Is it possible to customize markers in the Google Maps shortcode? =

Yes it is possible, please refer to [customizing markers](http://codewp.github.io/easy-listings-map/doc/plugin-settings/#markers-tab-of-settings-menu-).

== Screenshots ==

1. General settings
2. Marker settings
3. Google Maps listings shortcode form
4. Listings Google Maps
5. Google Maps listing info window
6. Single listing page Sattelite view map
7. Single listing page Roadmap view map

== Changelog ==

= 1.1.1 =

* Fix : An issue that cause to not loading some of listings that are in bound of the map.
* Fix : An issue that cause to not showing listings on the map when auto zoom feature enabled.
* New : A feature added to not loading markers when all of them loaded to the map already.

= 1.1.0 =

* New: Making maps responsive.
* Fix: An issue in the Google Maps shortcode when setting it's title that cause map doesn't shown.

= 1.0.1 =

* Fix: issues of the plugin in wordpress 3.3

= 1.0.0 =

* First offical release!
