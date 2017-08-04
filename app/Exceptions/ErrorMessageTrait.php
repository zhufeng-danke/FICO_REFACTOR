<?php

trait ErrorMessageTrait
{
    /**
     * 统一，简单的显示错误信息的页面
     * 2501 comes form <GHOST IN THE SHELL>
     */
    protected function error($message, $err_code = 2501)
    {
        throw new ErrorMessageException($message, $err_code);
    }

    protected function assert($condition, $message = null)
    {
        if (!$condition) {
            $this->error($message ?: '无权操作');
        }
    }

    protected function assertNot($condition, $message = null)
    {
        $this->assert(!$condition, $message);
    }
}
