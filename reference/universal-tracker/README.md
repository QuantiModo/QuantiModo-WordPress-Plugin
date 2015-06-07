QuantiModo-ChromeExtension
================

NOTE THAT YOU MUST BE LOGGED INTO https://quantimo.do/ FOR YOUR RATINGS TO BE RECORDED.  THIS IS A BETA TESTING RELEASE.  PLEASE EMAIL INFO@QUANTIMO.DO WITH QUESTIONS OR SUGGESTIONS AND I'LL RESPOND IMMEDIATELY. THANK YOU!

Use Quantimodo for Chrome to effortlessly track anything! You can track your sleep, diet, medication, physical activity, anything else that can be quantified.

How to Record a Measurement

1. CLICK THE EXTENSION - Click the QuantiModo icon in the upper right hand side of your browser. It might be hidden. In that case you'll have to drag the divider line to the right of the URL field to see it.

2. SELECT A VARIABLE - Type the name of the variable that you want to track. If it's an existing variable, the settings should be populated automatically and you can move to step 3. If you've never entered data for that variable before you'll need to adjust the settings for that variable first

3. ADD YOUR MEASUREMENT - Then enter the value for the measurement and select the correct units.

4. SEND YOUR MEASUREMENT - After pressing the "ADD" button data is then sent to https://quantimo.do/.

At QuantiModo, this data can be combined with data from other applications, devices and electronic health records. Since the human mind is not powerful enough to retain all of the necessary information, this data integration feature is essential to identifying correlations and causal relationships.

Users will also have the option to anonymously donate their data to the Mind First Foundation, the Personal Genome Project, and other researchers in order to help facilitate the crowd-sourced observational research which will eventually lead to the eradication of mental illness.


Our Chrome extension currently has all the options in one screen.  The functionality is mostly here, but we want to separate it out into four separate screens. 

The current version of the extension can be downloaded here:
https://chrome.google.com/webstore/detail/quantimodo-beta/jioloifallegdkgjklafkkbniianjbgi?hl=en-US

Currenly, if the user clicks on the extension and not logged in, they are sent to quantimo.do/analyze. Instead, we now want them to see a login box like this:

Login Screen:
![login-dialog](https://cloud.githubusercontent.com/assets/2808553/4691816/c792d3be-5735-11e4-98c5-3124f0145134.png)

That should use the same WordPress authentication logic found at https://quantimo.do/wp-login.php

Once they are logged in, they see this screen which uses the quantimodo/api/variables/search endpoint to autocomplete. If they press enter or click on a variable, it should move to the next screen.  This should function just like Google's autocomplete search. 

Select Variable Screen:
![initial](https://cloud.githubusercontent.com/assets/2808553/4623243/ded17462-5349-11e4-85e2-e900877ff0b1.png)

If the variable was one that existed and came up on the autocomplete, they see this screen where they may submit a measurement.

Add Measurement Screen:
![sumbit_measurement](https://cloud.githubusercontent.com/assets/2808553/4623245/e3256ea6-5349-11e4-887b-4fa3ab8b21a6.png)

If the variable that they entered does not already exist, the API call for the variables will return an empty array.  If this happens, the user should instead sent to this "Add Variable and Measurement" screen to create a new variable in addition to reporting their first measurement for that variable. 

Add Variable and Measurement Screen:
![new_variable](https://cloud.githubusercontent.com/assets/2808553/4623248/e7433374-5349-11e4-8907-1276a8cbe0b8.png)
