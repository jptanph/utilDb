<?
/**
 * UTF8 문자열에 대한 유효성 검사 및 복구
 *
 * @author      ulariul - jung
 * @package     util
 */

class utilUtf8 {

    /**
     * UTF8문자의 유효성 검사
     *
     * @param unknown_type $str
     * @param unknown_type $truncated
     * @return unknown
     */
    public static function validate($str, $truncated = false) {
        $length = strlen($str);
        if ($length == 0)
            return true;
        for ($i = 0; $i < $length; $i++) {
            $high = ord($str{$i});
            if ($high < 0x80) {
                continue;
            } else if ($high <= 0xC1) {
                return false;
            } else if ($high < 0xE0) {
                if (++$i >= $length)
                    return $truncated;
                else if (($str{$i} & "\xC0") == "\x80")
                    continue;
            } else if ($high < 0xF0) {
                if (++$i >= $length) {
                    return $truncated;
                } else if (($str{$i} & "\xC0") == "\x80") {
                        if (++$i >= $length)
                            return $truncated;
                        else if (($str{$i} & "\xC0") == "\x80")
                            continue;
                    }
            } else if ($high < 0xF5) {
                    if (++$i >= $length) {
                        return $truncated;
                    } else if (($str{$i} & "\xC0") == "\x80") {
                            if (++$i >= $length) {
                                return $truncated;
                            } else if (($str{$i} & "\xC0") == "\x80")  {
                                    if (++$i >= $length)
                                        return $truncated;
                                    else if (($str{$i} & "\xC0") == "\x80")
                                        continue;
                                }
                        }
                } // F5~FF is invalid by RFC 3629
            return false;
        }
        return true;
    }


    /**
     * UTF8을 전송할 경우 깨지는 문자열이 발생할 경우에 사용함
     *
     * @param unknown_type $str
     * @param unknown_type $broken
     * @return unknown
     *
     * @example
     *
     *   if (!utilUtf8::validate($this->params['code'])) {
     *       $this->params['code'] = utilUtf8::bring($this->params['code']);
     *   }
     *   $this->params['code']  = utilUtf8::correct($this->params['code']);
     */
    public static  function correct($str, $broken = '') {
        $corrected = '';
        $strlen = strlen($str);
        for ($i = 0; $i < $strlen; $i++) {
            switch ($str{$i}) {
                case "\x09":
                case "\x0A":
                case "\x0D":
                    $corrected .= $str{$i};
                    break;
                case "\x7F":
                    $corrected .= $broken;
                    break;
                default:
                    $high = ord($str{$i});
                    if ($high < 0x20) { // Special Characters.
                        $corrected .= $broken;
                    } else if ($high < 0x80) { // 1byte.
                        $corrected .= $str{$i};
                    } else if ($high <= 0xC1) {
                        $corrected .= $broken;
                    } else if ($high < 0xE0) { // 2byte.
                        if (($i + 1 >= $strlen) || (($str{$i + 1} & "\xC0") != "\x80"))
                            $corrected .= $broken;
                        else
                            $corrected .= $str{$i} . $str{$i + 1};
                        $i += 1;
                    } else if ($high < 0xF0) { // 3byte.
                        if (($i + 2 >= $strlen) || (($str{$i + 1} & "\xC0") != "\x80") || (($str{$i + 2} & "\xC0") != "\x80"))
                            $corrected .= $broken;
                        else
                            $corrected .= $str{$i} . $str{$i + 1} . $str{$i + 2};
                        $i += 2;
                    } else if ($high < 0xF5) { // 4byte.
                            if (($i + 3 >= $strlen) || (($str{$i + 1} & "\xC0") != "\x80") || (($str{$i + 2} & "\xC0") != "\x80") || (($str{$i + 3} & "\xC0") != "\x80"))
                                $corrected .= $broken;
                            else
                                $corrected .= $str{$i} . $str{$i + 1} . $str{$i + 2} . $str{$i + 3};
                            $i += 3;
                        } else { // F5~FF is invalid by RFC3629.
                            $corrected .= $broken;
                        }
                    break;
            }
        }
        if (preg_match('/&#([0-9]{1,});/', $corrected))
            $corrected = mb_decode_numericentity($corrected, array(0x0, 0x10000, 0, 0xfffff), 'UTF-8');
        return $corrected;
    }

