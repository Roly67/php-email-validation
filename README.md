# PHP Email Validation
Here's how to implement email address validation on your site using a very simple PHP script.

## Important Disclaimer
This script is provided AS IS for educational purposes only.

Script is NOT designed for industrial application and has 
several known key deficiencies in error tolerance 
and accuracy / coverage of email address queries.

No warranty or support is provided. Use of this script 
is at your own risk.

## Usage Warning
Many hosting companies do not allow SMTP send 
operations. Please get permission from your hosting provider 
before deploying this script.

## License
This script is licensed under [Apache 2.0](http://www.apache.org/licenses/).

## Installation

### Hosting and Platform Requirements

 * PHP capable hosting (e.g. Apache)
 * PHP version > 5.x
 * Hosting provider with port 25 unblocked

### Configuration
There is only one thing to configure in this script.

Please set **$FROM** (line 47) to an email address that makes sense. 
For example, if you're installing this script on domain "mydomain.com", an 
appropriate setting for **$FROM** might be "verify.email@mydomain.com".
 
### How to Install

 * Please configure **$FROM** setting as above in "Configuration"
 * Upload script "emailverify.php" to hosting provider.
 
### Running the Script
Once uploaded, you can access the script by loading the script in your browser. 
For example, if you uploaded the PHP script to the root of your domain "mydomamin.com", 
access is browser @ http://mydomain.com/emailverify.php

### Limitations
This is a **very simple** script and as such suffers from a number of limitations:

 * _Limited coverage_ - does not work very well with some mail services (e.g. Hotmail, Yahoo)
 * _Intermittent Connectivity_ - Your servers IP address can be temporarily or permanently banned with excessive use of this script. When IP blocks happen, the script stops working.
 * _Incorrect Results_ - Results can come back as "Ok" when infact the actual answer is "Bad" (confirmed by sending email to address). This is because script has no way of identifying "catch all" domains. Yahoo is a "catch all" domain and answers "Ok" to all queries.

### Where to Find Accurate Email Verification
Industry grade, accurate email verification is a technically advanced and challenging process. Getting it right needs a lot of infrastructure, 
knowledge and software.

The simple PHP script provided in this Github repository is provided for illustration purposes only and certainly 
not good enough for serious applications.

For industrial email validation please see my enterprise grade [email verification](https://www.emailhippo.com/en-US) solution 
provided as a Software as a Service (SaaS) for both bulk list and real time API applications.
