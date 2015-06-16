QuantiModo-WordPress-Plugin
=======================================

### NOTE: This plugin is in an extremely early stage of development and is not yet function. The roadmap for the development of the plugin is below. We'd be very grateful for any pull requests. Thanks!

Help ignite a revolution of citizen science to find new solutions to chronic illnesses.  Install the Quantimodo Wordpress plugin!

This is a mobile-friendly WordPress plugin that enables users of any WordPress to authenticate and share their data from QuantiModo.com. It also allows you to embed dynamic graphs containing your users' Quantified Self data agggregated at QuantiModo.com. These graphs may be included in any page or post using shortcode.

Note: This JavaScript library (https://github.com/QuantiModo/QuantiModo-SDK-JavaScript/blob/master/quantimodo-api.js) is required for this plugin's functionality.

## Add QuantiModo Connect Button

PURPOSE: 
The purpose of allowing users to log in via the QuantiModo API's OAuth2 endpoint is so that the WordPress site can obtain an OAuth2 access token which will be included in the Authorization header of all API requests to QuantiModo as a Bearer token. 

It is in this way that the user may authorize the QM API to obtain their data from other API's such as Fitbit, MyFitnessPal, Withings, etc. This is done using the Connect shortcode.

Once a user has authorized QuantiModo to obtain their data, the WordPress site can use the OAuth2 access token to also make API requests to the QM API in order to store new user supplied data or visualize their data on various graphs produced by the Timeline and Correlations shortcode. 

SETUP:
- Download and install the QuantiModo-WordPress-Plugin.
- Email api@quantimo.do to get an API client id and secret.
- Setup your QM API key/secret in the WordPress backend under Settings > QM-OAuth.
- Add the QM Login button anywhere to your site with the [qmoa_login_form] shortcode.

Once a user authorizes you to access their measurements, their OAuth access tokens will be stored and refreshed automatically. They will be able to continue to access their data through your site until they revoke access. 

For example:

[qmoa_login_form layout="buttons-column" align="left"]

*Possible shortcode attributes:*

- layout - determines whether to display the login buttons as links or buttons, stacked vertically or lined up horizontally. Possible values: links-row, links-column, buttons-row, buttons-column
- align - sets the horizontal alignment of the custom form elements. Possible values: left, middle, right
- show_login - determines when the login buttons will be shown. Possible values: never, conditional, always
- show_logout - determines when the logout button will be shown. Possible values: never, conditional, always
- logged_out_title - sets the text to display above the custom login form when the user is logged out. Possible values: any text
- logged_in_title - sets the text to display above the custom login form when the user is logged in. Possible values: any text
- logging_in_title - sets the text to display above the custom login form when the user is logging ing. Possible values: any text
- logging_out_title - sets the text to display above the custom login form when the user is logging out. Possible values: any text
- style - sets the custom css style to apply to the custom login form. Possible values: any text
- class - sets the custom css class to apply to the custom login form. Possible values: any text

## Timeline Graph
Shortcode - [qm_timeline]

To see how it works:
- Create an account at https://quantimo.do
- Connect the Weather connector at https://quantimo.do/connect/ (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)
- Go to https://quantimo.do/analyze and select a variable to chart on the left. 

If you have any problems with this process, please submit a help request by clicking the "Feedback" tab on the right. 

The code to be used for this shortcode is here: 
https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/timeline-page

![](https://i.imgur.com/AwhxdGP.png)

## Correlation Charts
Shortcode - [qm_correlation_charts]

To see how it works:
- Create an account at https://quantimo.do
- Connect the Weather connector at https://quantimo.do/connect/ (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)
- Go to https://quantimo.do/correlate and select a variable to chart on the left. 

If you have any problems with this process, please submit a help request by clicking the "Feedback" tab on the right. 

The code to be used for this shortcode is here: 
https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/correlation_charts

![](https://quantimo.do/wp-content/uploads/2013/08/Correlations-Page-Demo-Sleep-Mood-1024x648.png)

*Possible shortcode attributes for correlation charts:*

- examined_variable - Sets the default examined variable. Possible values: any variable name
- secondary_variable - Sets the default secondary variable to be selected on the bar graph. Possible values: any variable name
- examined_is_cause - Sets the examined variable to be considered the cause in the relationship.  Possible values: true or false. Default: false

## Connectors to Data Sources
Shortcode - [qm_import_data]

To see how it works:
- Create an account at https://quantimo.do
- Connect the Weather connector at https://quantimo.do/connect (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)

![quantimodo manage connected accounts - google chrome 10152014 63216 pm](https://cloud.githubusercontent.com/assets/2808553/8172734/39b6f640-1389-11e5-9d1c-332d4e5a7c54.jpg)

The code for this page is located here:
https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/connect-page

## Add a Measurement Button
Shortcode - [qm_add_measurement]

Demo: Try https://chrome.google.com/webstore/detail/quantimodo-universal-trac/jioloifallegdkgjklafkkbniianjbgi?hl=en-US

The relevant code and more information on the functionality can be found at https://github.com/Abolitionist-Project/QuantiModo-Chrome-Extension

## Correlation Search Box
Shortcode - [qm_correlation_search]

Demo: https://quantimo.do/qm-search

*Possible shortcode attributes for correlation search:*
- searched-cause-variable - Instead of a search box, only a list of the effects of this variable are displayed
- searched-effect-variable - Instead of a search box, only a list of the causes of this variable are displayed

![what-affects-qm-search-box-screenshot-2014-06-29-20 14_picmonkeyed](https://cloud.githubusercontent.com/assets/2808553/8192570/adcca18c-1434-11e5-8ba4-1c415f363394.png)

![search-results-page-mockup png](https://cloud.githubusercontent.com/assets/2808553/8192587/d66be102-1434-11e5-9082-fa47a69a108b.jpg)

The code and more information can be found at https://github.com/Abolitionist-Project/QM-Search-Plugin. Please email m@thinkbynumbers.org if you need access to this repository. 

## Mood Tracker
Shortcode - [qm_track_mood]

Demo: https://chrome.google.com/webstore/detail/moodimodo-beta/lncgjbhijecjdbdgeigfodmiimpmlelg?hl=en-US

We want to be able to embed those faces in any WP page or post.

Here's the code: https://github.com/Abolitionist-Project/MoodiModo-Chrome

![moodimodo chrome screenshot](https://cloud.githubusercontent.com/assets/2808553/8116915/6fe35728-104a-11e5-9c13-050d370a1332.jpg)

# Options for Adding Shortcode

Shortcode can be added to a post through "Add Media" by installing [ShortCake](https://github.com/fusioneng/Shortcake).

Our API documentation can be found at https://quantimo.do/api/docs/index.html
