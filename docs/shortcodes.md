
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


For example:

[qmoa_login_form layout="buttons-column" align="left"]
