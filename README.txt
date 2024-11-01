=== WP-Redmine-Issues ===
* **Contributors**: arcanasoft
* **Donate link**: https://arcanasoft.de/projects/WP-Redmine-Issues/donate
* **Requires at least**: 3.0
* **Tested up to**: 4.9
* **Requires PHP**: 5.2.4
* **Tags**: redmine, tickets, issues, create issue, leave comment
* **License**: GPLv3
* **License URI**: https://www.gnu.org/licenses/gpl-3.0.en.html


Create, view and comment on issues within your existing Redmine installation from within Wordpress Backend.


== Description ==

This plugin uses the Redmine API to display and, if enabled in Settings, view and post comments. You can create an issue in a dialog, asking for project, subject, details and, if enabled in Settings, issue categories.

API usage in this plugin is designed for a specially created Redmine "system-user" that is granted access to the relevant projects and has priviliges to create and view issues within these projects instead of using individual user-accounts from Redmine.
Benefits:

* You don't have to create every Wordpress user in Redmine or keep them synchronized. Which Wordpress user is creating an issue or comment can be seen as part of Details or the comment text with name and e-mail address as set in Wordpress.
* No messing around with access privileges in Redmine for individual users
* No direct access to Redmine for your wordpress users


= What is Redmine? =

[Learn more.](https://www.redmine.org/)



== Installation ==
= General Notes =

Prerequisite: Existing Redmine Installation with activated API, accessible from the server hosting WordPress.


= Install WPRI =

1. Upload the full `wp-redmine-issues` directory into your `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Open the new 'Settings/WPRI - Settings' menu and follow the instructions above to configure your Redmine connection. Save settings.
4. Click new 'Tickets' menu item in Wordpress Backend to see your Redmine-Tickets.


= Settings: =
* Redmine URL: Base URL for your existing Redmine Installation.
* Redmine API-Key: API-Key of special user you created within Redmine that has access to the project(s) of which you want to handle issues from within wordpress
* Default setting for the number of issues per page: Number of item per pages when pagination is needed
* Show issue comments: Show comment section in issue details
* Use issue categories: Issue Categories as defined in Redmine may be used as filter in ticket list and can be set for new issues


== Screenshots ==

1. Issues List
2. Issue Details
3. Issue Create - Project
4. Issue Create - Tracker and Categorie
5. Issue Create - Details
6. Issues Settings


== Frequently Asked Questions ==
= What is the plugin license? =

This plugin is released under a GPL license.


== Changelog ==
= Version 1.1 =
* English translation added

= Version 1.0 =
* German language only
** Display
** Create
** Display and create comments on issues
** Display issue categories. Set issue category in "Create issue" dialog.



== Translations ==

* English - default
* German

*Note:* All my plugins are localized/ translateable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization" plugin](http://wordpress.org/extend/plugins/codestyling-localization/) and for validating the ["Poedit Editor"](http://www.poedit.net/).


== Credits ==

Thanx to M. Henning, L. Reis and all who send me mails containing bug reports
