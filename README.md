# ServeSecret Craft CMS 4.x plugin

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/b1770367482b4ef48a2c3839b5cb881a)](https://www.codacy.com/gh/boscho87/serve-secret/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boscho87/serve-secret&amp;utm_campaign=Badge_Grade)

<img src="./resources/icon.svg" width="150">

Spend
--- 
[Spend (to PayPal)](https://www.paypal.com/donate/?hosted_button_id=KS6KTZ6QJ8DBL)

![QRCode (to PayPal)](.github/QR-Code.png)

___

Serve files that are not Stored in public accessable Directories. e.g for Password-Protected Areas. File links could and should not be shared!

The file links are decoded to hide the path from the user and to make the link only accessable for the current Session, so that's the reason why it's not possible to share the links!

## Requirements

This plugin requires Craft CMS 4.0.0 or later.

## Contribute to the Project

if you want to help with this project, [read how to contribute](CONTRIBUTE.md)

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require itscoding/serve-secret

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for ServeSecret.


## Using ServeSecret

![Screenshot](resources/img/volume.png)

1. Crate a volume in craft (start with `@webroot` is recommended but it should work also with absolute paths

2. Use it in your templates --> put your asset as an method argument!


```twig
# the second parameter is optional, if is set to false, the file will download instead of open in the browser,the default value is true
<a href="{{ secretFile(entry.files.first(),false) }}">{{ entry.files.first().title }}</a>

{% for files in entry.files %}
       <a href="{{ secretFile(file,true) }}">{{ file.title }}</a>
{% endfor %}

```

## ServeSecret Roadmap

Some things to do, and ideas for potential features:

* Implement possibility to share the file-links

Brought to you by [Simon Müller](https://itscoding.ch)
