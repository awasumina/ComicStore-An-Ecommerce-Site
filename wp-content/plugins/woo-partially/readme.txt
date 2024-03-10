=== Woo Partial.ly ===
Tags: payment plan,partial.ly,woocommerce,payment plans,layaway,instalment
Requires at least: 4.4
Tested up to: 6.2
Stable tag: 2.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Partial.ly allows you to offer easy and flexible payment plans to your customers

== Description ==

The Woo Partial.ly plugin for WooCommerce online stores is packed with features to help you grow sales and make payment easier. Set down payment, frequency and duration of payment plans to make it easier for customers to make those big ticket purchases at a rate that suits their finances.

* Seamlessly integrates a flexible payment plan offer directly into your checkout process
* Let customers customize plans within the limits that you set
* Automates payment processes so cash arrives in your bank account as scheduled

You can get started quickly through easy to follow instructions that make it really simple to install Partial.ly on your WooCommerce store. And if you get stuck, we’ve got great support.

* Costs nothing to sign up, no credit card required and no fixed or monthly fees
* No Stripe payment processing fee – included per transaction cost of 5% +$0.30
* Partial.ly is used by over 19,000 businesses in 30 countries so you’re in good company!

= Outstanding advantages for WooCommerce stores =

= Easy automated payments =

Automate your collections and bill customers exactly according to your specifications with scheduled, automated payment plan payments.

= Customers customize plans =

Increase sales by offering payment plan terms that work for both you and your customers. Let customers customize plans within the limits that you set.

= Flexible manual payments =

Process already scheduled payments early, or process a custom payment in any amount and the remaining payment schedule is adjusted automatically.

= Global ecommerce-ready =

Partial.ly is available for businesses in 30 countries around the world from Australia to the US, with more being added all the time. [See here](https://stripe.com/global) for a list of available countries.

= Total payment plan control =

Set the down payment, frequency, term and any additional line item fees. Set your plans to fit perfectly with your business processes.

= Industry leading security =

Partial.ly features industry leading encryption and security best practices. We do not store any customer payment information.

= Convenient merchant portal =

Easily manage all of your payment plans, process payments, open payment plans and monitor payment plan activity.

= Automate communication =

Automated emails for all payment plan activity to both merchants and customers makes it
easy to manage payment plans.

= Cash in the bank! =

Monitor your payment performance in Partial.ly and have your payments automatically deposited into your bank account.

[Visit Partial.ly.com for full details on hassle-free flexible payment processing](https://partial.ly/register?ga_campaign=woocommerce_plugin) today!

== Installation ==

= Minimum Requirements =

* WordPress 4.4+
* WooCommerce 2.6+

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of Partial.ly, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type “Partial.ly” and click Search Plugins. Once you’ve found our plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading our plugin and uploading it to your webserver via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

[Full instructions on how to configure are on the WooCommerce support pages.](https://support.partial.ly/woocommerce-plugin)

== Frequently Asked Questions ==

= Does it cost anything? =

No. Partial.ly is free to sign up, you are only charged when a transaction occurs.

= What are the requirements? =

A Stripe account.

= Are Stripe fees included? =

Yes, Stripe processing fees are included in the application fee.

= Where can I find more information? =

See our [website](https://partial.ly/?ga_campaign=woocommerce_plugin) for more information or our [woocommerce support page](https://support.partial.ly/woocommerce-overview) for WooCommerce specific help

== Screenshots ==

1. Partial.ly settings in WooCommerce admin
2. More Partial.ly settings
3. The Partial.ly widget on product landing page
4. The Partial.ly checkout button on cart page
5. Partial.ly payment option at checkout

== Changelog ==

= 2.2.5 =
* support woocommerce high-performance order storage (HPOS)
* support for order fees added to payment plans *

= 2.2.4 =
* fixed wordpress warning on order details page

= 2.2.3 =
* checkout button config for existing users

= 2.2.2 =
* allow updating deprecated fields for users who had them set

= 2.2.1 =
* restore per product customizations for widget only

= 2.2.0 =
* support for new Partial.ly advanced scripting
* Partial.ly test mode
* better error messages
* per product customizations and min/max deprecated in favor of advanced scripting
* if you were utilizing the minimum and maximum amounts feature or set a custom offer for a product, please setup advanced scripting with Partial.ly before upgrading the plugin
* checkout buttons deprecated in favor of native payment method

= 2.1.18 =
* fix Partially notifications for wordpress sites served under subdirectories
* compatibility with WordPress 6.1 and WooCommerce 7.x

= 2.1.17 =
* handle errors getting offers from Partially API

= 2.1.16 =
* fix return URL for product landing pages

= 2.1.15 =
* compatibility with WordPress 5.9 and WooCommerce 6.2

= 2.1.14 =
* compatibility with WordPress 5.8 and WooCommerce 5.6

= 2.1.13 =
* bugfix to make sure woo cart exists *

= 2.1.12 =
* compatibility with WordPress 5.6 and WooCommerce 5.0

= 2.1.11 =
* improved plugin detection for multisite installations

= 2.1.10 =
* use WooCommerce order statuses
* add partially_order_created_status filter for ability to customize Partial.ly order status

= 2.1.9 =
* fix for WP multisite installations

= 2.1.8 =
* improved customer error messages at checkout
* compatibility with WordPress 5.5
* compatibility with WooCommerce 4.3

= 2.1.7 =
* fixed a breaking change for php versions <= 5.6

= 2.1.6 =
* support custom thank you pages by updating order status via webhook notification
* include plugin version number in payment plan metadata

= 2.1.5 =
* compatibility with WordPress 5.4

= 2.1.4 =
* fixed creating orders with "paid" status, using WooCommerce completed status instead

= 2.1.3 =
* tested with WooCommerce v4.0

= 2.1.2 =
* fixed bug that broke WordPress menu editor

= 2.1.1 =
* use the WordPress configured language for Partial.ly checkout

= 2.1.0 =
* override offer per product
* disable Partially per product
* configure minimum amount for Partially
* configure maximum amount for Partially
* configure Partially checkout button image

= 2.0.7 =
* use new wc_reduce_stock_levels function

= 2.0.6 =
* improved support for WordPress multisite installations

= 2.0.5 =
* fixed bug with some order confirmed filters

= 2.0.4 =
* fixed bug with line item subtotal

= 2.0.3 =
* no default offer

= 2.0.2 =
* partially_gateway_settings filter to override gateway checkout settings
* optionally disable sending product thumbnail images to Partial.ly
* fixed bug setting custom order status after Partial.ly gateway purchase

= 2.0.1 =
* fixed bug with incorrect line item total display

= 2.0.0 =
* New Partial.ly gateway for WooCommerce
* Partial.ly product widget
* improved Partial.ly button
* improved documentation and screenshots

= 1.0.2 =
* improved title of line items with product variations
* fixed price of line items with product variations
