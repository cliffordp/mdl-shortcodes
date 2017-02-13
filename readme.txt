=== MDL Shortcodes ===
Contributors: cliffpaulick
Tags: mdl, material design lite, google, shortcode, shortcodes
Donate link: http://tourkick.com/pay/
Requires at least: 4.0
Tested up to: 4.3.1
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

This plugin is no longer maintained or supported. Google is no longer maintaining MDL.

== Description ==

From [Google MDL Staff](https://github.com/google/material-design-lite/issues/1206#issuecomment-269769984):
> MDL is no longer being maintained by the core team so much. We are focusing on [Material Components for Web (previously MDLv2)](https://github.com/material-components/material-components-web) and MDL is primarily up to community members to handle at this point.

===

Making it easy to use Google's Material Design Lite (MDL) components in WordPress via shortcodes. MDL components are viewable at http://www.getmdl.io/components/

Integrates with Shortcake (Shortcode UI) so building shortcodes (even ones with complex options) is super easy!

Loads the 3 required files -- Icon Font (0.6 kB), CSS (18.2 kB), and JS (10.3 kB) totaling approximately 29.1 kB (or 0.028 MB) -- on every page load, so feel free to use the MDL Shortcodes (or even your own custom MDL styling) anywhere throughout your site.

There will be a plugin update each time there is a new [MDL Release](https://github.com/google/material-design-lite/releases).

= Highlights =

* Extremely lightweight and optimized by Google!
  * Icon Font, CSS, and JS files load from Google's servers so they're always fast and possibly cached in the user's browser from another site.
  * CSS and JS files are already minified by Google
* Easily use all the MDL Components' features by using the Shortcode UI (available for most but not all MDL Components)
* Most shortcodes appear in Visual Editor for live preview, avoiding the need to preview every change by visiting the front-end.
* Some shortcodes can pull in post information (title, featured image, excerpt) or even WP Nav Menus!
* Internationalized / translatable. Translations not provided but can be started from the included .pot file.
* No WP_DEBUG messages
* Actions and Filters available for developers and advanced customizations
* Responsive plugin developer

= Complimentary Plugins =
These plugins may come in handy when building or customizing a site with MDL Shortcodes:
(may contain affiliate links)

* [Shortcake (Shortcode UI)](https://wordpress.org/plugins/shortcode-ui/) - You'll be prompted to install this one when you install MDL Shortcodes; that's how great it is!
* [Easy Google Fonts](https://wordpress.org/plugins/easy-google-fonts/) - to override MDL using [Roboto](https://www.google.com/fonts/specimen/Roboto) font by default
* **[WP Views](https://wp-types.com/home/views-create-elegant-displays-for-your-content/?aid=5336&affiliate_key=Lsvk04DjJOhq)** or [Shortcode Factory](https://wordpress.org/plugins/shortcode-factory/) - to output post info (Views is paid and much more powerful but Shortcode Factory is free)
* [amr shortcode any widget](https://wordpress.org/plugins/amr-shortcode-any-widget/) - can come in handy if wanting to display widgets in an MDL Grid/Cell

= Acknowledgements =
Special thanks to:

* [Google Material Design Lite](http://www.getmdl.io/) because duh!
* The Shortcake (Shortcode UI) and [Shortcake Bakery](https://wordpress.org/plugins/shortcake-bakery/) developers/contributors. MDL Shortcodes used Shortcake Bakery as a foundation for building upon Shortcake.
* The [TGM Plugin Activation](http://tgmpluginactivation.com/) developers/contributors. It's how Shortcake gets suggested right when you install this plugin.

= Support Me =
* [Leave a great review](https://wordpress.org/support/view/plugin-reviews/mdl-shortcodes?rate=5#postform)
* [View my other plugins](https://profiles.wordpress.org/cliffpaulick/#content-plugins)
* [Hire Me for Customizations](http://tourkick.com/)
* [Contribute code via GitHub](https://github.com/cliffordp/mdl-shortcodes)
* **[Tweet this plugin](https://twitter.com/home?status=I%20love%20the%20free%20%23Google%20%23MDL%20Shortcodes%20plugin%20at%20https%3A//wordpress.org/plugins/mdl-shortcodes/%20-%20Thanks%20%40TourKick%20and%20%40GoogleDesign%20%23WebDev)**


== Installation ==

After automatically installing to wp-content/plugins/:

1. Install the Shortcake (Shortcode UI) plugin (you'll be prompted to do so if it's not already installed and activated). It's optional but highly recommended.
2. Click the "MDL Shortcodes Options" wp-admin menu item (next to "Appearance" menu item) to select your MDL colors.
3. Then just use the shortcodes in any Visual Editor (e.g. Post/Page edit screens). With Shortcake (Shortcode UI) plugin activated you'll be able to click "Add Media" then "Insert Post Element" then select one of the shortcodes to customize.

== Frequently Asked Questions ==
**What shortcodes are available?**

* mdl-badge
* mdl-button
* mdl-card
* mdl-cell
  * *These do the same but exist so they can be nested without closing/breaking parent shorcodes:*
  * mdl-cell-a
  * mdl-cell-b
  * mdl-cell-c
  * mdl-cell-d
  * mdl-cell-e
  * mdl-cell-f
  * mdl-cell-g
  * mdl-cell-h
  * mdl-cell-i
  * mdl-cell-j
  * mdl-cell-k
  * mdl-cell-l
  * mdl-cell-m
  * mdl-cell-n
  * mdl-cell-o
  * mdl-cell-p
  * mdl-cell-q
  * mdl-cell-r
  * mdl-cell-s
  * mdl-cell-t
  * mdl-cell-u
  * mdl-cell-v
  * mdl-cell-w
  * mdl-cell-x
  * mdl-cell-y
  * mdl-cell-z
* mdl-grid (no UI)
  * *These do the same but exist so they can be nested without closing/breaking parent shorcodes:*
  * mdl-grid-a
  * mdl-grid-b
  * mdl-grid-c
  * mdl-grid-d
  * mdl-grid-e
  * mdl-grid-f
  * mdl-grid-g
  * mdl-grid-h
  * mdl-grid-i
  * mdl-grid-j
  * mdl-grid-k
  * mdl-grid-l
  * mdl-grid-m
  * mdl-grid-n
  * mdl-grid-o
  * mdl-grid-p
  * mdl-grid-q
  * mdl-grid-r
  * mdl-grid-s
  * mdl-grid-t
  * mdl-grid-u
  * mdl-grid-v
  * mdl-grid-w
  * mdl-grid-x
  * mdl-grid-y
  * mdl-grid-z
* mdl-icon
* mdl-menu
* mdl-nav (no UI)
* mdl-tab-group (no UI)
* mdl-tab
* mdl-tooltip

**What are some shortcode examples?**

When you install and activate the MDL Shortcodes plugin, a new Page will be created. It will be titled "MDL Shortcodes Plugin Demo Examples" and is only a Draft, not Published. You can click in to edit this page; go to the Text Editor (not Visual Editor) to see the shortcodes in use. Note that this will be the page you preview when clicking the "MDL Shortcodes Options" wp-admin menu link to pick your CSS colors.

For universal reference, here are some shortcode examples:

* MDL Icon with custom color and background color: `[mdl-icon icon="router" color="mdl-color-text--pink" bgcolor="mdl-color--black" class="hello special"]`
* MDL Badge: `[mdl-badge badgetext="Followers" data="74"]`
* MDL Button: `[mdl-button type="fab" icon="flip_to_front" url="http://www.getmdl.io/components/index.html#buttons-section" target="_blank"]`
* MDL Card pulling in a single Post's info and overriding the excerpt text: `[mdl-card postid="382" menu="info" menulink="http://www.getmdl.io/components/index.html#cards-section" menutarget="_blank" mediaplacement="mediaarea" supporting="Overriding excerpt text here... that is, if it had an excerpt." actionstarget="_blank" shadow="2"]`
* MDL Card with manual/custom content: `[mdl-card title="Custom Title Text Here" menu="info" menulink="http://www.getmdl.io/components/index.html#cards-section" menutarget="_blank" supporting="Supporting text here." actions="An MDL Card" actionsicon="event" shadow="2"]`
* MDL Grid with MDL Cell 8 + MDL Cell 4: `[mdl-grid][mdl-cell size=8]something here that will be 8 columns wide[/mdl-cell][mdl-cell]something here that will be 4 columns wide, since 4 is the default size[/mdl-cell][/mdl-grid]`
* MDL Tabs: `[mdl-tab-group][mdl-tab title="Starks" active="true"]content here[/mdl-tab][mdl-tab title="Lannisters"]content here[/mdl-tab][/mdl-tab-group]`
* MDL Menu: `[mdl-menu nav="37"]`
* MDL Tooltip: `[mdl-tooltip text="XML"]eXtensible Markup Language[/mdl-tooltip]`

*Don't forget most of these shortcodes have a user interface (UI) to make it easy to create them (so you don't have to manually enter all that shortcode garbly-gook).*

These components will be styled as seen at http://www.getmdl.io/components/ unless customized.

**Does MDL Shortcodes work with my theme?**

The styling of all these components is essentially self-contained. Due to the nature of implementing the shortcodes (they require the Icon Font, CSS, and JS files to be loaded), this plugin can feel like it "takes over" your theme's styling. In summary, yes, it should WORK with your theme (not cause PHP or other errors), but it may not LOOK GREAT with your theme (although that doesn't mean it can't with some tweaks).

**How can I override the Roboto font being used everywhere?**

This comes from the MDL stylesheet (CSS). It can be easily overridden via [Easy Google Fonts](https://wordpress.org/plugins/easy-google-fonts/).

**Can I use my own version of the CSS (e.g. Sass)?**

Yup. There are a few filters for that.

**Why does this plugin add the MDL styling to the wp-admin area?**

In order to render shortcode previews in the TinyMCE Visual Editor, the 3 MDL files are loaded whenever there's a TinyMCE Visual Editor present. This obviously happens on Page/Post Editing screens, but it can also happen elsewhere in wp-admin. It shouldn't be causing any issues other than styling inconsistencies among wp-admin screens.

== Screenshots ==
1. MDL Shortcodes settings in the WordPress Customizer. Get there by clicking the "MDL Shortcodes Options" wp-admin menu item (next to "Appearance" menu item).

2. Shortcodes render in the wp-admin Visual Editor when Shortcake (Shortcode UI) plugin is active.

3. With Shortcake (Shortcode UI) plugin activated you'll be able to click "Add Media" then "Insert Post Element" then select one of the shortcodes to customize.

4. Example Shortcake (Shortcode UI) interface for building a MDL Button shortcode.

5. Front-end example: MDL Icon, MDL Badge, MDL Button

6. Front-end example: MDL Card pulling in post info (blurred out) and MDL Card with manually-entered text.

7. Front-end example: MDL Grids and MDL Cells

8. Front-end example: MDL Tabs

9. Front-end example: MDL Menu

10. Front-end example: MDL Tooltip

== Changelog ==
*Changelog DIFFs for all versions are available at <a href="http://plugins.trac.wordpress.org/browser/mdl-shortcodes/trunk" target="_blank">WordPress SVN</a>.*
*MDL Releases are viewable at <a href="https://github.com/google/material-design-lite/releases">github.com/google/material-design-lite/releases</a>*

= Version 1.0.3 =
* Move TGM Plugin Activation file to 'inc' directory to avoid it showing up as a separate plugin in site's list of plugins

= Version 1.0.2 =
* Updated to load MDL Version 1.0.6

= Version 1.0.1 =
* Updated to load MDL Version 1.0.5 instead of 1.0.4

= Version 1.0 =
* Initially uploaded to WordPress.org on September 14, 2015
