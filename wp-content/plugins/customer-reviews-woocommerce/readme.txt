=== Customer Reviews for WooCommerce ===
Contributors: ivole
Tags: woocommerce, review plugin, review reminder, customer reviews, review for discount
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 3.16
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html

Customer Reviews for WooCommerce plugin helps you get more sales with social proof. Set up automated review reminders and increase conversion rate.

== Description ==

Customer Reviews for WooCommerce plugin helps you get more sales with social proof. Encourage your customers leave product reviews and increase conversion of your shop. This WooCommerce review plugin enables you to set up automatic review reminders for customers who recently purchased a product from your shop. Reminder emails are sent to your customers inviting them to review the recent product(s) they purchased.

=== Features ===

Major features of Customer Reviews for WooCommerce include:

* Review reminder
* Aggregated review form
* Enhanced customer reviews
* Review for discount

=== Review Reminder ===

Receive more authentic reviews from your real customers by sending automated invitations to submit a review. You will receive feedback from customers who never bother to answer surveys or submit reviews.

* Increase sales with social proof
* Receive reviews for several products at once by asking customers to answer a one-page review form
* Get great unique SEO content for your shop written by your customers
* Send automated email invitations asking customers who recently purchased for a review
* Send email invitations manually for selected orders
* Personalize emails for each customer with built-in variables
* Restrict emails to particular categories of products
* Works out of the box by using a responsive email template with custom colors
* Unsubscribe option
* Built-in testing tool to make sure that emails look beautifully before sending them
* Reminders in different languages via an integration with "qTranslate X" plugin

=== Aggregated Review Form ===

Let your customers review all the products from their orders on a single page.

* Automatically generate review forms for each WooCommerce order
* A single review form includes questions about all products in the order, so a customer will review several products at once
* After submission of the review form, the plugin will transfer reviews to pages of individual products
* Review forms are stored as static HTML files to ensure the fastest page load speed
* Review forms are optimized for different screen sizes (including mobile)

=== Enhanced Customer Reviews ===

Enhance the standard WooCommerce reviews with additional features.

* Enable customers to attach pictures to reviews
* Enhance rich snippets and structured data for reviews with pictures (requires WooCommerce 3.0 or newer)
* Prevent SPAM by enabling reCAPTCHA for reviews
* Show reviews summary bar on product pages
* Filter reviews by rating
* Built-in [cusrev_reviews] shortcode to display reviews inside post, page or widget. You can use this shortcode as [cusrev_reviews comment_file =”/comments.php”] or simply as [cusrev_reviews] on product pages. Here, 'comment_file' is an optional argument. If you have a custom comment file, you should specify it here.

=== Review for Discount ===

Stimulate your customers to leave reviews and increase their lifetime value by offering discount codes. Send coupons to customers who reviewed their purchases to keep them engaged with your shop. It will help to increase repeat purchases and up-sells.

* Automatically generate new coupons for customers who review their purchases
* Automatically send emails with newly generated coupon or an existing coupon upon submission of a review
* Personalize emails with coupons for each customer using built-in variables
* Fine-tune properties of coupons according to your sales strategy
* Works out of the box by using a responsive email template with custom colors
* Built-in testing tool to make sure that emails look beautifully before sending them
* Emails with coupons in different languages via an integration with "qTranslate X" plugin

=== How does it work? ===

Customer Reviews for WooCommerce plugin works as follows:

1. A customer makes a purchase from your online shop
2. You process their order and set status of the order as “Completed”
3. After a certain delay (configured in the options of the plugin), the customer will receive an email with an invitation to leave a review
4. The customer writes reviews about products using a simple form
5. The customer receives an email containing a discount coupon for future purchases in your shop

=== Supported Languages ===

* Czech
* Danish
* Dutch
* English
* Finnish
* French
* German
* Hungarian
* Indonesian
* Italian
* Portuguese
* Romanian
* Russian
* Serbian
* Slovenian
* Spanish
* Swedish
* You can contribute a translation to your language from the settings page of this plugin

=== Prerequisites ===

To use this plugin, you should first do the following:

1. Install and configure WooCommerce
2. Enable customer reviews for product pages (they are enabled by default for new installations)

=== Premium Version ===

