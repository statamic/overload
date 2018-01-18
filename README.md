# Overload for Statamic ![Statamic v2](https://img.shields.io/badge/statamic-v2-blue.svg?style=flat-square)

CLI commands to create test and sample content. Useful for testing loads or while waiting around for client content.

## Usage
- Install the addon by copying the files into `site/addons/Overload`.
- Run `php please update:addons` to install dependencies
- Run the `please` command.

## Commands
Running the `please` commands without any arguments will give you interactive prompts to help you create your content.

### Entries
```
php please overload:entries [<folder>] [<count>]
```

### Users
```
php please overload:users [<count>]
```
