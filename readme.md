# Movies2sheet

Transform your **own device full of movies** in your own **\*\*\*flix**.

⚠️ **UNDER ACTIVE DEVELOPMENT**

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
- Composer
- FFPROBE
- A TMDb API Key

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
| API_LANG |  |
| API_KEY |  |

## Commands

Launch a command: `php m2s [COMMAND]`

| Commande | Description | Options |
|----------|-------------|---------|
| `build` |  |  |
| `volume` |  |  |
| `database:build` |  |  | 
| `database:ffprobe` |  |  | 
| `database:tmdb` |  |  | 
| `frontend:build` |  |  |
| `frontend:serve` |  |  |

## Useful documentation

- [symfony/console](https://symfony.com/doc/5.1/components/console.html)
- [symfony/finder](https://symfony.com/doc/5.1/components/finder.html)
- [guzzlehttp/guzzle](https://docs.guzzlephp.org/en/stable/)
- [ffprobe](https://ffmpeg.org/ffprobe.html)
- [TMDb](https://developers.themoviedb.org/3/getting-started/introduction)

## Licence

MIT