<?php

namespace Weiaibaicai\OperationLog\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Weiaibaicai\OperationLog\Models\OperationLog;

class OperationLogClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:operation-log-clean  {--days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clean operation log';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        if (!is_numeric($days) && $days !== 'all')
            return Command::FAILURE;
        if ($days === 'all') {
            OperationLog::query()->truncate();
            $this->info("全部清空");
        } elseif ($days >= 1) {
            $days = Carbon::now()->addDays(-$days);
            $result = OperationLog::query()
                ->whereDate('created_at' , '<' ,$days)
                ->delete();
            $this->info("已删除: $result");
        }
        return Command::SUCCESS;
    }
}
