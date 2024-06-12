# PHSD
PHSD is a PHP Library for handling data storage with expiration.
```
// Example usage:
PHSD::add('name', 'John', 5); // Add data with 5-minute expiration
print PHSD::get('name'); // Should print 'John'

PHSD::update('name', 'Jane', 10); // Update data with 10-minute expiration
print PHSD::get('name'); // Should print 'Jane'

PHSD::remove('name'); // Remove data
print PHSD::get('name'); // Should print null
```
