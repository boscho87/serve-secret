# ServeSecret plugin for Craft CMS 3.x

Serve files that are not Stored in public accessable Directories. e.g for Password Protected Areas. File links could and should not be shared!


<img src="https://github.com/boscho87/serve-secret/blob/master/resources/img/icon.svg" width="48">


## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Contribute to the Project
if you want to help with this project, [read how to contribute](CONTRIBUTE.md)

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require boscho87/serve-secret

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for ServeSecret.


## Using ServeSecret

![Screenshot](resources/img/volume.png)
1. Crate a volume

2. Add this to your template
```twig
<-- just use the "secretFile" function and add the Asset Object into it -->
<a href="{{ secretFile(entry.files.first()) }}">Download Secret File</a>
```

## ServeSecret Roadmap

Some things to do, and ideas for potential features:

* Implement possibility to share the file-links

Brought to you by [Simon Müller](https://itscoding.ch)
