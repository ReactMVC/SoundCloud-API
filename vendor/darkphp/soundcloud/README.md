# SoundCloud PHP Library

The SoundCloud PHP Library provides a convenient way to interact with the SoundCloud API and perform various actions such as searching for music, retrieving music details, downloading music, and more.

## Installation

To install the SoundCloud PHP Library, add the following line to your `composer.json` file and run the `composer require` command:

```bash
composer require darkphp/soundcloud
```

## Usage ( php +8.1 )
```php
<?php
require_once 'vendor/autoload.php';

use DarkPHP\SoundCloud;

// Create a new instance of the SoundCloud class
$soundCloud = new SoundCloud();

// Search for music
$query = 'your_search_query';
$musicResults = $soundCloud->searchMusic($query);
var_dump($musicResults);

// Get music details
$musicId = 'your_music_id';
$musicDetails = $soundCloud->getMusic($musicId);
var_dump($musicDetails);

// Download music
$musicId = 'your_music_id';
$soundCloud->downloadMusic($musicId);

// Get music details by URL
$url = 'https://soundcloud.com/user/track';
$musicByUrl = $soundCloud->getMusicWithUrl($url);
var_dump($musicByUrl);
```

## Methods

### searchMusic(string $query): stdClass|array

Searches for music tracks based on the provided query. Returns an array of music tracks found matching the query.

### getMusic(string $id, bool $exportOriginal = false): bool|stdClass|array

Retrieves the details of a specific music track identified by its ID. Returns the details as an array or a `stdClass` object. If `$exportOriginal` is set to `true`, the method returns the original data as a `stdClass` object.

### downloadMusic(string $id): void

Downloads a music track identified by its ID. The downloaded file will be saved with the track's title as the filename.

### getMusicWithUrl(string $url): bool|stdClass|array

Retrieves the details of a music track based on the provided SoundCloud URL. Returns the details as an array or a `stdClass` object.

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Developer 
SoundCloudPHP is developed by Hossein Pira.
- Email: h3dev.pira@gmail.com 
- Telegram: @h3dev

If you have any questions, comments, or feedback, please feel free to contact John via email or Telegram.
