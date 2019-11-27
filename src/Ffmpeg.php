<?php

namespace App;

class Ffmpeg
{
    /** @var string|null $infos */
    public $infos;
    /** @var string $size */
    public $size;
    /** @var string $resolution */
    public $resolution;
    /** @var string $duration */
    public $duration;
    /** @var string $audio */
    public $audio;
    /** @var string $subs */
    public $subs;
    /** @var bool $hdr */
    public $hdr = false;

    public function __construct(Movie $movie)
    {
        $key = $movie->name . '_ffmpeg';

        if (Cache::has($key)) {
            $this->infos = Cache::get($key);
        } else {
            $command = Env::get('FFMPEG_BIN') . ' -i ' . escapeshellarg($movie->path) . ' -vstats -hide_banner 2>&1';
            $this->infos = shell_exec($command);
            Cache::set($key, $this->infos);
        }

        if ($this->infos) {
            $this->size = $this->parseSize(filesize($movie->path));
            $this->readVideo();
            $this->readAudio();
            $this->readSubtitles();
            $this->readDuration();
        }
    }

    protected function parseSize($size): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($size) - 1) / 3);
        return sprintf("%.2f", $size / pow(1024, $factor)) . @$sz[$factor];
    }

    protected function readVideo(): void
    {
        preg_match("/Video: ([^\r\n]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/iu", $this->infos, $regs);
        $codec = $regs[1] ?? null;
        $width = $regs[3] ?? null;
        $height = $regs[4] ?? null;

        $this->codec = $codec ?? null;

        if (stripos($this->infos, 'bt2020') !== false) {
            $this->hdr = true;
        }

        if ($width >= 3000) {
            $this->uhd = true;
            $this->resolution = '4k';
        } else if ($width >= 1900) {
            $this->resolution = 'Full HD';
        } else if ($width >= 700) {
            $this->resolution = 'HD';
        } else {
            $this->resolution = $width && $height ? sprintf('%dx%d', $width, $height) : null;
        }
    }

    protected function readAudio(): void
    {
        preg_match_all("/\#\d\:\d\(([a-z0-9]+)\)?\:\sAudio/iu", $this->infos, $regs);

        if (count($regs) > 0) {
            $audio = [];

            foreach ($regs[1] as $lang) {
                $lang = $this->replaceCode($lang);
                if (!in_array($lang, $audio)) {
                    $audio[] = $lang;
                }
            }

            $this->audio = $audio ? implode(',', $audio) : 'FR';
        }
    }

    protected function readSubtitles(): void
    {
        preg_match_all("/\#\d\:\d\(([a-z0-9]+)\)?\:\sSubtitle/iu", $this->infos, $regs);

        if (count($regs) > 0) {
            $subs = [];

            foreach ($regs[1] as $lang) {
                $lang = $this->replaceCode($lang);

                if (!in_array($lang, $subs)) {
                    $subs[] = $lang;
                }
            }

            $this->subs = $subs ? implode(',', $subs) : '-';
        }
    }

    protected function readDuration(): void
    {
        preg_match("/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/iu", $this->infos, $regs);
        $hours = $regs[1] ?? null;
        $mins = $regs[2] ?? null;

        $this->duration = $hours && $mins ? sprintf('%dh%d', $hours, $mins) : null;
    }

    protected function replaceCode($code): ?string
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
}
