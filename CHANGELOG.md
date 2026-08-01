# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-08-01

### Fixed
- Removed `.gitattributes` `export-ignore` rules that stripped `tests/`, `.github/`, and tooling
  config from `composer create-project` downloads, defeating the purpose of the skeleton.

## [1.0.0] - 2026-08-01

### Added

- Initial release: skeleton provider implementation, stub test, CI workflow, tooling config.

[1.0.1]: https://github.com/arielespinoza07/plow-provider-skeleton/releases/tag/v1.0.1
[1.0.0]: https://github.com/arielespinoza07/plow-provider-skeleton/releases/tag/v1.0.0
