<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Illuminate\Support\Facades\DB;
use App\Models\DomainTrafic;

class doScrap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'do:scrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'scrap website and get data from that';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Start Scrap Data...");
        $client = new HttpBrowser();

        $crawler = $client->request('GET', 'https://stat2.dlhost.top/login');
        $form = $crawler->selectButton('Login')->form();
        $crawler = $client->submit($form, array('email' => 'Sorin2@gmail.com', 'password' => 'fVgP6YevuNHkLF'));

        $crawler = $client->request('GET', 'https://stat2.dlhost.top/domains');

        // push all td texts in $data array without key
        $data = [];
        $crawler->filter('tbody > tr')->each(function ($node) use (&$data) {
            $nodeData = [];
            $node->filter("td")->each(function($node2 , $index) use (&$nodeData) {
                array_push($nodeData , $node2->text());
            });
            array_push($data , $nodeData);
        });


        // in this part foreach on $data and set key name for each of items
        $final_array = [];
        foreach ($data as $key => $value) {
            $local_array = [];
            $keys_array = array("id" , "Server", "User", "Name", "Size", "TodayBW", "YesterdayBW", "MonthBW","test");
            for ($i = 0; $i < count($keys_array); $i++) {
                $local_array[$keys_array[$i]] = $value[$i];
            }
            array_push($final_array , $local_array);
        }


        // in this part dicrease -40% from todayBw item each of record
        foreach ($final_array as $key => $final_data) {
            $fourtyPercent = (float)((float)explode(" ",$final_data["TodayBW"])[0] * 40) / 100;
            $final_data["TodayBwDicresed"] = (string)((float)explode(" ",$final_data["TodayBW"])[0] - $fourtyPercent)." "."TB";
            $final_data["g_created_at"] = (string)verta()->formatGregorian('Y-n-j');
            $final_data["j_created_at"] = (string)verta()->formatDate();
            $final_array[$key] = $final_data;
        }

        if(count(DomainTrafic::all()) == 0){
            // if not exist trafic recorde in table
            foreach ($final_array as $value) {
                DomainTrafic::create($value);
            }
        }else {
            foreach (DomainTrafic::all() as $trafic) {
                // if exist record in table and check which records g-created-at field match with today date
                if(verta($trafic->g_created_at)->diffDays() == 0){
                    $trafic->delete();
                }
            }

            foreach ($final_array as $value) {
                DomainTrafic::create($value);
            }
        }

        $this->info("Ended Scrap Data.");
    }
}
