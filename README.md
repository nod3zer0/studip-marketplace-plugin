author: René Češka <xceska06@stud.fit.vutbr.cz>

# Catalog plugin

This folder contains Catalog plugin, originally known as Marketplace plugin.
The purpose of this plugin is to allow users to create demands or offers for a wide range of commodities. For example, the catalog can be utilized to find team members for projects, instructors for courses, collaborators for research endeavors and more. Search engines are central to this plugin, as they offer varying levels of complexity, making it easy for users to search for anything they specify.

The plugin is customizable and allows for having multiple catalogs with different types of commodities. Moreover, all of the pages within the plugin have help functionality which contains information on how to use that part of the plugin to help users.

More detailed description is available in bachelor theses for which it was developed. To help with getting started, when user accesses plugin for the first time, there is tour that explains features of the plugin. There is also useful information in help menus.

## Roles

- admin - configures plugin

- tutor, dozent, autor - uses all other functionality apart from configuration

## Installation

Plugin is installed same way as any other Stud.IP plugin. This means that `marketplace.zip` should be uploaded and enabled in `admin -> system -> plugins`.

## Sending mails

To enable sending mails it is needed to configure Stud.IP mail API.

## Directory structure

Directory structure is same as is recommended in Stud.IP plugin tutorials. Here are explained files that are not defined by Stud.IP plugin tutorials. Files themselves are described in their headers.

- classes/
    - search/: classes that are used for generation of SQL for search engines
        - ...
    - StudIPSqlSearches/: classes that are used for simple autocomplete for search engines
        - SimpleSearchStudIp.php
        - CustomPropertySearchStudIp.php
        - DefaultPropertySearchStudIp.php
- Makefile - creates zip file
- migrations/ - sets up database and other migrations
    - ...
- README.md
- views/
    - ...
- assets/ - vue.js scripts and css
    - ...
- controllers/
    - ...
- Marketplace.class.php - initializes plugin and sets up navigation
- models/
    - ...
- LICENSE.txt
- marketplace.zip: compressed plugin prepared for uploading to Stud.IP
- plugin.manifest
- demo_data.json: demo data that can be uploaded into plugin


## Administrator configuration

Here is explained configuration that is managed by administrator.

### Adding catalog

Catalog can be added in tab `Catalog->config`. By default it will be disabled and it is needed to enable it. There is also button that leads to catalog configuration.

### Catalog configuration

Here can be set what should commodities be called, categories that should available in this catalog and properties that should each commodity have.

### Import/Export

This feature is meant to transfer data when migrating Catalog plugin to another instance. It is available in `Catalog -> Config -> Export/Import`. Data are exported in JSON format. When importing data all of the already existing data are lost.

## How to use plugin

Information on how to use plugin is in help menus and help tour that starts on first load of the plugin and can be restarted in help menus.





