<?php

namespace App\Jobs;

use App\Modules\Bbs\Jobs\SendRegisterEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExceptionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $time;
    public $http_status;
    public $exception;
    public $message;
    public $file;
    public $line;
    public $trace;
    public $full_url;
    public $params;
    public $header;
    public $request_ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($full_url, $time, $http_status, $message, $exceptionName, $file, $line, $trace, $params, $header, $request_ip)
    {
        $this->full_url = $full_url;
        $this->time = $time;
        $this->http_status = $http_status;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
        $this->exception = $exceptionName;
        $this->params = $params;
        $this->header = $header;
        $this->request_ip = $request_ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // if (config('app.env') == 'local') return;
        $title = $this->getEnvMessage() . ' ' . $this->message . '（' . date('Y-m-d H:i:s', $this->time) . '）';
        $bodyFile = $this->file;
        $bodyLine = $this->line;
        $bodyTrace = $this->trace;

        $params = my_json_encode($this->params);
        $header = my_json_encode($this->header);

        $type = $this->exception;

        $body =
            '- Route Url: ' . $this->full_url . '
- Request IP: ' . $this->request_ip . '

- Http Status: ' . $this->http_status . '

- Exception Type: ' . $type . '

- File: ' . $bodyFile . '

- Line: ' . $bodyLine . '

- Header: ' . $header . '

- Params: ' . $params . '

- Trace Detail: ' . json_encode($bodyTrace);

        // 发送给邮件服务
        try {
            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new \App\Mail\SendExceptionEmail($title, $body));
        } catch (\Exception $exception) {
            Log::info('报警邮件发送失败');
            Log::info($exception->getMessage());
        }

    }

    public function getEnvMessage()
    {
        $name = config('app.name');
        switch (config('app.env')) {
            case 'local':
                return $name . ' - 本地环境 ';
            case 'dev':
                return $name . ' - 开发环境 ';
            case 'test':
                return $name . ' - 测试环境 ';
            case 'production':
                return $name . ' - 生产环境 ';
            default:
                return $name . ' - 未知环境: ' . config('app.env');
        }
    }
}
