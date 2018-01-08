=== Plugin Name ===
Contributors: jonimo
Tags: redirect, login, logout, buddypress, multisite, profile, page, tag, user roles, custom, link, limit, profile, home page, friends, activity, woocommerce, shop, offer, ecommerce, sales, marketing, category, custom url
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily redirect users with specific roles to any url, page, tag or category a set number of times when they login or logout.

== Description ==

[jonimo](http://jonimo.com) simple redirect helps your users with different roles get to the right place when they login or logout of your WordPress, WooCommerce or BuddyPress site.

* Set any url (internal or external) to redirect users to on login or logout.
* Redirect users with specific roles to a location just once, or up to nine times.
* After a user has been redirected the required number of times on login, they are automatically redirected to a default location
* The default location is where users will be redirected if no custom location is specified. The default location can be any internal or external url, tag, page or category.
* On logout redirect users to any internal or external url.
* Fully compatible with WordPress 3.9 and BuddyPress 1.9.2 You can give your users an experience more similar to popular social networks by redirecting them to 
their personal profile pages, their 'friends' menu or the activity stream.
* Fully multisite compatible, giving each site administration control over where the different users of their site are redirected to
* Extendable and built with developers in mind. It's easy to change the default redirect behaviour using custom filters.

= Works with WooCommerce  =

* [jonimo simple redirect](http://jonimo.com/product/jonimo-simple-redirect-pro) is 100% compatible with Woocommerce, meaning you can login using the woocommerce login form and still redirect users a set number of times to a specific location 
* NEW With Woocommerce -> Example: Redirect each user just once to an offer page on login or logout
* NEW With Woocommerce -> Example: Redirect each user with a specific role to a set product category just once on login, and then to any other location on logout.
* NEW With Woocommerce -> Example: Always redirect different users on logout to a thank you for buying screen 
* Free support for 1 year


= Use examples for a none WooCommerce site.

* On login, redirect subscribers to an welcome page just once, and then to the homepage.
* On login, redirect users to any url before reverting to the default location the next time a user logs in.
* On login, redirect BuddyPress users to their profile edit screen a set number of times before redirecting them to their profile.  
* On login, redirect users to a specific blog article just once when they login and then after that to another location. 
* On login, encourage users to accept updates to terms and conditions.   
* Always redirect users to a specific location.
* On logout, redirect users to any location, including external sites


If you have any questions, or require support, let us know at [jonimo](http://jonimo.com/forums/support "jonimo support")

== Installation ==

To install:

1. Upload `jonimo_simple_redirect` to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You should be able to see a top level menu called 'Redirect Settings' appear in your administration panel. 
1. Select login, then select your role, destination and the number of times you want your users to be redirected. 
1. Select the logout menu and select one area to redirect all users to on logout 
1. Select default login and set a location to redirect to if no custom location is defined. 

== Frequently Asked Questions ==

For complete support or if you want any features added, get in touch at [jonimo](http://jonimo.com/forums/support "jonimo support")

= Are there any known conflicts with other plugins?  =

Any other plugin that attempts to redirect users on login or logout may conflict with this plugin. Likewise, any plugin that uses a 
custom login script may cause jonimo simple redirect to be ineffective. 
We are looking at ways to ensure as wide a compatability as possible in upcoming releases. If you have any problems, please let us know at
[jonimo](http://jonimo.com/forums/support "jonimo support")


= Is this plugin compatible with BuddyPress? =

Yes. If you are using BuddyPress you can give your users an experience more similar to popular social networks by redirecting them to 
their personal profile pages, their 'friends' menu or the activity stream. If you would like other possible areas included, please let us know.  

= Is this plugin compatible with multi-site installations? =

Yes. jonimo simple redirect is compatible with multi-site installations. Each site owner has control over where their users are directed to. 
An option for the superadmin to optionally override this for all sites is coming in the next release.

= Can I get support? =

Yes. For support within 48 hours, five days a week, visit [jonimo](http://jonimo.com/forums/support "jonimo support")

= Can I easily extend? =

Yes. We have provided several useful filters to give developers lots of options if further functionality needs to be added.


== Screenshots ==

1. On the login options screen you can choose where to redirect different users based on their role.
2. On the Default options screen you can set a location to redirect to if no custom location is set.
3. On the logout options screen you can direct all users to a common location.

== Changelog ==

= 1.5 = 
* Now compatible with WordPress 3.9
* You can now use with WooCommerce as standard
* Bug Fixes

= 1.4.1 = 
* You can now specify any url as the default login link. 


= 1.4 = 
* Added ability to redirect different users to any web address on login or logout.

= 1.3.1 = 
* Bug fix

= 1.3 = 
* You can now add the number of times a user can redirect to a given location, before they redirect back to the default destination.
* Improved messaging 


= 1.2 = 
* You can now select a unique default place for users to be redirected to on login if no custom destination has been set for their role.
* Improved messaging 

= 1.1 = 
* You can now select different redirect destination for users based on their role.
* Tested on BuddyPress 1.9 and 3.8
* Added more filters for developers

= 1.0 =
* New release

