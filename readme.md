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
- FFPROBE

### Some doc

- [symfony/console](https://symfony.com/doc/5.1/components/console.html)
- [symfony/finder](https://symfony.com/doc/5.1/components/finder.html)
- [ffprobe](https://ffmpeg.org/ffprobe.html)
- [TMDb](https://developers.themoviedb.org/3/getting-started/introduction)

### File names

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

## Configuration

Open the `.env` file then edit:

| Config | Description |
|--------|-------------|
| VOLUME_PATH |  |
| EXTENSIONS |  |
| FFPROBE_BIN |  |
| API_KEY |  |

## Commandes

Les commandes se lancent par la commande de base : `php index.php [COMMAND]`

| Commande | Description | Options |
|----------|-------------|---------|
| `overview` | Liste les films du volume | 
| `ffprobe` | Génére les infos ffprobe de chaque film dans le volume | `--export` (ou `-e`) : Génère un fichier d'export au format .csv |
| `debug` | Dump les infos d'un film | Slug du film, ex: `prometheus` |

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
