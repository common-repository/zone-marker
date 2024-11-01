=== Zone Marker ===
Contributors: Andy Gilbert
Donate link: https://buy.stripe.com/28o28Y1do5e9gOA6op
Tags: drone, map, photography, video, boundary
Requires at least: 4.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables a user to highlight on a map a given area for submission via email to the website admin. Useful for aerial photography or boundary knowledge.

== Description ==
Do you struggle to understand the area which your client is trying to explain? You could be a drone pilot who need to know the exact location of where you need to video, or it could be some boundary issue that needs highlighting or a crop to spray.
Zone Marker enables the website user to mark out the exact area that is under discussion, then emails the details to the site admin so you can view the area and make informed decisions on how to proceed. This could be to formulate a quote or just to help make plans.
Use the shortcode [zonemarker] in your page or post.

=== Pro Version ===
This is still in development. But expect the options to choose the email address of the recipient. The ability to change the colours of the zone marker. The option to choose the point markers.

== Installation ==

=== From within WordPress ===
1. Visit Plugins -> Add New
1. Search for 'Zone Marker'
1. Install
1. Activate Zone Marker from your Plugins page
1. Folllow the manual instructions shown after activation

=== Manually ===

1. Upload the directory `zone-maker` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings -> Zone Maker and configure the settings and layout. You will need a Google Map API key. There are instructions under the Settings tab. Add default latitude and longitude values up to 6 decimal points. Otherwise the map defaults to centring on the London Eye.
1. In the Layout tab set the heading, & Intro. Below these you can set the checkbox labels i.e. Aerial Photography, Aerial Video, Aerial Mapping, Heat Map
1. 'Show submitted' displays the content of the email that is sent to the website's admin


== Frequently Asked Questions ==
None

== Changelog ==
v 1.0.0 release version 2019/05/23
v 1.0.1 Prevented the form from displaying in the back end 2019/05/23
v 1.0.2 Adding '-' to the regex in the admin 2020/09/23
v 1.0.3 Changing the way the content is flushed 2020/10/02
v 1.0.31 Added namespace, fixed an issue with a couple areas not translating 2021/03/13
v 1.0.32 Added the co-ordinates to the email 2021/03/18
v 1.0.33 Added the area of the polygon to the screen and email 2021/03/18
v 1.0.34 Removed the background colour and top border 2021/03/20
v 1.0.35 Tweak to the way in which the output is handled 2021/03/20
v 1.0.36 Added a Google Maps link to show the location. Useful if it's not clear from the embedded map. The facility for showing the polygon is not possible in Google maps 2021/03/21
v 1.0.37 Change brief description to optional. Remove 'Area' display if less than 3 points. Change the Google link from .co.uk to .com. Round the area to 0 descimal places. Added submitting URL to email.
v 1.0.4 Release version containing the updates since version 1.0.3 2021/04/07
v 1.0.41 Added missing namespace to the activate & deactivate hooks, it was causing a warning on activation 2021/04/12
v 1.0.42 Tweak to only load the scripts on the pages/posts that use the plugin 2021/05/20
v 1.0.43 Google map link fixed. Required version of PHP downgraded to 7.2 2021/06/10
v 1.0.44 WP version update and removal of HTML comment 2022/10/17
v 1.0.45 WP version update (6.4.1) and tested to PHP 8.2 2023/11/24
v 1.0.46 WP version update (6.5) and tested to PHP 8.2 2024/04/09

== Upgrade Notice ==
A Pro version is in development and will bring several sought-after features

== Screenshots ==
1. The frontend
2. The backend Settings screen
3. The backend Layout screen