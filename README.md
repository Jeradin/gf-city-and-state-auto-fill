=== Gravity Forms - City and State Auto-fill add-on ===

Contributors: Jeradin
Tags: gravity forms, zip code, auto populate
Requires at least: 4.0   
Tested up to: 4.0
Stable tag: 4.3

License: GPLv2 or later 

License URI: http://www.gnu.org/licenses/gpl-2.0.html 


== Description ==
This Gravity Forms AddOn adds a setting to the address field to allow the City and State to be dynamically populated from enter a 5 digit zip code for the United States or a 6 digit Zip Code for Canada.

This is an add-on for the [Gravity Forms](http://www.gravityforms.com/)
WordPress plugin and will not provide any functionality to WordPress unless Gravity Forms is installed and activated.

= Source Repository on GitHub =
https://github.com/Jeradin/gf-city-and-state-auto-fill

= Bugs, Questions or Suggestions =
https://github.com/Jeradin/gf-city-and-state-auto-fill/issues


== Notes ==
	1. This only works for the US and Canada currently. please select one of these as the "Address Type" general settings for the Address Field.
	2. Some 5 digit zip codes don't bring up the correct city: http://semaphorecorp.com/cgi/zip5.html
		a. API only allows for 5 digits


== Installation ==

The Website Field plugin needs to be added to your themes.

	1. Download the zip file and upload as any Wordpress plugin.

== How to Use ==
Add an address field type to your form, make sure to change the "Address Type" to either United States or Canada, than go to the advanced tab and check the box that says "Populate by ZIP code"
Now when you view your form on the front end the City/State will be hidden until the user enters a ZIP code.

== Frequently Asked Questions ==

= I've activated the plugin, but nothing happens! =

Make sure you have [Gravity Forms](http://www.gravityforms.com/) installed and
activated. This is not a standalone plugin for WordPress, it only adds additional functionality to Gravity Forms.

== Future Enhancements ==

1. Add more countries with Postal Codes and validation. http://bootstrapvalidator.com/validators/zipCode/
2. Validation the field live http://www.zippopotam.us/static/sample_us.html

== Screenshots ==

1. Field Advanced settings.


## Changelog ##
### 1.0
* Initial Release
