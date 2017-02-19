# Planned Features

## QuantiModo Studies Plugin
This plugin could be used to automatically create studies using the "Study" custom post type by selecting one or more variables.  This custom post type would be located in the QuantiModo custom WP dashboard menu.

A WordPress plugin to allow citizen scientists to share their discoveries. This plugin could be used to automatically create studies using the "Study" custom post by selecting  one or more variables. 

The study post would display:
- Title: Question to be answered by the study.
- Conclusion: Answer the question through interpretation of results. 
- Hypothetical Cause:
- Hypothetical Effect: 
- Principal Investigator: Person who created the study.
- Background: Reason for the study and/or existing research on topic. 

Results: 
- Scatterplot with hypothetical effect on y axis and hypothetical cause on x-axis; 
- Parameters of analysis (variable settings); 
- Data download option;  
- Timeline graph (optional)
- Methods: List of apps and devices used to obtain the measurements.
- Limitations (Optional): Include with any reservations like potentially confouding variables, sampling error, etc
- Suggestions for further research. 


Walkthrough of User Actions:

- Creates a Study custom post.
- Enters a research question like "What affects my mood?" in the title of the post
- Enters some text description of the study in the post body content
- Selects an "Examined Variable"
- Selects "What is predictive of EXAMINED VARIABLE?" or "What does EXAMINED VARIABLE predict?"
- User presses publish.
- Study posts are listed on a WP Page Called Studies
- User click on their study title and excerpt in the Studies page.
- User is taken to a Study post
- Study post contains the Research Question (Title) at the top
- User study post body content is below the title
- Bar graph containing "Secondary Variable" correlations with the Examined Variable is below the post body content text.
- If user clicks on a Secondary Variable in the bar graph, a scatterplot and timeline chart pop up

Here's the initial code for this feature: https://github.com/QuantiModo/QuantiModo-WordPress-Plugin/tree/develop/reference/QuantiModo-Personal-Studies-Plugin

Preliminary Mockup for Back End of Study custom post type:
![heroku_wplms_create_studies](https://cloud.githubusercontent.com/assets/2808553/8193448/81f8b202-143a-11e5-9137-d47c5b2ae1ce.png)


Preliminary Mockup for front end of Study custom post type:
![qm-study-custom-post-type-front-end-template](https://cloud.githubusercontent.com/assets/2808553/8193426/5eaff8f0-143a-11e5-9a08-76f3d5538e5f.png)


QuantiPress
===========

Other shortcodes:

### Data Import 
**Tracker** - Any application or device capable of quantifying some aspect of human life.

This plugin should:

1. Create a single "Trackers" WP PAGE where icons for all connectors and uploaders are displayed in categories (see https://quantimo.do/tools/ or https://www.dropbox.com/s/ke032k91j2m8c5k/Screenshot%202014-07-15%2000.39.25.png). 
- Create a "QuantiModo" top-level WP administration menu see (ask Zain for example link and screenshot).
- Create a "Trackers" sub-level WP administration menu within the "QuantiModo" top-level WP administration menu.
- Create a "Tracker" custom post type. See this example of the front end appearance https://quantimo.do/portfolio/fitbit-2/ or https://www.dropbox.com/s/z61ncgo7gmyxsmf/Screenshot%202014-07-15%2001.05.13.png.  See this example of the back end appearance
- Create a "Tracker" custom post (like  for each record in the `quantimodo`.`sources` database.
- shortcodes for each connector icon to be displayed.  
- Specific connectors could be added as shortcodes or widgets to any page. 
- Any connectors could be added on the individual tools pages.  
- This would create a page and page template for the connectors page.


### Predictor Search
This plugin would add a search box widget and a qm-search and qm-search-results templates.  The search box would be similar to the Google search. The search results page template would have a list of variables preditive of or predicted by the searched variable in order of correlation. This page would also display the average values predictive of an above or below average effect.

### Universal Tracker 
This would create a widget similar to the the QuantiModo Chrome extension that allows a user to submit a measurement. 

### MoodiModo 
This would create a widget/shortcode with the mood-rating faces that we could put in a page/post or sidebar. If clicked on, it would submit an overall mood measurement for the currently logged in user.

### Visualization 
This allow one to create a widget or shortcode for embedding a Highcharts longitudinal timeline, correlation scatterplot, or correlation listing bar graph in posts, pages, custom post types, and sidebars.  Variables, settings, and filters are supplied when creating a the widget or shortcode. The charts would also include a the ability to add or remove variables.

Example for inserting into posts: 
- http://wordpress.org/plugins/shortcodes-ultimate
- http://wordpress.org/plugins/wp-slimstat-shortcodes/

### Variable Settings 
This would create a pop-up 
This would be a pop-up window, widget, and page template that could be called from other widgets.  Could be embedded using shortcode.  Requirements: QuantiModo API Plugin.  

Example: http://wordpress.org/plugins/advanced-custom-fields/

### Studies 
This plugin could be used to automatically create studies using the "Study" custom post type by selecting one or more variables.  This custom post type would be located in the QuantiModo custom WP dashboard menu. ﻿

Example: http://wordpress.org/plugins/wp-slimstat-shortcodes/

## WHY
To abolish all uneccessary suffering on earth. 

## HOW
Accelerate research by integrate, analyze, and visualize life tracking data to find out what most impacts health and happiness.  Then, share your discoveries with other citizen scientists!
