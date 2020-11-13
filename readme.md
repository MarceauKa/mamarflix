# Mamarflix

Transform your **own device full of movies** in your own local **Netflix**. Want a demo? [Here is it](https://marceauka.github.io/mamarflix) (movies not included).

⚠️ This tool does not encourage anyone to illegally download movies. I made this because I have a BIG collection of DVD (and Blu-ray) gathering dust. If you want to rip your physic movies, look at an [other package](https://github.com/MarceauKa/ffmpeg-generator) I made.

## Use case

You have a **Volume** (External hard drive, for example) full of movies? This app will transform it in your own web platform.

It will:
- Get file infos (size, duration, format, HDR, audio and subtitles)
- Get movie infos (title, original title, release date, casting, resume, poster, etc)
- Dump all of these infos into a CSV and a JSON database
- Generate a web interface to browse your movies

This package is for my own usage but I share it with all of you with ♥️

## Requirements

- PHP >= 7.4
- [Composer](https://getcomposer.org/)
- [ffprobe](https://ffmpeg.org/ffprobe.html)
- A [TMDb API Key](https://www.themoviedb.org/documentation/api)

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
git clone https://github.com/MarceauKa/mamarflix && cd mamarflix
composer install
cp .env.example .env
```

## Configuration

Open the `.env` file then edit:

| Config | Description | Default |
|--------|-------------|---------|
| VOLUME_PATH | Path of your volume | `/Volumes/` |
| EXTENSIONS | File extensions used to find movies in the volume | `.mkv,.avi,.mov,.mp4` |
| FFPROBE_BIN | Path to ffprobe binary | `/usr/local/bin/ffprobe` |
| API_LANG | Lang to be used for movies (and posters) | `fr` |
| API_KEY | Your TMDb API key | |

## Commands

Launch a command: `php m2s [COMMAND]`

| Commande | Description | Options |
|----------|-------------|---------|
| `build` | Build the entire app (`database:ffprobe`, `database:tmdb` and `frontend:build`) | - |
| `volume` | List movies on the volume | - |
| `database:build` | Build `data/database.json` from `database:ffprobe` and `database:tmdb` |  | 
| `database:ffprobe` | Get ffprobe data from each movies in the volume | `-e` will export result in `data/exports/ffprobe.csv` | 
| `database:tmdb` | Interactively retrieve movies data from TMDb | `-e` will export result in `data/exports/tmdb.csv` | 
| `frontend:build` | Build app frontend to the volume, copying all movies posters | `-d` (dev only) build public demo into `docs/` |
| `frontend:serve` | (dev only) Start the frontend server | - |

## Useful documentation

- [symfony/console](https://symfony.com/doc/5.1/components/console.html)
- [symfony/finder](https://symfony.com/doc/5.1/components/finder.html)
- [guzzlehttp/guzzle](https://docs.guzzlephp.org/en/stable/)
- [ffprobe](https://ffmpeg.org/ffprobe.html)
- [TMDb](https://developers.themoviedb.org/3/getting-started/introduction)

## Licence

MIT