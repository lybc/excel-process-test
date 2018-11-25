<?php

namespace App\Console\Commands;

use App\Imports\TestImport;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Settings;

class ExcelReader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:reader {path} {--drive=laravel-excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * 测试文件的行数
     * @var int
     */
    private $rows = 0;

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
     * @throws \Exception
     */
    public function handle()
    {
        $path = $this->argument('path');
        $option = $this->option('drive');

        switch ($option) {
            case 'laravel-excel':
                $this->useLaravelExcelDrive($path);
                break;
            case 'spout':
                $this->useSpoutDrive($path);
                break;
            default:
                throw new \Exception('Invalid option ' . $option);
        }

        $this->info(sprintf('共读取数据：%s 行', $this->rows));
        $this->info(sprintf('共耗时：%s秒', $this->timeUsage));
        $this->info(sprintf('共消耗内存: %sM', $this->memoryUsage / 1024 / 1024));
    }

    private function useSpoutDrive($path)
    {
        ini_set('memory_limit', -1);
        $start = now();
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->setShouldFormatDates(true);
        $reader->open(storage_path('app/'.$path));
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $this->rows++;
                $rows[] = $row;
            }
        }
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = xdebug_peak_memory_usage();
    }

    private function useLaravelExcelDrive($path)
    {
        $start = now();
        Settings::setLibXmlLoaderOptions(LIBXML_COMPACT | LIBXML_PARSEHUGE);
        ini_set('memory_limit', -1);
        $array = Excel::toArray(new TestImport(), $path);
        $this->rows = count($array[0]);
        $this->timeUsage = now()->diffInSeconds($start);
        $this->memoryUsage = xdebug_peak_memory_usage();
    }
}
