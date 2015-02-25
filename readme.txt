=== WordPress Image shrinker ===
Contributors: HETWORKS
Plugin Name: WordPress Image Shrinker by HETWORKS
Plugin URI: https://wordpress.org/plugins/wp-image-shrinker/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=6JSXUB6Q9SD2U&lc=NL&item_name=HETWORKS&item_number=TinyPNG%20for%20WordPress&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Version: 1.1.0
Tested up to: 4.1
Author: Anne Rozema
Author URI: http://www.hetworks.nl
Tags: downsize, image, optimisation, optimise, optimization, optimize, plugin, processing, resize, resizing, upload, bandwidth, compress, faster, improve, smush, shrink, performance, png, jpg, jpeg, speed, tinyjpg, tinypng, website, jpegmini, pngmini
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Reduce image file sizes drastically and improve performance and Pagespeed score using the TinyPNG API within WordPress. Works for both PNGs and JPGs.

== Description ==

You probably know that the speed of your website is important for both user experience and for your search engine ranking. The people 
at TinyPNG.com offered this great service where you could upload your PNG images and shortly also your JPG images, to have the file size 
shrinked to much lower amount of data, without a noticable reduce of image quality. And that is great! Compare your Google pagespeed score 
before and after you shrinked your images. You will gain many points.

The disadvantage of using TinyPNG is that you had to use the service upfront, before you could upload your images into your WordPress 
medialibrary you had to upload them to TinyPNG and download the shrinked files again. When the images were already in your medialibrary you 
had to do this all over, also for the images that were cropped by WordPress into the different filesizes. With this plugin that is all history. 
You just upload your images and as part of the proces these images are processed by TinyPNG.   

This is what TinyPNG say themselves:
How does it work? TinyPNG uses smart lossy compression techniques to reduce the file size of your PNG files. By selectively decreasing the 
number of colors in the image, fewer bytes are required to store the data. The effect is nearly invisible but it makes a very large 
difference in file size!



== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the WordPress Image shrinker folder to the plugins directory in your WordPress installation
2. Activate the plugin
3. Navigate to the WordPress Image shrinker UI Menu
4. Generate your TinyPNG API key from https://tinypng.com/developers and enter it on the Settings page.

That's it! From now on all your new images are shrinked on the fly!


== Frequently Asked Questions ==

= The plugin doesn't work? =

Did you insert your TinyPNG API key? You can retreive it from https://tinypng.com/developers It is free for up to 500 images per month.

= What about the images I did upload before installing your plugin? =

On the settings page you find a button to reprocess all images in the library again.

= Why is uploading images taking more time =

After the image is uploaded WordPress shows you it is finished (100%), but right then your image is processed by TinyPNG. That may take a few extra seconds.


== Screenshots ==

1. Screenshot of backend panel

== Changelog ==

= 1.1.0 =
* Added option to pick which sizes of already uploaded images to compress

= 1.0.3 =
* Added API Key input reminder on plugins page if no api key is present

= 1.0.2 =
* Better error handling

= 1.0.1 =
* Bugfixes

= 1.0 =
* First release




