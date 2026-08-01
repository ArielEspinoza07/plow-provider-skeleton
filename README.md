# Plow Provider Skeleton

Starting point for a [Plow](https://github.com/ArielEspinoza07/plow) provider, built on
[plow-provider-kit](https://github.com/ArielEspinoza07/plow-provider-kit).

## Getting started

1. `composer create-project arielespinoza07/plow-provider-skeleton vendor-name/plow-provider-your-tool`
2. Find-and-replace `Vendor\PlowProviderSkeleton` → your own namespace, and `vendor/plow-provider-skeleton` → your real package name, across `composer.json`, `src/`, and `tests/`.
3. Rename `SkeletonProvider` to something specific to your tool.
4. Fill in the `TODO` comments in `src/YourProvider.php`: task, binary path, command building.
5. `composer install`, then `vendor/bin/pest` to confirm the stub test passes.
6. Publish, and add `"type": "plow-provider"` stays as-is — that's what Plow's discovery looks for.

See [plow-provider-kit's README](https://github.com/ArielEspinoza07/plow-provider-kit) for
the full list of available contracts and value objects.