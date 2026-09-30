# Releasing

1. Refresh local tags from GitHub: `git fetch --tags --force origin`.
2. Bump `<version>` in `mod_prettylinks.xml` and add an entry for the version to `changelog.xml`.
3. Set `.github/release-description` (one line) and, if the supported Joomla versions change, `.github/release-targetplatform` (a regex matched against the Joomla version, e.g. `(5\.4|6)`).
4. Commit these changes to `main` and push.
5. Tag that commit `V<version>` and push the tag. The Release workflow downloads the tag archive, computes the sha256 and adds the entry to `updates.xml` on `main`.
6. Pull `main`, check the new `updates.xml` entry, and confirm the update is offered on a test site.
