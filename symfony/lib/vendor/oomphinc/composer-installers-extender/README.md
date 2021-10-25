# Composer Installers Extender

The `composer-installers-extender` is a plugin for [Composer](https://getcomposer.org/) that allows
any package to be installed to a directory other than the default `vendor/` directory within
the repo on a package-by-package basis. This plugin extends the [`composer/installers`](https://github.com/composer/installers)
plugin to allow any arbitrary package type to be handled by their custom installer and specified explicitly in the
`"installer-paths"` mapping in the `"extra"` data property.

`composer/installers` has a finite set of supported package types and we recognize the need for
any arbitrary package type to be installed to a specific directory other than `vendor/`. This plugin
allows additional package types to be handled by `composer/installers`, benefiting from their explicit install path
mapping and token replacement of package properties.

## How to Use
Add `oomphinc/composer-installers-extender` as a dependency of your project.
```sh
composer require oomphinc/composer-installers-extender
```
`composer/installers` is a dependency of this plugin and will be automatically required as well.

To support additional package types, add an array of these types in the `"extra"` property in your `composer.json`:
```
	"extra": {
		"installer-types": ["library"]
	}
```
Then, you can add mappings for packages of these types in the same way that you would add package types
that are supported by [`composer/installers`](https://github.com/composer/installers#custom-install-paths):
```
  "extra": {
    "installer-types": ["library"],
    "installer-paths": {
      "special/package/": ["my/package"],
      "path/to/libraries/{$name}/": ["type:library"]
    }
  }
```
By default, packages that do not specify a `type` will be considered type `library`. Adding support for this type
allows any of these packages to be placed in a different install path.

If a type has been added to `"installer-types"`, the plugin will attempt to find an explicit installer path in the mapping.
If there is no match either by name or by type, the default installer path for all packages will be used instead.

Please see the README for [`composer/installers`](https://github.com/composer/installers) to see the supported
syntax for package and type matching as well as the supported replacement tokens in the path (e.g. `{$name}`).
