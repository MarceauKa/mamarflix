<?php

namespace App\Ffprobe;

use App\Movie;
use App\Utils\Cache;
use App\Utils\Env;
use Illuminate\Support\Collection;

class Ffprobe
{
    public ?Movie $movie;
    public ?Collection $streams;
    public ?Collection $format;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
        $this->getFileInfos();
    }

    public function getSize(): ?string
    {
        $size = $this->format->get('size');

        if (empty($size)) {
            return null;
        }

        $sz = 'BKMGTP';
        $factor = floor((strlen($size) - 1) / 3);

        return sprintf("%.2f", $size / pow(1024, $factor)) . @$sz[$factor];
    }

    public function getDuration(): ?string
    {
        $duration = (int)$this->format->get('duration');

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
            $title = $this->findFirstKeyIn(
                $audio['tags'] ?? [],
                ['title', 'name'],
                null
            );

            $lang = $this->findFirstKeyIn(
                $audio['tags'] ?? [],
                ['lang', 'language', 'LANG', 'LANGUAGE'],
                'N.C'
            );

            $lang = $this->replaceLangCode($lang);

            if ($title) {
                return sprintf('%s - %s', $lang, trim($title));
            }

            return $lang;
        })->values()->toArray();
    }

    public function getSubtitleTracks(): array
    {
        return $this->getSubtitleStreams()->transform(function ($sub) {
            $title = $this->findFirstKeyIn(
                $sub['tags'] ?? [],
                ['title', 'name'],
                null
            );

            $lang = $this->findFirstKeyIn(
                $sub['tags'] ?? [],
                ['lang', 'language', 'LANG', 'LANGUAGE'],
                'N.C'
            );

            $lang = $this->replaceLangCode($lang);

            if ($title) {
                return sprintf('%s - %s', $lang, trim($title));
            }

            return $lang;
        })->values()->toArray();
    }

    public function toArray(): array
    {
        return [
            'size' => $this->getSize(),
            'duration' => $this->getDuration(),
            'format' => $this->getVideoFormat(),
            'hdr' => $this->getVideoHasHdr(),
            'audio' => $this->getAudioTracks(),
            'subtitles' => $this->getSubtitleTracks(),
        ];
    }

    protected function getFileInfos(): self
    {
        $cacheKey = sprintf('ffprobe-%s.json', $this->movie->getSlug());

        if (Cache::has($cacheKey)) {
            $infos = Cache::get($cacheKey);
            $this->parseFileInfos($infos);

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
            $this->parseFileInfos($infos);
        } catch (\Exception $e) {
            throw new \RuntimeException("Can't decode JSON infos from {$this->movie->getPath()}");
        }

        return $this;
    }

    protected function parseFileInfos(string $infos): self
    {
        $infos = json_decode($infos, true);

        $this->format = Collection::make($infos['format'] ?? []);
        $this->streams = Collection::make($infos['streams'] ?? []);

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
            $video = $this->streams->filter(function ($item) {
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
            $audios = $this->streams->filter(function ($item) {
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
            $subtitles = $this->streams->filter(function ($item) {
                return $item['codec_type'] === 'subtitle';
            })->values();
        } catch (\Exception $e) {
            unset($e);
        }

        return $subtitles;
    }
}
