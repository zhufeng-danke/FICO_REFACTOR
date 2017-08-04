<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BICache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bi:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dispose bi related caches';

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
        $city = \Xiaoqu::listCity();

        if (!count($city)){
            return false;
        }

        foreach ($city as $c){
            $xq = \Xiaoqu::where('city', $c)->where('name', 'not like',
                '%废%')->pluck('name', 'id')->toArray();
            if (!count($xq)){
                dump(date('Y-m-d H:i:s').'，缓存小区列表：'.$c.', 暂无小区');
                continue;
            }
            $data = [];
            foreach ($xq as $k => $v) {
                $xq = \Xiaoqu::find($k);
                $v = $v . ' - ' . ($xq->block ?? '-');

                $data[] = [
                    'k' => $k,
                    'v' => $v
                ];
            }

            $key = $c . '_小区列表';
            if(Cache::has($key)){
                Cache::forget($key);
            }

//            Cache::forever($c . '_小区列表', $data);
            Cache::put($key, $data, 24 * 60);
            dump(date('Y-m-d H:i:s').'，缓存：'.$key);
        }

    }
}
