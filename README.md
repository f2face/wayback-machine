# Wayback Machine
Wayback Machine (Archive.org) API wrapper.

Info: https://archive.org/help/wayback_api.php


# Installation
> composer require f2face/waybcak-machine


# Usage Example
~~~~
use f2face\WaybackMachine;

$url_to_check = 'https://gitlab.com';

$wb = new WaybackMachine();
var_dump($wb->available($url_to_check));
~~~~