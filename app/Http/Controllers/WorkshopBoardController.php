<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBay;
use App\Models\JobCard;
use App\Models\User;

class WorkshopBoardController extends Controller
{
    public function index()
    {
        $serviceBays = ServiceBay::with(['activeJobCards.vehicle', 'activeJobCards.leadMechanic', 'activeJobCards.customer'])->get();

        $kanbanColumns = [
            'estimate_approved' => [
                'title' => 'Ready for Work',
                'color' => 'indigo',
                'jobs' => JobCard::where('status', 'estimate_approved')->with(['vehicle', 'customer', 'leadMechanic'])->get(),
            ],
            'work_in_progress' => [
                'title' => 'In Progress (On Bays)',
                'color' => 'blue',
                'jobs' => JobCard::where('status', 'work_in_progress')->with(['vehicle', 'customer', 'leadMechanic', 'serviceBay'])->get(),
            ],
            'quality_check' => [
                'title' => 'Quality Control (QC)',
                'color' => 'purple',
                'jobs' => JobCard::where('status', 'quality_check')->with(['vehicle', 'customer', 'leadMechanic'])->get(),
            ],
            'ready_for_delivery' => [
                'title' => 'Ready for Delivery',
                'color' => 'teal',
                'jobs' => JobCard::where('status', 'ready_for_delivery')->with(['vehicle', 'customer'])->get(),
            ],
        ];

        $mechanics = User::whereHas('role', function ($q) {
            $q->where('slug', 'mechanic');
        })->get();

        return view('workshop.board', compact('serviceBays', 'kanbanColumns', 'mechanics'));
    }
}
