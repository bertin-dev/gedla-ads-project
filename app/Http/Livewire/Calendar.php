<?php

namespace App\Http\Livewire;
use Illuminate\Support\Arr;
use Livewire\Component;
use App\Models\Event;

class Calendar extends Component
{
    public $events = [];
    public function render()
    {
        $this->events = json_encode(Event::all());
        return view('livewire.calendar');
    }

    public function eventChange($event)
    {
        $e = Event::find($event['id']);
        $e->start = $event['start'];
        if(Arr::exists($event, 'end')) {
            $e->end = $event['end'];
        }
        $e->save();
    }

    public function eventAdd($event)
    {
        Event::create($event);
    }

    public function eventRemove($id)
    {
        Event::destroy($id);
    }
}
