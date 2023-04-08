<?php

namespace App\Console\Commands;

use App\Models\Session;
use App\Models\User;
use App\Notifications\Telegram\Delimeter;
use App\Notifications\Telegram\LongNoInfo;
use App\Notifications\Telegram\ZeroTables;
use Illuminate\Console\Command;

class TelegramProblematicMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:problematic-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sessionsWithProblems = Session::where('finish', 0)->with('account')->get()->filter(function ($session) {
            return $session->hasProblems();
        });
        if ($sessionsWithProblems->count() > 0) {
            $users = User::all();

            foreach ($users as $user) {
                foreach ($sessionsWithProblems as $session) {
                    if ($session->zeroTables()) {
                        try {
                            $user->notify(new ZeroTables($session->account->nickname));
                            continue;
                        } catch (\Exception $e) {
                            break;
                        }
                    }
                    if ($session->longNoInfo()) {
                        try {
                            $user->notify(new LongNoInfo($session->account->nickname));
                        } catch (\Exception $e) {
                            break;
                        }
                    }
                }
                try {
                    $user->notify(new Delimeter);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
}
