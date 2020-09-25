# MauticCustomImportBundle

This plugin allow 

- create imports from CSV files from directory
- run parallel import process by command
- remove tags by command

## Support

https://mtcextendee.com/plugins

## Installation

### Command line
- `composer require mtcextendee/mautic-custom-import-bundle`
- `php bin/console mautic:plugins:reload`
-  Go to /s/plugins and setup CustomImport integration

### Manual 
- Download last version https://github.com/mtcextendee/mautic-custom-import-bundle/releases
- Unzip files to plugins/MauticCustomImportBundle
- Clear cache (app/cache/prod/)
- Go to /s/plugins/reload
- Setup CustomImport integration

## Usage

### Create imports from directory

Command: `php bin/console mautic:import:directory`

Setup in plugin settings 
- **Template from existed import** - it's import from history which is used as template. Especially config (especially parsers config and matched contact fields)
- **Path to directory with CSV files**. It's server path where you can store unlimited CSV files

Command read this directory and create standard import from CSV with settings from template.

### Run parallel import process

Command: `php bin/console mautic:import:parallel`

First, increase **parallel_import_limit** in app/config/local.php. Don't forgot clear cache (app/cache/prod/)
This option allow run parallel import process by command

Each command in parallel processes import 1000 contacts by default. You can change it in plugin settings (Import records limit)

### Remove tags by command

Command: `php bin/console mautic:remove:tags`

If you want tag your contacts by import, then before import you are able to remove tag.
Just setup tags in plugin settings and call command


## Credits

<div>Icons made by <a href="https://www.flaticon.com/authors/chanut" title="Chanut">Chanut</a> from <a href="https://www.flaticon.com/"                 title="Flaticon">www.flaticon.com</a>