The plugin has a premium version that offers a possibility to white label the plugin and dedicated support by email. You can purchase a license for the premium version here: [Official Customer Reviews Plugin Website](https://www.cusrev.com).

== Installation ==

1. Make sure that WooCommerce plugin is installed and activated. If it is not installed, install [WooCommerce](https://wordpress.org/plugins/woocommerce/) first because it is necessary for this plugin.
2. Upload the plugin files to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Use the WooCommerce->Settings->Reviews screen to configure the plugin

== Frequently Asked Questions ==

= I found a bug in the plugin. How to get it fixed? =

Please create a new topic at the support forum and provide detailed information with a screenshot.

= How to get Site Key and Secret Key for reCAPTCHA? =

Please visit [reCAPTCHA website](https://developers.google.com/recaptcha/docs/start) and sign up for an account. Then, you will be able to get API key pair (Site Key and Secret Key) for your website. Copy these keys and paste into the settings of the plugin.

= Will collected reviews be shown as stars in Google? =

The standard WooCommerce functionality already includes structured data mark up to display your product reviews effectively within organic search results. This plugin extends the standard functionality and adds some extra mark up to help search engines properly crawl your shop. It is important to understand that having a valid structured data mark up in place makes your website eligible for organic stars in Google but it doesn't guarantee that they will be shown. You can test the rich snippets using [Google’s Structured Data Testing Tool](https://search.google.com/structured-data/testing-tool).

== Screenshots ==

1. A sample email with an invitation to review products.
2. A sample review form generated by the plugin.
3. A sample email with a discount code.
4. The reviews summary bar.
5. A button to attach pictures to reviews.
6. reCAPTCHA field to prevent fake reviews.
7. A customer review with a picture.
8. The first settings page.
9. The second settings page.
10. The third settings page.
11. The fourth settings page.
12. WooCommerce Orders page with manual reminders option enabled.

== Changelog ==

= 3.16 =
* New translation: Russian
= 3.15 =
* New feature: let customers control their privacy by choosing how their names are shown next to reviews
= 3.14 =
* New feature: added structured data mark up for reviews with images (associatedMedia, contentUrl)
= 3.13 =
* New feature: option to remove plugin's branding from the reviews summary bar on product pages (requires premium version)
* Important bug fixes
= 3.12 =
* Updated text domain
= 3.11 =
* New feature: customize shop name (previously, it was defaulted from WordPress Site Title)
* New translation: Indonesian
* New translation: Italian
* New translation: Romanian
= 3.10 =
* New feature: reviews summary bars for product pages
= 3.9 =
* New feature: if there are several variations of the same product in the order, the plugin will ask to review only the parent product
= 3.8.3 =
* New translation: Danish
* New translation: Serbian
= 3.8.2 =
* Bug fix: reCAPTCHA was required on admin pages when answering to reviews
* WordPress 4.9 compatibility
= 3.8 =
* New translation: Dutch
* New translation: Finnish
* New translation: Hungarian
* New translation: Slovenian
* Compatibility with W3 Total Cache plugin (DB caching)
* Additional check that Site Title is not empty when sending test emails
* New feature: modify "From" email address (requires premium version)
* New feature: modify "From" name (requires premium version)
* New feature: modify text in footer of emails (requires premium version)
* New feature: a dedicated tab for manually created coupons on the standard WooCommerce coupons page
= 3.7 =
* New translation: Czech
* New translation: French
* New translation: German
* New translation: Portuguese
* New translation: Spanish
= 3.6 =
* New feature: translate all parts of emails and review forms to your language
* New feature: option to make comments required when leaving a review
* Bug fix: number of stars wasn't properly updated after receiving a comment
= 3.5 =
* Resolves the problem with submission of reviews on sites without pretty permalinks
* New feature: integration with "qTranslate X" plugin for multilingual shops
= 3.4 =
* New feature: manual sending of review reminders
= 3.3 =
* More detailed reporting of errors when sending test emails
= 3.2 =
* New feature: customize colors of emails
* Bug fixes
= 3.1 =
* New feature: upload multiple images with a review
* New feature: images attached to reviews are opened in a lightbox
= 3.0 =
* Major update - you must check settings and test sending of emails immediately after updating the plugin
* New feature: responsive template for emails with review reminders and discount coupons
* New feature: sending of emails with review reminders and discount coupons is performed by Amazon SES to increase deliverability
* New feature: a customer will be able to review all products from their order on a single page using a stylish and lightweight form that loads blazingly fast
* New feature: specify reply-to address for emails with review reminders and discount coupons
* New feature: enable moderation of reviews submitted by customers in response to reminders
* New feature: a custom shortcode to display reviews inside post, page or widget
* Several email variables were discontinued
* Bug fixes
= 2.6 =
* Added support for ALTERNATE_WP_CRON
= 2.5 =
* Bug fixes and improvements
= 2.4 =
* Improvements in sending emails for old WooCommerce versions
= 2.3 =
* Additional improvements in compatibility with old WooCommerce versions
= 2.2 =
* Improved compatibility with old WooCommerce versions
= 2.1 =
* Bug fixes
= 2.0 =
* Added review for discount feature - automatically generate and send coupons to customers who left a review
= 1.5 =
* Added possibility to attach pictures to reviews
* Added support of reCAPTCHA to prevent fake reviews
* Updated settings page
= 1.4 =
* Bug fixes
= 1.3 =
* Bug fixes
= 1.2 =
* Bug fix for a missing file
= 1.1 =
* Bug fix for the error when WooCommerce is not installed
= 1.0 =
* Initial release.
