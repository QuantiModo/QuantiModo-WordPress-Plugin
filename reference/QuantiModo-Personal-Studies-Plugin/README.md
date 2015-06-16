QM-Personal-Studies-Plugin
==========================


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