    /**
     * UTF8문자열이 아닌경우 변환합니다.
     *
     * @param unknown_type $str
     * @param unknown_type $encoding
     * @return unknown
     */
    public static function bring($str, $encoding = null) {
        return @iconv((isset($encoding) ? $encoding : 'euc-kr'), 'UTF-8', $str);
    }


    public static function convert($str, $encoding = null) {
        return @iconv('UTF-8', (isset($encoding) ? $encoding : 'euc-kr'), $str);
    }


    public static  function length($str) {
        $len = strlen($str);
        for ($i = $length = 0; $i < $len; $length++) {
            $high = ord($str{$i});
            if ($high < 0x80)
                $i += 1;
            else if ($high < 0xE0)
                $i += 2;
            else if ($high < 0xF0)
                $i += 3;
            else
                $i += 4;
        }
        return $length;
    }


    public static  function lengthAsEm($str) {
        $len = strlen($str);
        for ($i = $length = 0; $i < $len; ) {
            $high = ord($str{$i});
            if ($high < 0x80) {
                $i += 1;
                $length += 1;
            } else {
                if ($high < 0xE0)
                    $i += 2;
                else if ($high < 0xF0)
                    $i += 3;
                else
                    $i += 4;
                $length += 2;
            }
        }
        return $length;
    }

    public static function lessenAsEncoding($str, $length = 255, $tail = '...') {
        global $database;
        if(!isset($database['utf8']) || empty($database['utf8']) || $database['utf8'] == true)
            return self::lessen($str, $length, $tail);
        else
            return self::lessenAsByte($str, $length, $tail);
    }


    public static function lessen($str, $chars, $tail = '...') {
        if (self::length($str) <= $chars)
            $tail = '';
        else
            $chars -= self::length($tail);
        $len = strlen($str);
        for ($i = $adapted = 0; $i < $len; $adapted = $i) {
            $high = ord($str{$i});
            if ($high < 0x80)
                $i += 1;
            else if ($high < 0xE0)
                $i += 2;
            else if ($high < 0xF0)
                $i += 3;
            else
                $i += 4;
            if (--$chars < 0)
                break;
        }
        return trim(substr($str, 0, $adapted)) . $tail;
    }


    public static function lessenAsByte($str, $bytes, $tail = '...') {
        if (strlen($str) <= $bytes)
            $tail = '';
        else
            $bytes -= strlen($tail);
        $len = strlen($str);
        for ($i = $adapted = 0; $i < $len; $adapted = $i) {
            $high = ord($str{$i});
            if ($high < 0x80)
                $i += 1;
            else if ($high < 0xE0)
                $i += 2;
            else if ($high < 0xF0)
                $i += 3;
            else
                $i += 4;
            if ($i > $bytes)
                break;
        }
        return substr($str, 0, $adapted) . $tail;
    }


    public static function lessenAsEm($str, $ems, $tail = '...') {
        if (self::lengthAsEm($str) <= $ems)
            $tail = '';
        else
            $ems -= strlen($tail);
        $len = strlen($str);
        for ($i = $adapted = 0; $i < $len; $adapted = $i) {
            $high = ord($str{$i});
            if ($high < 0x80) {
                $i += 1;
                $ems -= 1;
            } else {
                if ($high < 0xE0)
                    $i += 2;
                else if ($high < 0xF0)
                    $i += 3;
                else
                    $i += 4;
                $ems -= 2;
            }
            if ($ems < 0)
                break;
        }
        return trim(substr($str, 0, $adapted)) . $tail;
    }
}
?>