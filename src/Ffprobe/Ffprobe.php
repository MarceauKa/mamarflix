<?php

namespace App\Ffprobe;

use App\Movie;
use App\Utils\Cache;
use App\Utils\Env;
use Illuminate\Support\Collection;

class Ffprobe
{
    public ?Movie $movie;
    public ?Collection $infos;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
        $this->getFileInfos();
    }

    public function getSize(): ?string
    {
        $size = $this->infos['format']['size'];

        if (empty($size)) {
            return null;
        }

        $sz = 'BKMGTP';
        $factor = floor((strlen($size) - 1) / 3);

        return sprintf("%.2f", $size / pow(1024, $factor)) . @$sz[$factor];
    }

    public function getDuration(): ?string
    {
        $duration = (int)$this->infos['format']['duration'];

        if (empty($duration)) {
            return null;
        }

        return gmdate("H\hi", $duration);
    }

    public function getVideoFormat(): ?string
    {
        $video = $this->getVideoStream();

        if (empty($video)) {
            return null;
        }

        $width = $this->findFirstKeyIn($video, ['width'], 0);

        if ($width >= 2000) {
            return '4k';
        } else if ($width >= 1900) {
            return '1080p';
        } else if ($width >= 1200) {
            return '720p';
        } else {
            return 'SD';
        }
    }

    public function getVideoHasHdr(): bool
    {
        $video = $this->getVideoStream();

        if (empty($video)) {
            return false;
        }

        $colors = $this->findFirstKeyIn($video, ['color_space', 'color_primaries'], 'bt');

        return stripos($colors, 'bt2020') === 0;
    }

    public function getAudioTracks(): array
    {
        return $this->getAudioStreams()->transform(function ($audio) {
            $layout = $this->findFirstKeyIn($audio, ['channel_layout'], null);

            if (preg_match('/\d\.\d/', $layout)) {
                $layout = substr($layout, 0, 3);
            }

            $lang = $lang = $this->findFirstKeyIn(
                $audio['tags'] ?? [],
                ['lang', 'language', 'LANG', 'LANGUAGE'],
                'N.C'
            );

            $lang = $this->replaceLangCode($lang);

            return sprintf('%s %s', $lang, $layout);
        })->values()->toArray();
    }

    public function getSubtitleTracks(): array
    {
        return $this->getSubtitleStreams()->transform(function ($sub) {
            $lang = $this->findFirstKeyIn(
                $sub['tags'] ?? [],
                ['lang', 'language', 'LANG', 'LANGUAGE'],
                'N.C'
            );

            return $this->replaceLangCode($lang);
        })->values()->toArray();
    }

    protected function getFileInfos(): self
    {
        $cacheKey = sprintf('ffprobe-%s.json', $this->movie->getSlug());

        if (Cache::has($cacheKey)) {
            $infos = Cache::get($cacheKey);
            $this->infos = Collection::make(json_decode($infos, true));

            return $this;
        }

        $command = vsprintf('%s %s %s', [
            Env::get('FFPROBE_BIN'),
            '-loglevel quiet -show_format -show_streams -print_format json',
            escapeshellarg($this->movie->getPath())
        ]);

        $infos = shell_exec($command);

        if (empty($infos)) {
            throw new \RuntimeException("Can't get infos for {$this->movie->getPath()}");
        }

        Cache::set($cacheKey, $infos);

        try {
            $this->infos = Collection::make(json_decode($infos, true));
        } catch (\Exception $e) {
            throw new \RuntimeException("Can't decode JSON infos from {$this->movie->getPath()}");
        }

        return $this;
    }

    protected function replaceLangCode($code): ?string
    {
        if (!empty($code)) {
            return str_replace([
                'fre',
                'fra',
                'fr',
                'eng',
                'en',
                'spa',
                'jpn',
                'jap',
                'srp',
                'dut',
                'nld',
                'nl',
                'ces',
                'dan',
                'chi',
                'por',
                'und',
                'ger',
                'kor',
                'rus',
                'hin',
                'ita',
            ], [
                'FR',
                'FR',
                'FR',
                'EN',
                'EN',
                'ES',
                'JP',
                'JP',
                'RS',
                'NL',
                'NL',
                'NL',
                'CS',
                'DA',
                'ZH',
                'PT',
                'FR',
                'DE',
                'KO',
                'RU',
                'HI',
                'IT',
            ], $code);
        }

        return null;
    }

    protected function findFirstKeyIn(array $payload, array $keys, $default)
    {
        $value = null;

        foreach ($keys as $key) {
            if (array_key_exists($key, $payload)) {
                $value = trim($payload[$key]);
                break;
            }
        }

        return $value ?? $default;
    }

    protected function getVideoStream(): ?array
    {
        try {
            $streams = Collection::make($this->infos['streams']);

            $video = $streams->filter(function ($item) {
                return $item['codec_type'] === 'video';
            })->first();
        } catch (\Exception $e) {
            unset($e);
        }

        return $video;
    }

    protected function getAudioStreams(): Collection
    {
        try {
            $streams = Collection::make($this->infos['streams']);

            $audios = $streams->filter(function ($item) {
                return $item['codec_type'] === 'audio';
            })->values();
        } catch (\Exception $e) {
            unset($e);
        }

        return $audios;
    }

    protected function getSubtitleStreams(): Collection
    {
        try {
            $streams = Collection::make($this->infos['streams']);

            $subtitles = $streams->filter(function ($item) {
                return $item['codec_type'] === 'subtitle';
            })->values();
        } catch (\Exception $e) {
            unset($e);
        }

        return $subtitles;
    }
}
