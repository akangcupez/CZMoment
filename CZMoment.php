<?php
/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 4/1/2016 10:21 AM
 */
class CZMoment
{
    private $subvars = array
    (
        'Y' => 31104000,
        'm' => 2592000,
        'd' => 86400,
        'H' => 3600,
        'i' => 60,
        's' => 1
    );

    private $locale_list;
    private $locale;
    private $lang;
    private $file;
    private $list   = null;

    function __construct($locale = 'en')
    {
        require_once('locales' . DIRECTORY_SEPARATOR .'locale_list.php');

        /** @var array $code */
        $this->locale_list = $code;
        $this->locale = (!(is_null($locale) || empty($locale))) ? $locale : 'en';
        $this->SetLanguage($this->locale);
    }

    /**
     * <h4>Set Locales</h4>
     *
     * @param $locale
     *
     * @return $this
     */
    public function SetLocale($locale)
    {
        $this->locale = $locale;
        $this->SetLanguage($this->locale);

        return $this;
    }

    private function SetLanguage($locale)
    {
        $this->lang = $this->GetLanguage($locale, $this->locale_list);
    }

    private function GetLanguage($locale, $lang)
    {
        $local = trim(strtolower($locale));
        foreach($lang as $key => $lng)
        {
            if(in_array($local, $lng)) return $key;
        }
        return null;
    }

    private function LoadLanguage()
    {
        if(!(is_null($this->lang) || empty($this->lang)))
        {
            $this->file  = 'locales';
            $this->file .= DIRECTORY_SEPARATOR;
            $this->file .= $this->lang . '.php';

            if(file_exists($this->file))
            {
                /** @noinspection PhpIncludeInspection */
                require_once($this->file);

                /** @var array $config */
                $this->list = $config;
                return true;
            }
        }
        return false;
    }

    private function DateFix($input)
    {
        $dt = explode(' ', trim($input));
        if(count($dt) === 1)
        {
            $date = preg_replace('/[^0-9]/', '', $dt[0]);

            $len = strlen($date);
            if($len <= 6)
            {
                $y = substr($date, 0, 4);
                $x = ($len === 6) ? -2 : -1;
                $m = intval(substr($date, $x));
                $m = ($m > 0 && $m < 10) ? str_pad($m, 2, '0', STR_PAD_LEFT) : $m;

                return "{$y}-{$m}-01 00:00:00";
            }
        }

        return $input;
    }

    //TODO: count datetime with negative values for future timespan
    public function FromNow($timestamp)
    {
        $this->LoadLanguage();

        $frm = $this->DateFix($timestamp);
        $est = time() - strtotime(date_format(date_create($frm), 'Y-m-d H:i:s'));
        if($est === 1) return $this->list['1-s'];

        foreach($this->subvars as $skey => $secs)
        {
            $sec = $est / $secs;
            if($sec >= 1)
            {
                $sec = round($sec);
                if($sec == 1)
                {
                    foreach($this->list as $key => $val)
                    {
                        if($key === "1-{$skey}") return $val;
                    }
                }
                else
                {
                    foreach($this->list as $key => $val)
                    {
                        if($key === "(:num)-{$skey}")
                        {
                            $is_week = ($skey == 'd' && ($sec > 7 && $sec < 28)) ? true : false;
                            $num = ($is_week === true) ? (round($sec / 7)) : $sec;
                            $idx = ($is_week === true) ? (($num > 1) ? '(:num)-W' : '1-W') : $key;

                            $interval = $this->list[$idx];

                            return preg_replace('/(\$1){1}/', $num, $interval);
                        }
                    }
                }
            }
        }

        //not implemented yet
        return null;
    }
}
