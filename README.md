=== QuantiModo ===
Contributors: mikepsinn
Tags: social, science, quantified self, mood tracking, digital health, healthcare, mental health
Requires at least: 4.3
Stable tag: 0.6.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

# QuantiModo WordPress Integration

Help ignite a revolution of citizen science to find new solutions to chronic illnesses.  Install the Quantimodo WordPress plugin!

This plugin allows your users to record, aggregate, analyze and visualize their health and life-tracking data.
The data collection, analysis and visualization functionality may be included in any page or post.

See a [LIVE DEMO](https://quantimo.do) by clicking the icon in the lower right-hand corner at [https://quantimo.do](https://quantimo.do)!

# Installation

1. Create, configure, and optionally add your branding to your new app at [QuantiModo](https://app.quantimodo.com/builder).  It's free!
2. Add this plugin to WordPress and enable it. See [Managing Plugins](https://codex.wordpress.org/Managing_Plugins)
3. Get your [QuantiModo Client ID here](https://builder.quantimo.do)
4. Visit QuantiModo Settings on your WordPress site (yoursite.com/wp-admin/admin.php?page=menus.php), enter your app's QuantiModo client id and save.

Go to your user-facing site, refresh the page and click the icon in the lower right corner to try out your new app!

# Users

If the user is logged in on your WordPress site, we use their WordPress id as an identifier in the widget.
Otherwise the widget operates in anonymous mode and the user must log in with a social provider or QuantiModo account.

# Embedding in Pages or Posts

To embed a specific page of your QuantiModo app in a WordPress page or post:
- Go to your QuantiModo web app at https://quantimodo.quantimo.do .
- Go to the page you want to embed and copy the url.
- Go to the WordPress page or post editor "text" or "code" section.
- Paste this in the post or page:
```
<iframe 
  src="THE_URL_YOU_COPIED_WITH_HTTPS_AND_WITHOUT_ANY_TRAILING_URL_PARAMS?clientId=your_client_id" 
  width="100%" 
  height="650px" 
  frameborder="1" 
  scrolling="yes" 
  align="left">
</iframe>
```
- Replace `https://web.quantimo.do/WHATEVER_YOU_WANT_TO_EMBED` with your actual link to the page you want to embed
- Replace `your_client_id` with your client id.
- Adjust or remove the iFrame settings as needed.

# Screenshots
## Charts
![QuantiModo chart](https://raw.githubusercontent.com/Abolitionist-Project/QuantiModo-WordPress-Plugin/develop/assets-wp-repo/screenshot-1.png)

## History
![QuantiModo history](https://raw.githubusercontent.com/Abolitionist-Project/QuantiModo-WordPress-Plugin/develop/assets-wp-repo/screenshot-2.png)

## Import Data
![QuantiModo import](https://raw.githubusercontent.com/Abolitionist-Project/QuantiModo-WordPress-Plugin/develop/assets-wp-repo/screenshot-3.png)

Allow your users to import their data from various digital health devices and website including:
- Facebook
- Fitbit
- GitHub
- Google Calendar
- Google Fit (imports from dozens of other data sources)
- MedHelper
- Mint
- MoodPanda
- Moodscope
- MyFitnessPal
- MyNetDiary
- RescueTime
- RunKeeper
- Sleep as Android
- Strava
- Weather
- WhatPulse
- Withings

## Predictors
![QuantiModo predictors](https://raw.githubusercontent.com/Abolitionist-Project/QuantiModo-WordPress-Plugin/develop/assets-wp-repo/screenshot-4.png)

Allow your users to search for:
- The strongest predictors of the severity of any given condition
- The most strongly predicted effects of any given stimulus

## Reminder Inbox
![QuantiModo inbox](https://raw.githubusercontent.com/Abolitionist-Project/QuantiModo-WordPress-Plugin/develop/assets-wp-repo/screenshot-5.png)

### SECURITY REQUIREMENTS

- An SSL-secured WordPress site

If you'd like, I can create and host a WordPress site for you.  If interested, please email mike@quantimo.do.

## Support

If you have any problems with the setup process, please submit a help request at https://help.quantimo.do.

## Development

Additional API documentation can be found at [https://app.quantimo.do/api/v2/account/api-explorer](https://app.quantimo.do/api/v2/account/api-explorer)

== Screenshots ==

1. Charts
2. History
3. Import Data
4. Predictors
5. Reminder Inbox
6. Manage Reminders
