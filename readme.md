# Movies2sheet

**Movies2sheet** is a command line tool useful to transform a list of movies into a CSV file.

## Use case

I downloaded (legally 🙃) several movies and put them into a **Volume** (usually, an external drive).
Now, I want a CSV file containing all of my downloaded movies, plus, additional metadata.

Movies2sheet will:
__TODO__

This package is for my own usage (developped in about 2 hours) but I share it with all of you with ♥️

## Requirements

- PHP >= 7.4
- Composer
- FFPROBE
- An TMDb API Key

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
| `tmdb` | Génére les infos TMDb de chaque film dans le volume | `--export` (ou `-e`) : Génère un fichier d'export au format .csv |
| `debug` | Dump les infos d'un film | Slug du film, ex: `prometheus` |

## Useful documentation

- [symfony/console](https://symfony.com/doc/5.1/components/console.html)
- [symfony/finder](https://symfony.com/doc/5.1/components/finder.html)
- [guzzlehttp/guzzle](https://docs.guzzlephp.org/en/stable/)
- [ffprobe](https://ffmpeg.org/ffprobe.html)
- [TMDb](https://developers.themoviedb.org/3/getting-started/introduction)

## Licence

MIT