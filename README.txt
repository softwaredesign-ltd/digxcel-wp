=== GDPR - WP Plugin ===
Contributors: ianluddy, vicpada
Donate link: http://digxcel.com/
Tags: eu, gdpr, general data protection regulation, digxcel, privacy, data, software design
Requires at least: 4.6
Tested up to: 4.9.5
Stable tag: 1.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple GDPR compliance.

== Description ==

The digXcel GDPR plugin allows any Wordpress-powered website to integrate with the [digXcel PDM platform](http://digxcel.com), easily enabling organisations to become GDPR compliant.

The plugin also empowers website visitors to grant or revoke cookie consent.


**GDPR**

GDPR stands for General Data Protection Regulation. It is the European Union’s new regulation that safeguards the personal data of individuals in the EU, as well as the export of personal data. This means that it doesn’t just affect the European continent, but also businesses around the world that deal with information of European citizens. At the time of writing, the GDPR also includes the United Kingdom (UK), despite recent Brexit changes.

The GDPR defines ‘personal data’ as: *“any information related to a natural person or ‘Data Subject’, that can be used to directly or indirectly identify the person. It can be anything from a name, a photo, an email address, bank details, posts on social networking websites, medical information, or a computer IP address.”*

The purpose of the GDPR is to ensure that the privacy of EU citizens is protected. It is to provide new ‘digital rights’ to consumers, and to secure consequences for the misuse of sensitive information.


**The digXcel platform**

The digXcel PDM Platform is designed to put control of personal data into the hands of its owners, the data subjects, enabling the entire process to be streamlined and automated by your organisation.
With the Data Subject Portal, your data subjects can review and manage their personal data, held by you, at any time. Data subjects can review and manage their consents, and request copies of their personal data or deletion, if required.

The Organisation Portal enables your organisation to configure and manage how your data subjects can access and manage their personal data. Subject access requests and deletion requests can be configured to be processed automatically or reviewed by authorised personnel.

The digXcel PDM Platform supports integration with third-party data processors and multiple applications, platforms and systems via our API and integration SDKs - providing a complete and unified solution for personal data management and compliance.

More info at [digXcel.com](http://digxcel.com)


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

1. Cookie consent dialog

== Changelog ==

= 1.0 =
* First version

== Upgrade Notice ==

= 1.0 =
First version
