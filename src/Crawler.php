<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class Crawler
{
    /** @var string $searchUrl */
    protected static $searchUrl = 'https://www.senscritique.com/search?categories[0][0]=Films&q=';
    /** @var DomCrawler $crawler */
    protected $crawler;
    /** @var string $url */
    public $url;
    /** @var string $poster */
    public $poster;
    /** @var string $director */
    public $director;
    /** @var string $actors */
    public $actors;
    /** @var string $genre */
    public $genre;
    /** @var string $note */
    public $note;

    public function __construct(Movie $movie)
    {
        $this->getMoviePage($movie);

        if ($this->crawler
            && $this->crawler->count() > 0) {
            $this->grep();
        }
    }

    protected function getMoviePage(Movie $movie)
    {
        $this->url = $this->getMoviePageUrl($movie);

        if (empty($this->url)) {
            return;
        }

        if (Cache::has($this->url)) {
            $content = Cache::get($this->url);
        } else {
            $content = $this->fetch($this->url);
            Cache::set($this->url, $content);
        }

        $this->crawler = new DomCrawler($content);
    }

    protected function getMoviePageUrl(Movie $movie): ?string
    {
        $key = $movie->name . '_page_url';

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $url = self::$searchUrl . urlencode($movie->name);
        $crawler = new DomCrawler($this->fetch($url));

        if ($crawler && $crawler->count() > 0) {
            try {
                $link = $crawler->filter('div.ProductListItem__Container-s1ci68b-0:nth-child(1) > div:nth-child(2) > div:nth-child(1) > a:nth-child(1)')->attr('href');
            } catch (\Exception $e) {
                unset($e);
                throw new \RuntimeException("Unable to get movie page url " . $movie->name);
            }

            return $link;
        }

        return null;
    }

    protected function fetch(string $url): string
    {
        try {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Accept: text/*; application/*',
            ]);

            $content = curl_exec($curl);

            curl_close($curl);
        } catch (\Exception $e) {
            unset($e);
        }

        return $content ?? '';
    }

    protected function grep(): void
    {
        try {
            $this->poster = $this->crawler->filter('.pvi-hero-poster')->attr('src');
        } catch (\Exception $e) {
            unset($e);
            throw new \RuntimeException("Unable to grep poster of " . $this->url);
        }

        $this->grepTextValue('genre', 'li.pvi-productDetails-item:nth-child(2)');
        $this->grepTextValue('director', 'li.pvi-productDetails-item:nth-child(1) > span:nth-child(2) > a:nth-child(1) > span:nth-child(1)');
        $this->grepTextValue('actors', '.pvi-productDetails-workers');
        $this->grepTextValue('note', '.pvi-scrating-value');
    }

    protected function grepTextValue(string $value, string $selector): void
    {
        try {
            $this->$value = $this->crawler->filter($selector)->text();
            $this->$value = str_replace(["\n", "\t", "Avec"], '', $this->$value);
        } catch (\Exception $e) {
            unset($e);
            throw new \RuntimeException("Unable to grep text value {$value} of {$this->url}");
        }
    }
}
