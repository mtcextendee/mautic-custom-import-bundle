# MauticCustomImportBundle

This plugin allow 

- create imports from CSV files from directory
- run parallel import process by command

## Support

https://mtcextendee.com/plugins

## Installation

### Command line
- `composer require mtcextendee/mautic-custom-import-bundle`
- `php app/console mautic:plugins:reload`
-  Go to /s/plugins and setup CustomImport integration

### Manual 
- Download last version https://github.com/mtcextendee/mautic-custom-import-bundle/releases
- Unzip files to plugins/MauticCustomImportBundle
- Clear cache (app/cache/prod/)
- Go to /s/plugins/reload
- Setup CustomImport integration

## Usage

1, Setup plugin

1, See new item on left menu:

![image](https://user-images.githubusercontent.com/462477/61192212-1567ed00-a6b3-11e9-971e-eb3ab3df6beb.png)

2, Setup SQL conditions

3, Parameters for SQL

- :contactId
- :campaignId
- :eventId
- :rotation

4, Condition return true If there is results

## Credits

<div>Icons made by <a href="https://www.flaticon.com/authors/chanut" title="Chanut">Chanut</a> from <a href="https://www.flaticon.com/"                 title="Flaticon">www.flaticon.com</a>
