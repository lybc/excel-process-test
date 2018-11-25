<?php

namespace App\Console\Commands;

use App\Exports\TestExport;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExcelWriter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:writer {--drive=laravel-excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * 程序运行消耗的时间(s)
     * @var int
     */
    private $timeUsage = 0;

    /**
     * 程序运行消耗的内存(byte)
     * @var int
     */
    private $memoryUsage = 0;

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
     * @return mixed
     */
    public function handle()
    {
        $option = $this->option('drive');

        switch ($option) {
            case 'laravel-excel':
                $this->useLaravelExcelDrive();
                break;
            case 'spout':
                $this->useSpoutDrive();
                break;
            default:
                throw new \Exception('Invalid option ' . $option);
        }

        $this->info(sprintf('共耗时：%s秒', $this->timeUsage));
        $this->info(sprintf('共消耗内存: %sM', $this->memoryUsage / 1024 / 1024));
    }


    private function useSpoutDrive()
    {
        ini_set('memory_limit', -1);
        $start = now();
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile(storage_path('app/testWriter.xlsx'));
        $writer->addRows(generate_test_data());
        $writer->close();
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = xdebug_peak_memory_usage();
    }

    private function useLaravelExcelDrive()
    {
        ini_set('memory_limit', -1);
        $start = now();
        Excel::store(new TestExport(), 'testWriter.xlsx');
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = xdebug_peak_memory_usage();
    }
}
