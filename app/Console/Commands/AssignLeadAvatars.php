<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class AssignLeadAvatars extends Command
{
    protected $signature = 'leads:assign-avatars';
    protected $description = 'Assign random avatars to all leads that do not have one';

    public function handle()
    {
        $this->info('Starting avatar assignment for leads...');
        
        // Get all leads without avatars
        $leads = Lead::whereNull('avatar')->get();
        $total = $leads->count();
        
        if ($total === 0) {
            $this->info('No leads found without avatars.');
            return;
        }
        
        $this->info("Found {$total} leads without avatars. Assigning now...");
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        foreach ($leads as $lead) {
            // Get available avatars
            $avatars = [
                'male-1.jpg',
                'male-2.jpg',
                'male-3.jpg',
                'male-4.jpg',
                'male-5.jpg',
                'male-6.jpg',
                'male-7.jpg',
                'male-8.jpg',
                'male-9.jpg',
                'female-1.jpg',
                'female-2.jpg',
                'female-3.jpg',
            ];
            
            // Assign random avatar
            $lead->avatar = $avatars[array_rand($avatars)];
            $lead->save();
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Avatar assignment completed!');
    }
}