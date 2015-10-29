# financial-maths-mediawiki
Mediawiki extension to calculate compound interest functions

Requires at least: Mediawiki 1.25

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Code is at:https://github.com/owen-kellie-smith/financial-maths-mediawiki/

A wiki using the extension, running MediaWiki 1.25.2 is at http://mediawiki-treaties.rhcloud.com/wiki .

Practice financial mathematics questions for the actuarial exams. Define and calculate interest rates, annuity certain, mortgage repayment, general discrete or continuous cashflows, spot/forward/par yields.

## Description 

This extension provides a special page and tag which render forms that calculate and explain annuities certain, repayment mortgages etc: maths questions which typically appear in the CT1 actuarial exam and the Interest Theory part of the Financial Mathematics exam.


## Installation

1. Download, unzip and upload to your extensions directory.  
1. Add  wfLoadExtension( 'FinancialMathematics' );   to your LocalSettings.php.
1. Go to the new Special page (check in the special pages for "Financial Mathematics").

## How do I run the unit tests? 

Install phpunit and enter

phpunit


