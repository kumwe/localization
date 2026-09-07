# Runnable examples

After `composer install`, run `php examples/translate.php` to resolve and format a complete catalogue.
It prints `Hello, Kumwe!` and closes the operation context in finally. No App or test fixture is needed.

Run `php examples/container.php` to verify the same ports with a real Laminas ServiceManager. The host
must install laminas/laminas-servicemanager; package development and the isolated consumer gate do so.

Both scripts accept the consumer's Composer autoloader as their first argument when run inside vendor.
The archive gate exercises that installed-dependency path with authoritative autoloading and no dev files.
