<?php // zhangwei@wutongwan.org

use libphonenumber\PhoneNumberType;
use libphonenumber\NumberParseException;

class CustomValidator extends Illuminate\Validation\Validator
{
    /**
     * 验证是否是合法身份证号
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    protected function validateIdNumber($attribute, $value)
    {
        return (new IdCardInfo($value))->isValid();
    }

    /**
     * 验证号码是否是合法手机号
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    protected function validateMobile($attribute, $value, $parameters)
    {
        // 去掉字符串、数字验证
        $value = trim($value);
        if (!is_numeric($value)) {
            return false;
        }

        // 线下仅作简单验证, 方便测试, faker出的假数据还是很难过libphonenumber的校验
        if (!isProduction()) {
            return preg_match('/^1[3-8]\d{9}$/', $value);
        }

        $ccc = isset($parameters[0])
            ? $this->getValue($parameters[0])
            : Mobile::DEFAULT_CCC;

        $utils = app('libphonenumber');
        $region = $utils->getRegionCodeForCountryCode($ccc);

        try {
            $number = $utils->parse($value, $region);
            return PhoneNumberType::MOBILE === $utils->getNumberType($number);
        } catch (NumberParseException $e) {
            return false;
        }
    }

    /**
     * Validate the date is not before a given date.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    protected function validateNotBefore($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'not_before');

        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return !$this->validateBefore($attribute, $value, $parameters);
    }

    /**
     * Validate the date is not after a given date.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    protected function validateNotAfter($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'not_after');

        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return !$this->validateAfter($attribute, $value, $parameters);
    }

    protected function replaceNotAfter($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    protected function replaceNotBefore($message, $attribute, $rule, $parameters)
    {
        return $this->replaceBefore($message, $attribute, $rule, $parameters);
    }

    /**
     * Validate the string not contains given strings.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    protected function validateNotContains($attribute, $value, $parameters)
    {
        if (!$this->hasAttribute($attribute)) {
            return true;
        }

        $this->requireParameterCount(1, $parameters, 'not_contains');

        return !str_contains($value, $parameters);
    }

    /**
     * Validate the string contains any given strings.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    protected function validateContains($attribute, $value, $parameters)
    {
        if (!$this->hasAttribute($attribute)) {
            return true;
        }

        $this->requireParameterCount(1, $parameters, 'contains');

        return str_contains($value, $parameters);
    }

    /**
     * 替换 contains rule 错误信息中的 :contains 关键字
     */
    protected function replaceContains($message, $attribute, $rule, $parameters)
    {
        $parameters = collect($parameters)->map(function ($param) {
            switch (true) {
                case preg_match("/\t/", $param):
                    return '制表符(Tab)';

                case preg_match("/\r|\n/", $param):
                    return '换行符';

                case preg_match("/\ |\ /", $param):
                    return '空格符';

                default:
                    return $param;
            }
        });

        return str_replace(':contains', join(', ', $parameters->toArray()), $message);
    }

    /**
     * 替换 not_contains rule 错误信息中的 :contains 关键字
     */
    protected function replaceNotContains($message, $attribute, $rule, $parameters)
    {
        return $this->replaceContains($message, $attribute, $rule, $parameters);
    }
}