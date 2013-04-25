 * Title: Automatic Account Creation
 * Date: 08/21/2012
 * Author: Corbin Schwalm | AvidMobile
 * Version: 3.0
 * Requires: Authorize.Net & SMSPlatform Packages
 * 
 * This application accepts form input and then creates an Authorize.Net CIM profile.
 * Then the customer profile information is inserted into the recurrent billing database.
 * The application then creates a SMS Platform with the customers information.
 * It reads the MC_LEVEL in connection_info.php to determine the correct account level to create.
 * A success screen is then displayed with a welcome email being sent.

Installation Instructions:

1) Create a MySQL user & database and upload the table with the provided .sql file.

2) Place the included folder into the ROOT of the website. (CHMOD 755) - No write permission.

3) Have a CRON job run once a day at midnight on the file /automatic_account_creation/cron/autobill.php

4) Upload a logo to /automatic_account_creation/images/

5) Edit /automatic_account_creation/includes/connection_info.php

6) Add your plans to /automatic_account_creation/includes/pricing.php

7) Create a new .htpasswd file in /automatic_account_creation/admin/

8) Verify that the current .htaccess file in /automatic_account_creation/admin/ works.

9) If the payment form is to be linked to. Update/place the included .htaccess file in the root of the webserver.


Optional Instructions:

1) Disable the receipt e-mails in AUthorize.Net

2) Edit the HTML for the e-mail/status messages.

3) If the form is to be placed in-line with an existing webpage, see header comments to remove PHP code from
payment_form.php.