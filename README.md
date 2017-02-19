QuantiModo-WordPress-Plugin
=======================================

[![Codacy Badge](https://api.codacy.com/project/badge/9267e17136a34184905cec32f8867d32)](https://www.codacy.com/app/m_3/QuantiModo-WordPress-Plugin)
[![Code Climate](https://codeclimate.com/github/Abolitionist-Project/QuantiModo-WordPress-Plugin/badges/gpa.svg)](https://codeclimate.com/github/Abolitionist-Project/QuantiModo-WordPress-Plugin)

*The roadmap for the development of the plugin is below. We'd be very grateful for any pull requests. Thanks!*

Help ignite a revolution of citizen science to find new solutions to chronic illnesses.  Install the Quantimodo Wordpress plugin!

This is a mobile-friendly WordPress plugin that enables users of any WordPress to authenticate and share their data from QuantiModo.com. 
It also allows you to embed dynamic graphs containing your users' Quantified Self data agggregated at QuantiModo.com. 
These graphs may be included in any page or post using shortcode.

Note: This JavaScript library (https://github.com/QuantiModo/QuantiModo-SDK-JavaScript/blob/master/quantimodo-api.js) is required for this plugin's functionality.

## Add QuantiModo Login Button

PURPOSE: 
The purpose of allowing users to log in via the QuantiModo API's OAuth2 endpoint is so that the WordPress site can obtain an OAuth2 access token which will be included in the Authorization header of all API requests to QuantiModo as a Bearer token. 

Allow your users to improt their data from various digital health devices and website including:
- MyFitnessPal
- RunKeeper
- WhatPulse
- GitHub
- Moodscope
- Up by Jawbone
- Fitbit
- Facebook
- Withings
- MoodPanda
- RescueTime
- MyNetDiary
- Weather
- Sleep as Android

Additionally, you can allow store new user supplied data or visualize their data on various graphs produced by the Timeline and Predictors shortcode. 

SETUP:
- Download and install the QuantiModo-WordPress-Plugin.
- Create a new app in the [QuantiModo Developer Portal](https://app.quantimo.do/api/v2/apps) to get an API client id and secret.
- Add your QM API key/secret in the WordPress backend under Settings > QuantiModo.
- Add the QM Login button anywhere to your site with the [qmoa_login_form] shortcode.

Once a user authorizes you to access their measurements, their OAuth access tokens will be stored and refreshed automatically. 
They will be able to continue to access their data through your site until they revoke access. 

## Timeline Graph
Shortcode - [qmwp_timeline]

To see how it works:
- Create an account at https://app.quantimo.do
- Connect the Weather connector at https://app.quantimo.do/api/v2/account/connectors (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)
- Go to https://quantimo.do/analyze and select a variable to chart on the left. 

If you have any problems with this process, please submit a help request at https://help.quantimo.do. 

![](https://i.imgur.com/AwhxdGP.png)

## Correlation Charts
Shortcode - [qm_correlation_charts]

This shortcode would allow one to create a widget or shortcode for embedding a longitudinal timeline, correlation scatterplot, or correlation listing bar graph in posts, pages, custom post types, and sidebars.  

Variables, settings, and filters are supplied when creating a the widget or shortcode. The charts also include a the ability to add or remove variables.

To see how it works:
- Create an account at https://quantimo.do
- Connect the Weather connector at https://quantimo.do/connect/ (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)
- Go to https://quantimo.do/correlate and select a variable to chart on the left.

If you have any problems with this process, please submit a help request by clicking the "Feedback" tab on the right.

The code to be used for this shortcode is here:
https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/correlation_charts

![correlations-page-demo-sleep-mood-1024x648](https://cloud.githubusercontent.com/assets/2808553/10770971/25b5c1d4-7cbc-11e5-90d5-5c046cb70d89.png)

*Possible shortcode attributes for correlation charts:*

- examined_variable - Sets the default examined variable. Possible values: any variable name
- secondary_variable - Sets the default secondary variable to be selected on the bar graph. Possible values: any variable name
- examined_is_cause - Sets the examined variable to be considered the cause in the relationship.  Possible values: true or false. Default: false

## Import Data
Shortcode - [qmwp_manage_accounts]

To see how it works:
- Create an account at https://quantimo.do
- Connect the Weather connector at https://quantimo.do/connect (using "62034" as your zip code if you're not on Earth and you don't have a location to enter.)

![quantimodo manage connected accounts - google chrome 10152014 63216 pm](https://cloud.githubusercontent.com/assets/2808553/8172734/39b6f640-1389-11e5-9d1c-332d4e5a7c54.jpg)

The code for this page is located here:
https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/connect-page

## Add a Measurement Button
Shortcode - [qm_add_measurement]

This would create an "Add a Measurement" button which produces a popup similar to the the QuantiModo Chrome extension that allows a user to submit a measurement. 

How to Record a Measurement

1. CLICK THE EXTENSION - Click the QuantiModo icon in the upper right hand side of your browser. It might be hidden. In that case you'll have to drag the divider line to the right of the URL field to see it.
2. SELECT A VARIABLE - Type the name of the variable that you want to track. If it's an existing variable, the settings should be populated automatically and you can move to step 3. If you've never entered data for that variable before you'll need to adjust the settings for that variable first
3. ADD YOUR MEASUREMENT - Then enter the value for the measurement and select the correct units.
4. SEND YOUR MEASUREMENT - After pressing the "ADD" button data is then sent to https://app.quantimo.do/.

Demo: Try https://chrome.google.com/webstore/detail/quantimodo-universal-trac/jioloifallegdkgjklafkkbniianjbgi?hl=en-US

The relevant code and more information on the functionality can be found at https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/QM-Search-Plugin

## Correlation Search Box
Shortcode - [qm_correlation_search]

Demo: https://quantimo.do/qm-search

This plugin creates a new WordPress page that allows one to search for:
- The strongest predictors of the severity of any given condition
- The most strongly predicted effects of any given stimulus

The results are based on the average predictive correlations for all QuantiModo users. 

- The user should indicate if they want to search for the effects of 
- The user should type in characters 
- and have an auto-complete drop down with the top 5 variables with the highest correlations

If the entered variable "Crazy Ass Variable No One Ever Heard Of" doesn't exist:
```
Your search - Crazy Ass Variable No One Ever Heard Of - did not match any documents.

Suggestions:

Make sure all words are spelled correctly.
Try different keywords.
Try more general keywords.
```

*Possible shortcode attributes for correlation search:*
- searched-cause-variable - Instead of a search box, only a list of the effects of this variable are displayed
- searched-effect-variable - Instead of a search box, only a list of the causes of this variable are displayed

![what-affects-qm-search-box-screenshot-2014-06-29-20 14_picmonkeyed](https://cloud.githubusercontent.com/assets/2808553/8192570/adcca18c-1434-11e5-8ba4-1c415f363394.png)

![search-results-page-mockup png](https://cloud.githubusercontent.com/assets/2808553/8192587/d66be102-1434-11e5-9082-fa47a69a108b.jpg)

## Mood Tracker
Shortcode - [qmwp_rating_faces]

This would create a widget/shortcode with the mood-rating faces that we could put in a page/post or sidebar. 
If clicked on, it would submit an overall mood measurement for the currently logged in user.

![mood-rating-wordpress-shortcode](https://cloud.githubusercontent.com/assets/2808553/8238887/10787330-15be-11e5-853c-93f00d8e45cd.png)

# Options for Adding Shortcode

Shortcode can be added to a post through "Add Media" by installing [ShortCake](https://github.com/fusioneng/Shortcake).

Additional API documentation can be found at [https://app.quantimo.do/api/v2/account/api-explorer](https://app.quantimo.do/api/v2/account/api-explorer)