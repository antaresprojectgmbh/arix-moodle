## Antares
Antares is a system for the distribution of digital educational content.

This plugin allows you to search for media content in Antares as well as adding these to courses.

## How to Install
- Request an API key for your IP address.
- Copy the content of this repository to repository/arix.
- Go to the admin notifications page to install the plugin.
- Navigate to Settings > Site administration > Plugins > Repositories to enable it.
- Create a new instance of the plugin.
- Fill in the fields below
  - Name: The display name of the plugin.
  - Arix URL: An optional URL to an Arix API provider. If no URL is specified, then http://arix.datenbank-bildungsmedien.net/ is used.
  - Context: {HE/30/030999} [Country] / [location number] / [school number]

### Context
The context can also be shortened:
NRW is e.g. The general context North Rhine-Westphalia.
NRW/VIE is the general context for the Viersen site in NRW.
In the case of a search query, only the media that are available for this context is output. In the first case, therefore, only country licenses, in the second country licenses and district licenses.

Note: The plugin refers to external content. It is therefore necessary to add content as link/URL.