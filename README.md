# WordPress Form plugin
Simple WordPress plugin given as pre-interview task which provides a form that will generate a certificate and emailed once it is filled and submitted. To download the certificate the user needs to click on the link in the sent email.

## Installation
1. Clone the repository `git clone git@github.com:ivanyankov/interview-wordpress-task.git`
2. CD to the plugin directory.
3. Run `composer install`
3. Compress the folder and choose `.zip` format.
4. Go to your WordPress website plugins page.
5. Click on the button `Add New`
6. Click on the button `Upload Plugin` > `Choose File` > `Install Now`

## Usage
You can load the form on your webpage using the following `[get_user_form]`

## String Translations
1. Download https://poedit.net/
2. Open the ``.po`` file located in your ``languages`` plugin folder
3. Find the translation which you want to update and change it to the desired text
4. Save the file
5. Move the ``.po`` file and the ``.mo`` files (the ``.mo`` file is automatically generated when you are ready translating and saving the ``.po`` file) to your ``languages`` folder
6. Re-upload the plugin