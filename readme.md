# WP CrossRef DOI Plugin

This WordPress plugin allows you to generate DOIs (Digital Object Identifiers) for your conference papers using the CrossRef API.

## Features

-   Generates unique DOIs for conference papers
-   Adds a 'DOI' button to the post administration screen for easy access
-   Stores DOIs in custom fields of the posts
-   Allows users to fill in metadata for conference papers
-   Generates XML files based on CrossRef's best practice examples
-   Validates XML files using CrossRef's testing tools
-   Submits the XML files to CrossRef via HTTPS POST
-   Handles errors and displays responses from the CrossRef API

## Installation

1.  Download the plugin files from the [GitHub repository](https://github.com/docvinum/Wordpress-DOI-plugin).
2.  Extract the contents of the downloaded ZIP file.
3.  Upload the extracted `wp-crossref-doi` folder to your WordPress installation's `wp-content/plugins/` directory.
4.  Log in to your WordPress admin area, navigate to the 'Plugins' page, and locate the 'WP CrossRef DOI' plugin.
5.  Click 'Activate' to enable the plugin.

## Configuration

1.  In your WordPress admin area, navigate to 'Settings' > 'WP CrossRef DOI'.
2.  Enter your CrossRef login credentials (login_id and login_passwd) for both the production and test environments.
3.  Save your settings.

## Usage

1.  In the WordPress post administration screen, locate the desired conference paper post.
2.  Click the 'DOI' button under the post title.
3.  Fill in the metadata fields for the conference paper.
4.  Save the metadata.
5.  The plugin will validate the generated XML file and submit it to the CrossRef API (either the test or production environment, depending on your settings).
6.  View the API response and handle any errors if necessary.

## License

This plugin is licensed under the [GNU General Public License v2.0](https://www.gnu.org/licenses/gpl-2.0.html).

## Support and Contributions

If you encounter any issues or have suggestions for improvements, please [create an issue](https://github.com/docvinum/Wordpress-DOI-plugin/issues) on GitHub. Contributions to the plugin's development are welcome. Please submit a pull request with your proposed changes.

**Note:** Before deploying the plugin on a production site, make sure to test it thoroughly in a development environment to identify and resolve any potential issues.