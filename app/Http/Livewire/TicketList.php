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
        $rank = 0;

        while ( ( $data = fgetcsv($file)) !== FALSE)
        {
            if (trim($data[1]) !== 'CustPri' ) {

                $rank++;
                $customer = trim($data[0]);
                $points = trim($data[1]);
                $points = str_replace(',', '', $points);
                $points = floatval($points);

                if ($points <= 100) {
                    $weight = 1;
                } else {
                    $weight = round($points) / 100;
                }

                for ($x = 0; $x <= $weight; $x++) {
                    Customer::create([
                        'customer_id' => $customer,
                        'points' => $points,
                        'rank' => $rank,
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
                $output .= "\"" . $entry->customer_id . "\",\"" . $entry->points . "\",\"" . $entry->rank . "\"\r\n";
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
