<?php


namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPostsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check:posts-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktif edilmesi gereken postları aktif hale getirir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Başlangıç tarihi gelmiş yazıları aktif yap
        Post::where('start_date', '<=', $now)
            ->where('status', false)
            ->update(['status' => true]);

        // Bitiş tarihi geçmiş yazıları pasif yap, end_date null olanları dahil etme
        Post::where('end_date', '<=', $now)
            ->whereNotNull('end_date')
            ->where('status', true)
            ->update(['status' => false]);

        $this->info('Gönderi durumu başaraıyla güncellendi!');
    }
}

