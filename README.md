# wp-helpers

Helper classes for WordPress plugin development.

The purpose of these classes is to simplify and speed up the development of plugins and themes.


## Admin class

Common methods used from the WordPress admin.

```
# Creates a nonce based on a seed (or by default the project FILE constant).
Admin::createNonce();

# Verifies the WP nonce from a post submit key (will be prefixed)
Admin::verifyNoncePosted('myparam');

# Verifies a nonce value based on a seed (the key will not be prefixed)
Admin::verifyNonceValue('otherparam'); // It does not add prefixes to key strings

# Check if a given screen belongs to the slug
Admin::screenOf(($screen, $slug);
