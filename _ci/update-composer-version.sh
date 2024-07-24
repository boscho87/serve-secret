#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Update the version in composer.json
jq --arg version "$1" '.version = $version' composer.json > tmp.$$.json && mv tmp.$$.json composer.json
