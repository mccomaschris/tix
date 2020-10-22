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

        while ( ( $data = fgetcsv($file)) !== FALSE)
        {
            if (trim($data[1]) !== 'CustPri' ) {

                $customer = trim($data[0]);
                $points = trim($data[1]);
                $points = str_replace(',', '', $points);
                $points = floatval($points);

                if ($points >= 5000) {
                    $weight = 10;
                } elseif ($points <= 4999 || $points >= 4000) {
                    $weight = 9;
                } elseif ($points <= 3999 || $points >= 3000) {
                    $weight = 8;
                } elseif ($points <= 2999 || $points >= 2500) {
                    $weight = 7;
                } elseif ($points <= 2499 || $points >= 2000) {
                    $weight = 6;
                } elseif ($points <= 1999 || $points >= 1500) {
                    $weight = 5;
                } elseif ($points <= 1499 || $points >= 1000) {
                    $weight = 4;
                } elseif ($points <= 999 || $points >= 500) {
                    $weight = 3;
                } elseif ($points <= 499 || $points >= 250) {
                    $weight = 2;
                } elseif ($points <= 249 || $points >= 0) {
                    $weight = 1;
                }

                for ($x = 0; $x <= $weight; $x++) {
                    Customer::create([
                        'customer_id' => $customer,
                        'points' => $points,
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

        foreach ($entries as $entry) {
            if (!in_array($entry->customer_id, $this->outputList)) {
                array_push($this->outputList, $entry->customer_id);
                $output .= "\"" . $entry->customer_id . "\",\"" . $entry->points . "\"\r\n";
            }
        }

        $this->reset();

        return response()->streamDownload(function () use ($output) {
            echo $output;
        }, 'output.csv');

    }

    public function render()
    {
        return view('livewire.ticket-list');
    }
}
