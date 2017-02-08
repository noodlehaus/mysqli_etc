# mysqli_etc

Cruddy functions under the `mysqli` pseudo-namespace. Requires PHP 7.1+

```php
$connection = mysqli_connect($host, $username, $password, $database);

# perform an insert
mysqli_insert(
  $connection,
  'INSERT INTO users VALUES (?, ?, ?)',
  $username,
  $email,
  $password
);

# perform an update/delete
mysqli_update(
  $connection,
  'DELETE FROM users WHERE username=?',
  $username
);

# perform a fetch
mysqli_select(
  $connection,
  'SELECT * FROM users WHERE username=? LIMIT 1',
  $username
);

# create an interpolated sql statement
$statement = mysqli_interpolate(
  $connection,
  'SELECT * FROM users WHERE username=?',
  $username
);
mysqli_stmt_execute($statement);
```

## License

MIT <http://noodlehaus.mit-license.org>
