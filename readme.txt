=== Encryption contest ===
Contributors: ShippeR
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ZE6CPB6DEMSQJ&lc=CZ&item_name=Encryption%20contest%20plugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: task, competition, contest, survey, assignment, mission, logged users, skaut, morse code, codes, encrypt, snail, poland cross, substitution, picture code, email, expiration
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.0.1
Tested up to: 4.2.2
Stable tag: 1.1.1


This plugin allows you create competition based on time for all logged users.

== Description ==
Plugin contains a complete system for evaluating contests. It allows you create tasks queue for future days (such as task-1 for first week, task-2 for weekend etc.). 

Tasks changing is based on date. Once you set parameters and you no longer have to worry about anything.  All is doing automatically. 

When one task expirate, next task is followed and summary email is sended to you with results. You can manualy confirm user answer, if you think it's right. No problem, only hit one button.

= Creating tasks =
It's absolutely easy. 

1. Go to admin menu on page Encryption contest and hit create new task. 
1. Fill expirate date (from - to) with right solution, what user should answer. 
1. Choose code from checkbox and save it.

= Interface =
First one is front-end created by shortcode `[encryption-contest]`. You can place it on any page and user can start it using immediately. There is set one default Task for your inspiration, how does it works.

Second interface is options page Encryption contest. This page is for administrators only and you will visit that only once for general settings. There you can manage data
displayed in frontend (if countdown should be displayed etc). You can manage capatibility, what user need for creating tasks, it means that plugin isn't limited only to admins. It depend's on your settings.

Thirth interface is admin menu page Encryption contest. This is place, where you can create or edit all Tasks for users. It's simply. Admin insert text, what user should answer
and plugin will compare it with user answer. If text from admin is similar with text from user, user will be automatically set as right solution. 

= Codes to use =
Plugin consists of eight most used codes in general public. If you create your task with this prepared codes, many people it can encrypt. Predefined codes are:

* Move letter about X in alphabet
* Text backwards with random gaps
* Every second letter
* Substitution behind the numbers Z = 26
* Snail from center
* Great poland cross
* Morse code
* Reverse Morse code

Of course this plugin contain way to insert your own cyphre. It can be text or just image. If you want make any complex cyphre, this is only way. You must translate to code manually. 

= Multilangual =
This plugin is fully prepared for translation. Actually are supported languages: 

* English
* Czech


== Installation ==

You can install from within WordPress using the Plugin/Add New feature, or if you wish to manually install:

1. Download the plugin.
1. Upload the entire `encryption-contest` directory to your plugins folder 
1. Activate the plugin from the plugin page in your WordPress Dashboard
1. Insert shortcode into page and create your own competition 

### Admin menu page
Sometimes there is problem that after first activation will not be displaying page 'Encryption contest' in admin menu. You can fix it this way:

1. Options -> Encryption contest
1. Click on link in first sentence
1. Done, problem is fixed forewer!

### Using shortcode
* You can easily use this plugin inserting shortcode `[encryption-contest]` anywhere on page. Shortcode haven't got any other parameters. 
* If you want hide anothing from front-end, you can do it easily by checking boxes in options.

== Frequently Asked Questions ==

= How long to future can I plan Tasks? =
There is no restriction. You can plan how far you want.

= How can I turn off sending emails? =
Just visit Options->Encryption-contest and uncheck Sending emails.

= Where insert link to Picture code? =
So when you are creating new Task (or editing) you normally fill all fields and Save it. Of course you have to choose Picture code. After first save there will be new field
for link to picture. There you can insert it any way. By button Insert picture or pasting link from other site.

= Preview button doesn't work =
Go to Options->Encryption-contest and fill correct link for preview button. Default displaying page is `/encryption-contest/`. 
The only thing you have to do is change it by your page.

= I have idea for adding new code =
That's great! Please create new topic in plugin support and there we can agree. It should be easy add new code, because plugin is writed for easily adding new codes. 

= I completed plugin translation to my language =
Very nice! The best way how you can help himself and other plugin users is send the language files to me. I will add translate files to next plugin update.

== Screenshots ==
1. Unregistered user view of front-end
2. Inserting answer
3. Right answer - you can insert your own image
4. Wrong answer - user can compare his answer with asignment himself
5. Admin menu page for creating tasks
6. Options page - generally plugin settings
7. Email results of ended task.

== Changelog ==
= 1.1.1 =
* Edited declension of czech countdown words. Now it's displaying like 1 d 10 min 8 sek.

= 1.1 =
* Added option to change heading HTML tag.
* Some files changed location to folder /includes/.
* Better optimalization for translate.
* Fixed some strings where gaps was missing between words.

= 1.0 =
* Added translate folder, marked strings for translate, added czech language.

== Upgrade Notice ==
= 1.1.1 =
* Edited declension of czech countdown words. Now it's displaying like 1 d 10 min 8 sek.

= 1.1 =
* Recommended update. Added support for changing header tag (<h1>, <h2>...) in frontend. 
* Better translate optimalization.

= 1.0 =
* Added translate support.