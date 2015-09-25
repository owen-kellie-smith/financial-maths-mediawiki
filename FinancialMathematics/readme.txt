=== Plugin Name ===
Contributors: owenks
Donate link:           
Tags: actuarial, actuary, financial mathematics, financial, mathematics, CT1
Requires at least: 3.5.1
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Practice financial mathematics questions for the actuarial exams. Define and calculate interest rates, annuity certain, mortgage repayment, project valuation, spot/forward/par yields.

== Description ==

This plugin provides a shortcode which render forms that calculate and explain annuities certain, repayment mortgages etc: maths questions which typically appear in the CT1 actuarial exam and the Interest Theory part of the Financial Mathematics exam.


== Installation ==

1. Download, unzip and upload to your WordPress plugins directory.  
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add the shortcode [fin-math] to a page (not a post).


== Frequently Asked Questions ==

= Where is the plugin running? = 

It's running at [Financial mathematics](https://php-treaties.rhcloud.com/ "Financial mathematics") 

= How do I run the unit tests? =

Install phpunit and enter
$phpunit

== Screenshots ==

1. Input form rendered after mortgage selected from a form rendered with the [fin-math] shortcode is rendered.

2. Feedback once a user has submitted mortgage parameters to a page with [fin-math] shortcode.


== Changelog ==

= 1.17 =
* Removed dud references to bdmfst (an old defunct domain).

= 1.16 =
* Gets mathjax for either http or https.

= 1.15 =
* Tested it works with Wordpress 4.1.
* Included necessary PEAR files.
* Bug fix: removed a call-by-reference

= 1.14 =
* Tested it works with Wordpress 3.8.
* Added comment about needing to install PEAR.

= 1.13 =
* Bug fix. Par yields not shown for irrelevant terms.
* Presentation. Actuarial symbol used for annuity certain.

= 1.12 =
Concept classes (input/output forms) refactored and docuemented.

= 1.11 =
Spot rates added to [fin-math] and [concept_spot_rates] shortcodes.

= 1.10 = 
Multiple cashflows (mixtures of annuities) enabled in [fin-math].

= 1.9 =
* All forms loadable with single shortcode [fin-math].

= 1.8 = 
* Bug fix. Decreasing annuity can be entered.

= 1.7 =
* Classes and tests placed in own folders.  MathJax loaded remotely (no need to extra plugin).

= 1.6 =
* Renamed files to conform better to Wordpress PHP coding standards.
* Added phpunit tests.

= 1.5 =
* Bug fix. Annuity "Just show me the answer" button redirects to correct page.
* Bug fix. Formula for i(m) in mortgage schedule has right sign in power.  

= 1.4 =
* Bug fix.  Shortcode attributes now work.

= 1.3 =
* convertInt shortcode added (converts interest rates).

= 1.2 = 
* Formulae shown for mortgage repayment schedule.

= 1.1 =
* shortcode attributes enable question or answer form to be excluded.
* plugin options page (help page) added to admin menu.

= 1.0 =
* annuityCertain, mortgage shortcodes.


== Upgrade Notice ==

= 1.16 =
* Mathjax works with https.

= 1.15 =
* PEAR files now included with plugin.
* Works with Wordpress 4.1.

= 1.12 =
* New functionality: spot rates added to [fin-math] and [concept_spot_rates] shortcodes.
* Readability of code: Concept classes (input/output forms) refactored and documented.

= 1.10 = 
Multiple cashflows (mixtures of annuities) enabled in [fin-math].

= 1.9 =
All forms loadable with single shortcode [fin-math].

= 1.8 =
* Bug fix. Decreasing annuity can be entered.

= 1.7 =
Input values to get yields (or yields to get values). Escalating and increasing annuities.

= 1.5 =
* Bug fix. Annuity "Just show me the answer" button redirects to correct page.

= 1.4 =
Bug fix.  Shortcode attributes now work so that question or answer form can be
excluded.

= 1.3 = 
Interest rate conversion questions added e.g. convert from annual effective
rate to discount rate convertible monthly.

= 1.2 =
Formulae for mortgage repayment schedule shown explicitly.  Plugin's styles defined in ct1.css and added to header.

= 1.1 =
Calculator form (for any parameters) can be shown below the question form, according to what attributes you use in the shortcode.

= 1.0 =
First version.

