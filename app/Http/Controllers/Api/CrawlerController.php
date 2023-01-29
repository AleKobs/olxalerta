<?php

namespace App\Http\Controllers\Api;
use Symfony\Component\DomCrawler\Crawler;
use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Url;
use Illuminate\Http\Request;
use GuzzleHttp\Client;


class CrawlerController extends Controller
{
    //

    public function index() {
        $url = 'https://pb.olx.com.br/paraiba/joao-pessoa?q=notebook';
        $ads = $this->crawSpecficUrl($url);

        foreach($ads as $ad) {
            $this->saveAdvertise($ad, 1);
        }

    }

    public function start() {

        $url = Url::where('last_visited_at', '<', strtotime('-5 minutes'))->orderBy('last_visited_at', 'asc')->first();
        if (!$url) { return 'Nenhuma URL cadastrada'; }

        $ads = $this->crawSpecficUrl($url->url);




        foreach($ads as $ad) {
            if ($url->last_visited_at == 0) {
                $this->saveAdvertise($ad, $url->id, true);
            } else {
                $this->saveAdvertise($ad, $url->id);
            }
        }
        $url->last_visited_at = time();
        $url->save();

        /**
         *
         * tabela: urls
         * - id
         * - url
         * - expires_at: now() + 60 dias
         * - user_id
         *
         *
         * tabela users:
         * - id
         * - nome
         *
         *
         */

        return [
            'payload' => ['url' => $url, 'ads' => $ads]
        ];
        return ['sucess' => true];
    }

    private function saveAdvertise($ad, $url_id, $alert_date = null) {

        if (Advertise::where('url_id', $url_id)->where('url', $ad['url'])->count() > 0) {
            return false;
        }
        $ad['url_id'] = $url_id;
        if ($alert_date) {
            $ad['alert_date'] = date('Y-m-d H:i:s', time());
        }
        return Advertise::create($ad);
    }

    private function crawSpecficUrl($url) {

        $client = new Client();

        $contents = $client->request('GET',$url)->getBody()->getContents();

        // $contents = file_get_contents($url);
        $dataToReturn = [];
        $spider = new \Symfony\Component\DomCrawler\Crawler($contents);
        $spider = $spider->filter("a")->each(function($node) use (&$dataToReturn) {

                if($node->children()->filter('h2')->count() == 0) { return false; }

                $annouceData = [];
                $annouceData['url'] = $node->attr('href');
                // Pegar o titulo
                $node->children()->filter('h2')->each(function($node2) use (&$annouceData) {
                    $annouceData['title'] = $node2->text();
                });
                $node->children()->filter('span')->each(function($spans) use (&$annouceData) {
                    if (str_starts_with($spans->text(),'R$')) {
                        $annouceData['price_str'] = $spans->text();
                        $annouceData['price'] = preg_replace('/[^0-9]/', '', $spans->text());
                    } else if (str_starts_with($spans->text(),'Hoje, ')) {
                        $annouceData['published_at'] = str_replace('Hoje, ', date('Y-m-d').' ', $spans->text().':00');
                     }
                });
                $node->children()->filter('img')->each(function($imgs) use (&$annouceData) {
                    $annouceData['image'] = $imgs->attr('src');
                });

                // Verify if price is empty.
                if (!empty($annouceData['price'])) {
                    $dataToReturn[] = $annouceData;
                }







        });

        // [=""]
        return $dataToReturn;
    }
}
