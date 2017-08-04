<?php
//yubing@wutongwan.org

/**
 * 增强版Schedule,用以替代Laravel自带版本的schedule:run命令
 *
 * 特性:
 * 0. 多进程版本,提高整体容错能力 (原Schedule为单线程版本)
 * 1. 容错性更高的WithoutOverlapping方式 (原Event::withoutOverlapping()实现的有问题)
 * 2. 更实用的信息输出(哪些函数跑了有Log输出,方便后期排查)
 *
 * $event->debug_string 是刻意生造出来用于最终执行情况的.
 */

use \Illuminate\Console\Scheduling\CallbackEvent;
use \Illuminate\Console\Scheduling\Event;

class SchedulePlus
{

    //一堆Event对象实例
    private $events = [];

    /**
     * @param string $unique_name 需要加锁运行的Job放这里
     * @param callable $callback
     * @return CallbackEvent
     */
    public function addUniqueJob(string $unique_name, callable $callback)
    {
        $func = function () use ($unique_name, $callback) {
            $l = new Locker(__CLASS__ . __FILE__ . $unique_name);
            $is_locked = $l->lock();
            if ($is_locked) {
                call_user_func($callback);
                $l->unlock();
            }
        };

        $event = $this->addJob($func);
        $event->debug_string = $this->closureSourceCode($callback);
        return $event;
    }

    /**
     * @param callable $callback
     * @return CallbackEvent
     */
    public function addJob(callable $callback)
    {
        $this->events[] = $event = new CallbackEvent($callback);

        $event->debug_string = $this->closureSourceCode($callback);
        return $event;

    }

    /**
     * @param $command
     * @return Event
     */
    public function cmd($command)
    {
        $this->events[] = $event = new Event($command);
        $event->debug_string = $command;

        return $event;
    }

    private $workers = [];

    public function run($app)
    {
        $list = array_filter($this->events, function ($event) use ($app) {
            /* @var Event $event */
            return $event->isDue($app) && $event->filtersPass($app);
        });

        foreach ($list as $event) {
            /* @var Event $event */
            $pid = pcntl_fork();
            $this->workers[$pid] = true;
            if ($pid === 0) { //子进程进入了这个岔路口，父进程直接执行if后面的代码
                $pid = posix_getpid(); //实际的进程ID
                $this->log("$pid start, job:\n ###\n" . $event->debug_string . "\n ###");
                $event->run($app);
                exit; //子进程必须退出，否则还会继续执行if后面的代码
            }
            usleep(200000);
        }

        $this->log(__CLASS__ . " called! Total workers: " . count($this->workers));

        while (count($this->workers)) {
            $pid = pcntl_wait($status); //父进程中可以拿到子进程的ID
            unset($this->workers[$pid]);
            $this->log("{$pid} finish, current workers: " . count($this->workers));
            usleep(50000);
        }
    }

    private function log($msg)
    {
        echo(date('Y-m-d H:i:s ') . $msg . PHP_EOL);
    }

    private function closureSourceCode(Closure $c)
    {
        $str = 'function (';
        $r = new ReflectionFunction($c);
        $params = array();
        foreach ($r->getParameters() as $p) {
            $s = '';
            if ($p->isArray()) {
                $s .= 'array ';
            } else {
                if ($p->getClass()) {
                    $s .= $p->getClass()->name . ' ';
                }
            }
            if ($p->isPassedByReference()) {
                $s .= '&';
            }
            $s .= '$' . $p->name;
            if ($p->isOptional()) {
                $s .= ' = ' . var_export($p->getDefaultValue(), true);
            }
            $params [] = $s;
        }
        $str .= implode(', ', $params);
        $str .= '){' . PHP_EOL;
        $lines = file($r->getFileName());
        for ($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
            $str .= $lines[$l];
        }

        return $str;
    }

}