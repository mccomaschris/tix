<?php

namespace App\Http\Livewire;

use App\Csv;
use App\Models\Customer;
use Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketList extends Component
{

    use WithFileUploads;

    public $upload;
    public $uploaded = false;
    public $download;
    public $exclude_ids;
    public $importList = [];
    public $outputList = [];

    public function updatedUpload()
    {
        $this->validate([
            'upload' => 'required|mimes:txt,csv', // 1MB Max
        ]);

        $this->columns = Csv::from($this->upload)->columns();
    }

    public function import()
    {
        Customer::truncate();

        $file = fopen($this->upload->getRealPath(), "r");
        $rank = 0;

        while ( ( $data = fgetcsv($file)) !== FALSE)
        {
            if (trim($data[0]) !== 'Donor' ) {

                $balls = 0;
                $rank++;

                $customer = trim($data[0]);
                $name = trim($data[1]);
                $phone = trim($data[2]);
                $email = trim($data[3]);

                $points = trim($data[4]);
                $points = str_replace(',', '', $points);
                $points = floatval($points);

                $af20 = trim($data[5]);
                $haf = trim($data[6]);
                $tipoff = trim($data[7]);
                $rises = trim($data[8]);

                // Calculate Priority Point Balls
                // $ppBalls
                if ($points <= 25) {
                    $ppBalls = 1;
                // } else if ($points >= 20000) {
                //     $ppBalls = 225;
                } else {
                    $ppBalls = round($points) / 25;
                }

                // Calculate Annual Fund 2020 Balls
                // $afBalls
                if ($af20 >= 60 && $af20 <= 299) {
                    $afBalls = 1;
                } else if ($af20 >= 300 && $af20 <= 999) {
                    $afBalls = 2;
                } else if ($af20 >= 1000 && $af20 <= 1499) {
                    $afBalls = 5;
                } else if ($af20 >= 1500 && $af20 <= 4999) {
                    $afBalls = 25;
                } else if ($af20 >= 5000 && $af20 <= 9999) {
                    $afBalls = 50;
                } else if ($af20 >= 10000) {
                    $afBalls = 75;
                } else {
                    $afBalls = 0;
                }

                // Calculate HAF Balls
                // $hafBalls
                if ($haf >= 1 && $haf <= 2500) {
                    $hafBalls = 10;
                } else if ($haf >= 2501 && $haf <= 5000) {
                    $hafBalls = 20;
                } else if ($haf >= 5001) {
                    $hafBalls = 30;
                } else {
                    $hafBalls = 0;
                }

                // Calculate Herd Rises Balls
                // $risesBalls
                // $1,000 - $5,000 – 5 entries
                // $5,001 - $10,000 – 10 entries
                // $10,001 - $25,000 – 20 entries
                // $25,001 - $99,999 – 30 entries
                // $100,000 + - 50 entries
                if ($rises >= 1000 && $rises <= 5000) {
                    $risesBalls = 5;
                } else if ($rises >= 5001 && $rises <= 10000) {
                    $risesBalls = 10;
                } else if ($rises >= 10001 && $rises <= 25000) {
                    $risesBalls = 20;
                } else if ($rises >= 25001 && $rises <= 99999) {
                    $risesBalls = 30;
                } else if ($rises >= 10000) {
                    $risesBalls = 50;
                } else {
                    $risesBalls = 0;
                }

                // Calculate Tip Off Club Balls
                // $tipoffBalls
                // $150 – 1 entry
                // $300 – 2 entries
                // $600 – 3 entries
                // $1,500 – 4 entries
                // $5,000 – 5 entries
                if ($tipoff >= 150 && $tipoff <= 299) {
                    $tipoffBalls = 1;
                } else if ($tipoff >= 300 && $tipoff <= 599) {
                    $tipoffBalls = 2;
                } else if ($tipoff >= 600 && $tipoff <= 1499) {
                    $tipoffBalls = 3;
                } else if ($tipoff >= 1500 && $tipoff <= 4999) {
                    $tipoffBalls = 5;
                } else if ($tipoff >= 5000) {
                    $tipoffBalls = 10;
                } else {
                    $tipoffBalls = 0;
                }

                // $ppBalls + $afBalls + $hafBalls + tipoffBalls;
                $balls = $ppBalls + $afBalls + $hafBalls + $tipoffBalls + $risesBalls;

                for ($x = 0; $x <= $balls; $x++) {
                    Customer::create([
                        'customer_id' => $customer,
                        'name' => $name,
                        'phone' => $phone,
                        'email' => $email,
                        'points' => $points,
                        'annual_fund' => $af20 ? $af20 : 0,
                        'herd_athletic_fund' => $haf ? $haf : 0,
                        'herd_rises' => $rises ? $rises : 0,
                        'tip_off_club' => $tipoff ? $tipoff : 0,
                        'balls' => $balls,
                        'rank' => $rank
                    ]);
                }
            }

        }

        $this->uploaded = true;

    }

    public function resetAll()
    {
        $this->reset();
    }

    public function exportSelected()
    {
        $entries = Customer::inRandomOrder()->get();
        $output = "";

        $output = "Donor,Name,C Phone,E Phone,Prior,Drv 20 Commit,HAF 19 & 20,RB19 TO usage commit,Usage beginning with HR Commit all drives,Entries\r\n";

        foreach ($entries as $entry) {
            if (!in_array($entry->customer_id, $this->outputList)) {
                array_push($this->outputList, $entry->customer_id);
                $output .= "\"" . $entry->customer_id . "\",\"" . $entry->name . "\",\"" . $entry->phone . "\",\"" . $entry->email . "\",\"" . $entry->points . "\",\"" . $entry->annual_fund . "\",\"" . $entry->herd_athletic_fund . "\",\"" . $entry->tip_off_club . "\",\"" . $entry->herd_rises . "\",\"" . $entry->balls . "\"\r\n";
                // $output .= "\"" . $entry->customer_id . "\",\"" . $entry->name . "\",\"" . $entry->phone . "\",\"" . $entry->email . "\",\"" . $entry->points . "\",\"" . $entry->annual_fund . "\",\"" . $entry->herd_athletic_fund . "\",\"" . $entry->tip_off_club . "\",\"" . $entry->herd_rises . "\"\r\n";
            }
        }

        $this->reset();

        Customer::truncate();

        return response()->streamDownload(function () use ($output) {
            echo $output;
        }, 'output.csv');

    }

    public function render()
    {
        return view('livewire.ticket-list');
    }
}
