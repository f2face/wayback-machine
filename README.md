# Wayback Machine
Wayback Machine (Archive.org) API wrapper.

Info: https://archive.org/help/wayback_api.php

## Installation
### With Composer
> composer require f2face/wayback-machine

### Without Composer
[Download this package](https://gitlab.com/f2face/wayback-machine/repository/archive.zip?ref=master "Download source code")

## Usage Example
~~~~
use f2face\WaybackMachine;

// Initialize
$wb = new WaybackMachine();

// Check web page availability
$url_to_check = 'https://gitlab.com';
var_dump($wb->available($url_to_check));

// Save a web page
$url_to_save = 'https://gitlab.com';
var_dump($wb->save($url_to_save));
~~~~