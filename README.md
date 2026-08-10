# ns_theme_freelancer

- LIVE Demo: https://demo.t3terminal.com/?theme=t3t-freelancer
- FREE version: You can download FREE version with basic-features at https://extensions.typo3.org/extension/ns_theme_freelancer/
- PRO version: You can download PRO version with more-features & free-support at https://t3terminal.com/t3-freelancer-free-typo3-portfolio-template

## Demo page tree (Initialisation)

On a **fresh / empty** TYPO3 site (no pages yet), install the theme dependencies, then run:

```bash
vendor/bin/typo3 extension:setup
```

That imports the demo page tree from `Initialisation/Demo/data.xml` **only when `pages` is empty**, copies assets from `Initialisation/Files/` into `fileadmin/ns_theme_freelancer`, and creates `config/sites/ns_theme_freelancer/` when that site identifier does not exist yet.

### Existing sites are never overwritten

If the installation already has any pages (for example 5 pages you created yourself), `extension:setup` will **not** import the demo tree and will **not** change your pages. A registry marker is stored so the skip stays permanent.

### Safe re-runs

Running `extension:setup` again after a successful empty-site import does not re-import or wipe content.

### Force re-import (dev only)

Only on a disposable instance:

1. Remove demo pages / reset DB if needed
2. Delete registry rows for this package, e.g. `extensionDataImport` keys containing `ns_theme_freelancer/Initialisation/Demo`
3. Optionally remove `siteConfigImport` / `config/sites/ns_theme_freelancer`
4. Run `vendor/bin/typo3 extension:setup` again on an empty page tree
