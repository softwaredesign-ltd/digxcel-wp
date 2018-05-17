=== digXcel ===
Contributors: ianluddy
Donate link: http://digxcel.com/
Tags: eu, gdpr, general data protection regulation, digxcel
Requires at least: 4.6
Tested up to: 4.9.5
Stable tag: 1.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A Wordpress plugin to allow the digXcel agent to access/delete data subject data stored on a Wordpress website.
The plugin allows website admins to fulfill the new EU General Data Protection Regulation legislation.

== Description ==

Plugin to allow website admins to integrate their Wordpress installation with the digXcel PDM platform.
The digXcel PDM Platform is designed to put control of personal data into the hands of its owners, the data subjects, enabling the entire process to be streamlined and automated by your organisation.
With the Data Subject Portal, your data subjects can review and manage their personal data, held by you, at any time. Data subjects can review and manage their consents, and request copies of their personal data or deletion, if required.
The Organisation Portal enables your organisation to configure and manage how your data subjects can access and manage their personal data. Subject access requests and deletion requests can be configured to be processed automatically or reviewed by authorised personnel.
The digXcel PDM Platform supports integration with third-party data processors and multiple applications, platforms and systems via our API and integration SDKs - providing a complete and unified solution for personal data management and compliance.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the digxcel Settings screen to configure the plugin:
  a. Set the 'digxcel API Key value', this can be retrieved from your digXcel Organisation Portal (Integrations > CMS Integrations)
  b. Set the 'Cookie consent widget Key' value, this can be retrieved from your digXcel Organisation Portal (Integrations > Cookie Integrations)
  c. Check the 'Cookie consent widget enabled' checkbox to optionally enable the cookie consent widget

== Frequently Asked Questions ==

= Where is the data subject data retrieved from by default? =

This data is pulled from the users table in the wordpress database by default.

= How can I implement custom data stores in my installation? =

Please see README.md for details on implementing custom datastores.

== Screenshots ==

1. Plugin configuration page
2. Cookie consent plugin

== Changelog ==

= 1.0 =
* First version

== Upgrade Notice ==

= 1.0 =
First version
