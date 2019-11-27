# Movies2sheet

**Movies2sheet** is a command line tool useful to transform a list of movies into a CSV file.

## Use case

I downloaded (legally 🙃) several movies and put them into a **Volume** (usually, an external drive).
Now, I want a CSV file containing all of my downloaded movies, plus, additional metadata.

Movies2sheet will:
- Get data from **FFMPEG** such as: duration, size, audio tracks, subtitles, etc
- Get data from [SensCritique.com](https://www.senscritique.com) such as: director, actors, genre, poster, resume, etc

This package is for my own usage (developped in about 2 hours) but I share it with all of you with ♥️

## Requirements

- PHP >= 7.2
- Composer
- FFMPEG

⚠️ Files in your volume need to be named as following:
```
name 2019.ext
# Ex: Forrest Gump 1994.mkv
name [info] 2019.ext
# Ex: Spirited Away [Sen to Chihiro no Kamikakushi] 2001.mp4
```

## Installation

```
git clone https://github.com/MarceauKa/movies2sheet && cd movies2sheet
composer install
cp .env.example .env
```

## Config

Open the `.env` file then edit:

```
VOLUME_PATH=/Volumes/ # Path to the volume
FFMPEG_BIN=/usr/local/bin/ffmpeg # FFMPEG bin path
EXPORT_NAME=movies.csv # Export name located in exports/
```

## Run

```
php index.php app:generate # Generate your export
php index.php app:list # List files in your volume
```

You can get aditional help about command running `php index.php app:generate --help`.

## Example

I have a volume `/Volumes/Toshiba/Movies/` (my external HDD) containing the following files:
- Fight Club 1999.mkv
- Old Boy 2003.mkv
- World War Z 2009.mkv

The command `php index.php app:generate` will output:
```
Name;Year;Duration;Audio;Subs;Format;HDR;Size;Director;...
Fight Club;1999;2h19;EN,FR;FR;Full HD;No;2.64G;David Fincher;...
Old Boy;2003;2h0;FR,KO;FR;HD;No;2.45G;Park Chan-wook;...
World War Z;2009;2h3;FR,EN;FR,EN;4k;Yes;5.65G;Marc Forster;...
```

## Tests

This app contains a lot of tests (Unit, Feature and Integration tests). Just run:

```bash
php index.php app:generate
```

If you see this, **movies2sheet** isn't working.

![Tests](/tests.png?raw=true "Tests")

## Licence

WTFPL - Do What The Fuck You Want To Public License.

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
Version 2, December 2004

Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

Everyone is permitted to copy and distribute verbatim or modified
copies of this license document, and changing it is allowed as long
as the name is changed.

DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

0. You just DO WHAT THE FUCK YOU WANT TO. 
