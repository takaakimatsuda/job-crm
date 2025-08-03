<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ChatGptService;
use Illuminate\Support\Facades\Log;

class GptTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:gpt-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ChatGPTを使って履歴メモを要約・アドバイス出力するテスト';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gpt = new ChatGptService();

        $memo = '候補者と30分間面談。職務経験と希望条件のヒアリングを実施。';

        $this->info('🔍 ChatGPTによる要約を実行中...');
        $summary = $gpt->summarize($memo);

        $this->line('');
        $this->line('--- 🔎 要約結果 ---');
        $this->line($summary);
        $this->line('-------------------');

        Log::info('GptTestコマンドによる要約結果', ['summary' => $summary]);

        return Command::SUCCESS;
    }
}
